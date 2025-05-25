<?php

namespace App\Http\Controllers\Entrepreneur;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\TemplateCategory;
use App\Services\ImageService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificatesController extends Controller
{
    protected $imageService;
    protected $notificationService;
    
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct(ImageService $imageService, NotificationService $notificationService)
    {
        $this->imageService = $imageService;
        $this->notificationService = $notificationService;
        $this->middleware(['auth', 'role:predprinimatel,admin']);
    }

    /**
     * Отображение списка документов.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->certificates();
        
        // Фильтр по статусу
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Поиск по номеру или получателю
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%")
                  ->orWhere('recipient_phone', 'like', "%{$search}%");
            });
        }
        
        // Получение результатов с пагинацией и сохранение параметров запроса
        $certificates = $query->latest()->paginate(10)->withQueryString();
        
        // Дополнительная информация для статистики
        $activeCount = Auth::user()->certificates()->where('status', 'active')->count();
        $totalAmount = Auth::user()->certificates()->sum('amount');
        
        return view('entrepreneur.certificates.index', compact('certificates', 'activeCount', 'totalAmount'));
    }

    /**
     * Отображение формы выбора шаблона для создания документа.
     */
    public function selectTemplate(Request $request)
    {
        $query = CertificateTemplate::where('is_active', true);
        
        // Проверяем тариф пользователя для доступа к премиум шаблонам
        if (!Auth::user()->hasSubscriptionLevel('vip')) {
            $query->where('is_premium', false);
        }
        
        // Фильтрация по типу документа
        if ($request->has('type') && $request->type) {
            $query->where('document_type', $request->type);
        }
        
        // Фильтрация по категории
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        $templates = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        
        $categories = TemplateCategory::where('is_active', true)->orderBy('sort_order')->get();
        
        $documentTypes = TemplateCategory::getDocumentTypes();
        
        // Получаем информацию о текущем тарифном плане
        $currentPlan = Auth::user()->currentPlan();
        
        return view('entrepreneur.certificates.select-template', compact('templates', 'categories', 'documentTypes', 'currentPlan'));
    }

    /**
     * Показать форму для создания документа на основе выбранного шаблона.
     */
    public function create(CertificateTemplate $template)
    {
        // Проверка доступа к премиум-шаблонам
        if ($template->is_premium && !Auth::user()->hasSubscriptionLevel('vip')) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Для использования премиум-шаблонов необходимо обновить тарифный план');
        }
        
        return view('entrepreneur.certificates.create', compact('template'));
    }
    
    /**
     * Инициирует процесс создания сертификата с учетом тарифного плана пользователя.
     * Пользователи с тарифом start сразу перенаправляются на фоторедактор с базовым шаблоном.
     * Пользователи с более высокими тарифами перенаправляются на страницу выбора шаблона.
     */
    public function initiateCreate()
    {
        // Получаем текущий тарифный план пользователя
        $currentPlan = Auth::user()->currentPlan();
        
        // Если тариф Start - редирект на фоторедактор с базовым шаблоном
        if ($currentPlan && $currentPlan->slug === 'start') {
            // Получаем ID базового шаблона
            $templateId = $this->getDefaultTemplateId();
            
            if (!$templateId) {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Не найден шаблон для создания документа. Пожалуйста, обновите тарифный план.');
            }
            
            // Перенаправляем на страницу фоторедактора
            return redirect()->route('photo.editor', ['template' => $templateId]);
        }
        
        // Для других тарифов - редирект на страницу выбора шаблона
        return redirect()->route('entrepreneur.certificates.select-template');
    }
    
    /**
     * Получает ID шаблона по умолчанию для базового тарифа
     */
    protected function getDefaultTemplateId()
    {
        // Ищем классический шаблон сертификата
        $defaultTemplate = CertificateTemplate::where('is_active', true)
            ->where('is_premium', false)
            ->where('template_path', 'like', '%classic-certificate%')
            ->first();
        
        // Если не нашли специальный шаблон, проверяем по пути файла
        if (!$defaultTemplate) {
            $defaultTemplate = CertificateTemplate::where('is_active', true)
                ->where('is_premium', false)
                ->where('template_path', 'like', '%certificates/classic%')
                ->first();
        }
        
        // Если все еще не нашли, берем первый доступный неприемиум шаблон
        if (!$defaultTemplate) {
            $defaultTemplate = CertificateTemplate::where('is_active', true)
                ->where('is_premium', false)
                ->first();
        }
        
        // Если шаблонов вообще нет, создаем базовый шаблон
        if (!$defaultTemplate) {
            $defaultTemplate = $this->createDefaultTemplate();
        }
        
        return $defaultTemplate ? $defaultTemplate->id : null;
    }
    
    /**
     * Создает базовый шаблон если он не существует
     *
     * @return \App\Models\CertificateTemplate
     */
    protected function createDefaultTemplate()
    {
        // Получаем или создаем категорию по умолчанию
        $category = TemplateCategory::getDefaultCategory();
        
        // Проверяем существование файла шаблона
        $templatePath = 'templates/certificates/classic-certificate.php';
        $fullPath = public_path($templatePath);
        
        if (!file_exists($fullPath)) {
            \Log::error('Файл базового шаблона не существует', ['path' => $templatePath]);
            return null;
        }
        
        // Получаем HTML-контент шаблона
        $htmlTemplate = file_get_contents($fullPath);
        
        // Создаем шаблон
        $template = CertificateTemplate::create([
            'name' => 'Классический сертификат',
            'slug' => 'classic-certificate',
            'description' => 'Базовый шаблон сертификата',
            'category_id' => $category->id,
            'template_path' => $templatePath,
            'html_template' => $htmlTemplate,
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 1,
            'document_type' => 'certificate'
        ]);
        
        \Log::info("Создан базовый шаблон сертификата с ID: {$template->id}");
        
        return $template;
    }
    
    /**
     * Показать форму для создания документа через iframe на основе выбранного шаблона.
     */
    public function createWithIframe(CertificateTemplate $template)
    {
        // Проверка доступа к премиум-шаблонам
        if ($template->is_premium && !Auth::user()->hasSubscriptionLevel('vip')) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Для использования премиум-шаблонов необходимо обновить тарифный план');
        }
        
        return view('entrepreneur.certificates.create_iframe', compact('template'));
    }
    
    /**
     * Сохранить новый документ.
     */
    public function store(Request $request, CertificateTemplate $template)
    {
        // Выводим в лог все данные запроса для отладки
        \Log::debug('Данные запроса при создании сертификата', [
            'request_data' => $request->all(),
            'session' => session()->all()
        ]);
        
        // Получаем количество создаваемых сертификатов
        $batchSize = max(1, min(100, intval($request->input('batch_size', 1))));
        
        // Проверяем наличие достаточного количества стиков у пользователя
        $user = Auth::user();
        
        // Улучшенная проверка стиков
        if ($user->sticks < $batchSize) {
            return back()
                ->withInput()
                ->withErrors(['batch_size' => "Недостаточно стиков для создания {$batchSize} сертификатов. У вас доступно: {$user->sticks} стиков. Пожалуйста, обновите тарифный план или уменьшите размер серии."]);
        }
        
        // Поиск пути к обложке из всех возможных источников
        $coverPath = $this->findCoverPath($request);
        
        // Если coverPath не найден, добавляем его в ошибки валидации
        if (!$coverPath) {
            return back()
                ->withInput()
                ->withErrors(['temp_cover_path' => 'Пожалуйста, создайте обложку документа в фоторедакторе']);
        }

        // Добавляем найденный путь к обложке в request
        $request->merge(['temp_cover_path' => $coverPath]);
        
        // Валидация запроса с учетом временного пути
        // Удалены валидации recipient_name и recipient_phone, т.к. они не требуются на этом этапе
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:1000000',
            'amount_type' => 'nullable|in:money,percent',
            'message' => 'required|string|max:1000',
            'company_name' => 'required|string|max:255',
            'valid_from' => 'nullable|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'temp_cover_path' => 'required|string',
            'animation_effect_id' => 'nullable|integer|exists:animation_effects,id',
            'batch_size' => 'nullable|integer|min:1|max:' . $user->sticks, // Ограничиваем максимальное значение доступными стиками
        ]);
        
        // Определение суммы в зависимости от типа (деньги или процент)
        $isPercent = $request->input('amount_type') === 'percent';
        $amount = $isPercent 
            ? min($request->input('amount'), 100) // Если процент, максимум 100
            : $request->input('amount');          // Если деньги, используем как есть
        
        // Обработка даты начала действия сертификата
        $validFrom = $request->filled('valid_from') 
                    ? Carbon::parse($request->input('valid_from'))
                    : Carbon::now();
                    
        // Обработка даты окончания действия сертификата
        $validUntil = Carbon::parse($request->input('valid_until'));
        
        // Убедимся, что valid_until не меньше valid_from
        if ($validUntil->lessThanOrEqualTo($validFrom)) {
            $validUntil = $validFrom->copy()->addMonths(3);
        }
        
        // Создаем только родительский сертификат (без получателя)
        // Принудительно устанавливаем is_batch_parent в true для серий
        $isBatchParent = $batchSize > 1;
        
        // Определяем статус сертификата на основе batch_size
        $status = $isBatchParent ? Certificate::STATUS_SERIES : Certificate::STATUS_ACTIVE;
        
        $parentCertificate = $this->createCertificate(
            $template, 
            $request, 
            $user->id, 
            $amount, 
            $isPercent, 
            $validFrom, 
            $validUntil, 
            $coverPath, 
            $batchSize,
            $isBatchParent,
            null,  
            null,  
            $status  // Передаем определенный статус
        );
        
        // ВАЖНАЯ ПРОВЕРКА - подтверждаем, что флаг установлен корректно
        if ($isBatchParent && !$parentCertificate->is_batch_parent) {
            \Log::error("КРИТИЧЕСКАЯ ОШИБКА: Флаг is_batch_parent не был установлен для сертификата {$parentCertificate->id}");
            $parentCertificate->is_batch_parent = true;
            $parentCertificate->save();
        }
        
        // После успешного создания сертификата, создаем уведомление
        $this->notificationService->certificateIssued($parentCertificate);
        
        // Очистка данных о временной обложке из всех хранилищ
        $this->clearCoverPathData();
        
        // Перенаправление на страницу списка документов
        return redirect()->route('entrepreneur.certificates.show', $parentCertificate)
            ->with('success', "Документ успешно создан! " . 
                  ($batchSize > 1 ? "Создана серия из {$batchSize} сертификатов. " : "") . 
                  "Списано {$batchSize} стиков.");
    }
    
    /**
     * Метод для создания одного сертификата (обновленный для поддержки серий)
     */
    protected function createCertificate(
        $template, 
        $request, 
        $userId, 
        $amount, 
        $isPercent, 
        $validFrom, 
        $validUntil, 
        $coverPath, 
        $batchSize = 1,
        $isBatchParent = false,
        $parentId = null,
        $batchNumber = null,
        $status = null  // Изменяем параметр, пусть будет null по умолчанию
    ) {
        // Генерация уникального номера документа перед созданием
        $certificateNumber = $this->generateCertificateNumber();
        
        // Если статус не определен, устанавливаем его на основе типа сертификата
        if ($status === null) {
            $status = $isBatchParent && $batchSize > 1 ? Certificate::STATUS_SERIES : Certificate::STATUS_ACTIVE;
        }
        
        // Подготавливаем данные для создания документа
        $certificateData = [
            'certificate_number' => $certificateNumber,
            'user_id' => $userId,
            'certificate_template_id' => $template->id,
            // Не устанавливаем данные получателя для сертификата-серии
            'recipient_name' => null,
            'recipient_email' => null,
            'recipient_phone' => null,
            'amount' => $amount,
            'is_percent' => $isPercent,
            'message' => $request->input('message'),
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'status' => $status,  // Используем переданный или вычисленный статус
            'animation_effect_id' => $request->input('animation_effect_id'),
            'batch_size' => $batchSize,
            'is_batch_parent' => $isBatchParent,
            'parent_id' => $parentId,
            'batch_number' => $batchNumber,
            // Инициализируем счетчик активированных копий 
            'activated_copies_count' => 0
        ];
        
        // Создаем документ
        $certificate = Certificate::create($certificateData);
        
        // Обработка логотипа
        if ($request->logo_type === 'default') {
            $certificate->company_logo = Auth::user()->company_logo;
        } elseif ($request->logo_type === 'custom' && $request->hasFile('custom_logo')) {
            $certificate->company_logo = $this->imageService->createLogo($request->file('custom_logo'), 'company_logos');
        } else {
            $certificate->company_logo = null;
        }
        
        // Обработка изображения обложки
        if ($coverPath) {
            $certificate->cover_image = $coverPath;
        } else {
            // Запасной вариант для прямой загрузки файла (если есть)
            if ($request->hasFile('cover_image')) {
                $certificate->cover_image = $request->file('cover_image')->store('certificate_covers', 'public');
            }
        }
        
        $certificate->save();
        
        // Если это билет в кино и есть специфические поля билета, сохраняем их
        $isTicket = $template->document_type === 'ticket' || 
                    ($template->category && $template->category->document_type === 'ticket');
        
        if ($isTicket) {
            // Создаем запись с деталями билета
            $certificate->ticketDetail()->create([
                'movie_title' => $request->input('movie_title'),
                'cinema_name' => $request->input('cinema_name'),
                'hall_name' => $request->input('hall_name'),
                'seat_row' => $request->input('seat_row'),
                'seat_number' => $request->input('seat_number'),
                'show_date' => $request->input('show_date') ? Carbon::parse($request->input('show_date')) : null,
                'show_time' => $request->input('show_time')
            ]);
        }
        
        return $certificate;
    }
    
    /**
     * Ищет путь к обложке во всех возможных источниках
     * 
     * @param Request $request
     * @return string|null
     */
    private function findCoverPath(Request $request)
    {
        // Проверяем запрос напрямую через input
        $coverPath = $request->input('temp_cover_path');
        if ($coverPath) {
            \Log::info("Путь к обложке найден в request->input", ['path' => $coverPath]);
            return $coverPath;
        }
        
        // Проверяем запрос напрямую через post
        $postCoverPath = $request->post('temp_cover_path');
        if ($postCoverPath && $postCoverPath !== $coverPath) {
            \Log::info("Путь к обложке найден в request->post", ['path' => $postCoverPath]);
            return $postCoverPath;
        }
        
        // Проверяем параметр URL
        $urlCoverPath = $request->query('cover');
        if ($urlCoverPath) {
            \Log::info("Путь к обложке найден в URL", ['path' => $urlCoverPath]);
            return $urlCoverPath;
        }
        
        // Проверяем сессию
        $sessionCoverPath = session('temp_certificate_cover');
        if ($sessionCoverPath) {
            \Log::info("Путь к обложке найден в сессии", ['path' => $sessionCoverPath]);
            return $sessionCoverPath;
        }
        
        // Проверяем cookie
        $cookieCoverPath = $request->cookie('temp_certificate_cover');
        if ($cookieCoverPath) {
            \Log::info("Путь к обложке найден в cookie", ['path' => $cookieCoverPath]);
            return $cookieCoverPath;
        }
        
        // Проверяем существование файла на основе возможных имен файлов
        $possiblePaths = $this->findPossibleCoverPaths();
        if (!empty($possiblePaths)) {
            \Log::info("Путь к обложке найден через поиск файлов", ['path' => $possiblePaths[0]]);
            return $possiblePaths[0];
        }
        
        \Log::error("Путь к обложке не найден ни в одном из источников");
        return null;
    }

    /**
     * Ищет возможные пути к обложке на основе последних созданных файлов
     * @return array
     */
    private function findPossibleCoverPaths()
    {
        $tempDir = 'temp/certificates';
        $fullPath = storage_path('app/public/' . $tempDir);
        
        // Проверяем существование директории
        if (!is_dir($fullPath)) {
            return [];
        }
        
        // Получаем список файлов в директории
        $files = scandir($fullPath);
        
        // Фильтруем только файлы изображений и сортируем по времени создания (новые первые)
        $imageFiles = array_filter($files, function($file) use ($fullPath) {
            $fullFilePath = $fullPath . '/' . $file;
            
            // Пропускаем . и ..
            if ($file === '.' || $file === '..') {
                return false;
            }
            
            // Проверяем, что это файл и что он имеет расширение изображения
            $isFile = is_file($fullFilePath);
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            
            // Проверяем, что файл создан недавно (не более часа назад)
            return $isFile && $isImage && time() - filemtime($fullFilePath) < 3600; // Созданы не более часа назад
        });
        
        // Сортируем по времени изменения (новые первые)
        usort($imageFiles, function($a, $b) use ($fullPath) {
            return filemtime($fullPath . '/' . $b) - filemtime($fullPath . '/' . $a);
        });
        
        // Формируем полные пути для использования
        $paths = array_map(function($file) use ($tempDir) {
            return $tempDir . '/' . $file;
        }, $imageFiles);
        
        return $paths;
    }
    
    /**
     * Очищает данные о временной обложке из всех хранилищ
     */
    private function clearCoverPathData()
    {
        // Очищаем данные о временной обложке из сессии
        session()->forget('temp_certificate_cover');
        
        // Устанавливаем cookie с пустым значением
        cookie()->queue(cookie()->forget('temp_certificate_cover'));
        
        // Логируем успешную очистку
        \Log::info("Данные о временной обложке очищены после создания сертификата");
    }
    
    /**
     * Показать детали документа.
     */
    public function show(Certificate $certificate)
    {
        $this->authorize('view', $certificate);
        
        // Загружаем связанные копии, если это родительский сертификат серии
        if ($certificate->is_batch_parent && $certificate->batch_size > 1) {
            $certificate->load('copies');
        }
        
        return view('entrepreneur.certificates.show', compact('certificate'));
    }
    
    /**
     * Показать форму для редактирования документа.
     */
    public function edit(Certificate $certificate)
    {
        // Проверяем авторизацию
        $this->authorize('update', $certificate);
        
        // Получаем шаблон, связанный с сертификатом
        $template = $certificate->template;
        
        // Проверяем существование шаблона
        if (!$template) {
            return redirect()->route('entrepreneur.certificates.index')
                ->with('error', 'Не удалось найти шаблон для этого сертификата.');
        }
        
        return view('entrepreneur.certificates.edit', compact('certificate', 'template'));
    }
    
    /**
     * Обновить существующий документ.
     */
    public function update(Request $request, Certificate $certificate)
    {
        $this->authorize('update', $certificate);
        
        // Валидация данных - удаляем проверку recipient_phone
        $validated = $request->validate([
            'recipient_name' => 'nullable|string|max:255',
            'recipient_email' => 'nullable|email|max:255',
            'amount' => 'required|numeric|min:1|max:1000000',
            'amount_type' => 'nullable|in:money,percent',
            'message' => 'required|string|max:1000',
            'company_name' => 'required|string|max:255',
            'valid_from' => 'nullable|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);
        
        // Определение суммы в зависимости от типа (деньги или процент)
        $isPercent = $request->input('amount_type') === 'percent';
        $amount = $isPercent 
            ? min($request->input('amount'), 100) // Если процент, максимум 100
            : $request->input('amount');          // Если деньги, используем как есть
        
        // Обработка даты начала действия сертификата
        $validFrom = $request->filled('valid_from') 
                    ? Carbon::parse($request->input('valid_from'))
                    : $certificate->valid_from ?? Carbon::now();
                    
        // Обработка даты окончания действия сертификата
        $validUntil = Carbon::parse($request->input('valid_until'));
        
        // Убедимся, что valid_until не меньше valid_from
        if ($validUntil->lessThanOrEqualTo($validFrom)) {
            $validUntil = $validFrom->copy()->addMonths(3);
        }
        
        // Обновляем основные данные сертификата
        $certificate->update([
            'recipient_name' => $request->input('recipient_name'),
            'recipient_email' => $request->input('recipient_email'),
            'amount' => $amount,
            'is_percent' => $isPercent,
            'message' => $request->input('message'),
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
        ]);
        
        // Перенаправление на страницу деталей документа
        return redirect()->route('entrepreneur.certificates.show', $certificate)
            ->with('success', 'Документ успешно обновлен!');
    }
    
    /**
     * Удалить документ.
     */
    public function destroy(Certificate $certificate)
    {
        $this->authorize('delete', $certificate);
        
        // Удаляем связанные сущности
        if ($certificate->ticketDetail) {
            $certificate->ticketDetail->delete();
        }
        
        // Удаляем сам документ
        $certificate->delete();
        
        return redirect()->route('entrepreneur.certificates.index')
            ->with('success', 'Документ успешно удален!');
    }
    
    /**
     * Генерирует уникальный номер для сертификата
     * 
     * @return string
     */
    private function generateCertificateNumber()
    {
        $prefix = 'SN';
        $date = now()->format('ymd');
        $random = strtoupper(Str::random(4));
        
        return $prefix . $date . $random;
    }
    
    /**
     * Отметить документ как использованный.
     */
    public function markAsUsed(Certificate $certificate)
    {
        $this->authorize('update', $certificate);
        
        if ($certificate->status === 'used') {
            return back()->with('error', 'Документ уже отмечен как использованный');
        }
        
        $certificate->status = 'used';
        $certificate->used_at = now();
        $certificate->save();
        
        // Создаем уведомление об использовании сертификата
        $this->notificationService->certificateUsed($certificate);
        
        return back()->with('success', 'Документ отмечен как использованный');
    }
    
    /**
     * Восстановить статус документа как активный.
     */
    public function markAsActive(Certificate $certificate)
    {
        $this->authorize('update', $certificate);
        
        $certificate->status = 'active';
        $certificate->used_at = null;
        $certificate->save();
        
        return back()->with('success', 'Документ восстановлен как активный');
    }
    
    /**
     * Страница быстрого изменения статуса сертификата по QR-коду
     * 
     * @param string $uuid UUID сертификата
     * @return \Illuminate\Http\Response
     */
    public function statusChangeByQR($uuid)
    {
        // Находим сертификат по UUID
        $certificate = Certificate::where('uuid', $uuid)->first();
        
        // Если сертификат не найден, показываем сообщение об ошибке
        if (!$certificate) {
            return view('entrepreneur.certificates.status-change', [
                'error' => 'Сертификат не найден',
            ]);
        }
        
        // Проверяем права доступа - только создатель сертификата может менять его статус
        if ($certificate->user_id != Auth::id()) {
            return view('entrepreneur.certificates.status-change', [
                'error' => 'У вас нет прав для изменения статуса этого сертификата. Только предприниматель, создавший сертификат, может менять его статус.',
                'certificate' => $certificate,
                'readonly' => true
            ]);
        }
        
        // Проверяем, активирован ли сертификат
        $isClaimed = $certificate->isClaimed(); // Метод isClaimed должен быть определен в модели Certificate
        
        // Передаем сертификат и информацию о его состоянии в представление
        return view('entrepreneur.certificates.status-change', [
            'certificate' => $certificate,
            'isClaimed' => $isClaimed,
            'readonly' => false
        ]);
    }

    /**
     * Обработка быстрого изменения статуса сертификата по QR-коду
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatusByQR(Request $request)
    {
        // Валидируем данные запроса
        $validated = $request->validate([
            'certificate_id' => 'required|exists:certificates,id',
            'status' => 'required|in:active,used',
        ]);
        
        $certificate = Certificate::findOrFail($validated['certificate_id']);
        
        // Строгая проверка прав доступа - только создатель сертификата может менять его статус
        if ($certificate->user_id != Auth::id()) {
            return back()->with('error', 'У вас нет прав для изменения статуса этого сертификата. Только предприниматель, создавший сертификат, может менять его статус.');
        }
        
        // Обновляем статус
        $newStatus = $validated['status'];
        $certificate->status = $newStatus;
        
        // Если статус "использован", устанавливаем дату использования
        if ($newStatus === 'used') {
            $certificate->used_at = now();
            
            // Создаем уведомление об использовании сертификата
            $this->notificationService->certificateUsed($certificate);
        } else {
            $certificate->used_at = null;
        }
        
        $certificate->save();
        
        // Возвращаемся с сообщением об успехе
        return back()->with('success', 'Статус сертификата успешно изменен');
    }
}
