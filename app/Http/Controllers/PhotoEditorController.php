<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class PhotoEditorController extends Controller
{
    /**
     * Показать страницу редактора фотографий
     */
    public function index(Request $request)
    {
        // Получаем ID шаблона из запроса, если он был передан
        $templateId = $request->query('template');
        
        // Если ID шаблона не передан, но пользователь авторизован, проверяем его тариф
        if (!$templateId && Auth::check() && Auth::user()->hasRole('predprinimatel')) {
            // Получаем текущий тарифный план пользователя
            $currentPlan = Auth::user()->currentPlan();
            
            if ($currentPlan && $currentPlan->slug === 'start') {
                // Для тарифа start ищем базовый шаблон
                $certificates = new CertificatesController(new ImageService());
                $templateId = $certificates->getDefaultTemplateId();
            }
        }
        
        return view('photo-editor.photo-editor', compact('templateId'));
    }

    /**
     * Сохраняет отредактированное изображение для дальнейшего использования при создании документа
     *
     * @param Request $request
     * @param int $templateId ID шаблона документа
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveToCertificate(Request $request, $templateId)
    {
        try {
            // Проверим содержит ли запрос данные изображения
            if (!$request->has('image') && !$request->hasFile('image')) {
                \Log::error('Отсутствуют данные изображения в запросе', ['templateId' => $templateId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Отсутствуют данные изображения'
                ], 400);
            }
            
            // Проверяем существование шаблона
            $template = CertificateTemplate::find($templateId);
            if (!$template) {
                \Log::error('Шаблон не найден', ['templateId' => $templateId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Шаблон документа не найден'
                ], 404);
            }
            
            // Подготавливаем директории для сохранения
            $tempPath = storage_path('app/public/temp/certificates');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            // Обработка изображения - поддерживаем как файл, так и base64
            $path = null;
            
            // Случай, когда передана base64 строка
            if ($request->has('image') && is_string($request->image)) {
                // Для отладки сохраним часть строки в лог
                $imageDataSample = substr($request->image, 0, 100);
                \Log::info('Получена base64 строка изображения', ['sample' => $imageDataSample]);
                
                // Проверяем формат base64 и удаляем метаданные
                $imageData = $request->image;
                if (strpos($imageData, 'data:image') === 0) {
                    $parts = explode(',', $imageData, 2);
                    $imageData = isset($parts[1]) ? $parts[1] : $imageData;
                }
                
                // Декодируем строку
                $decodedImage = base64_decode($imageData);
                if (!$decodedImage) {
                    \Log::error('Ошибка декодирования base64 строки');
                    return response()->json([
                        'success' => false, 
                        'message' => 'Ошибка декодирования изображения'
                    ], 400);
                }
                
                // Генерируем имя файла и сохраняем изображение
                $filename = 'certificate_' . time() . '_' . uniqid() . '.png';
                $fullPath = $tempPath . '/' . $filename;
                $result = file_put_contents($fullPath, $decodedImage);
                
                if ($result === false) {
                    \Log::error('Ошибка сохранения файла', [
                        'path' => $fullPath,
                        'diskSpace' => disk_free_space(dirname($fullPath))
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Ошибка сохранения файла на сервере'
                    ], 500);
                }
                
                $path = 'temp/certificates/' . $filename;
            } 
            // Случай, когда передан файл
            elseif ($request->hasFile('image')) {
                $file = $request->file('image');
                if (!$file->isValid()) {
                    \Log::error('Загруженный файл поврежден или его загрузка не завершена');
                    return response()->json([
                        'success' => false,
                        'message' => 'Загруженный файл поврежден'
                    ], 400);
                }
                
                $path = $file->store('temp/certificates', 'public');
            }
            
            if (!$path) {
                \Log::error('Не удалось сохранить изображение, путь не определен');
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось сохранить изображение'
                ], 500);
            }
            
            // Проверим доступность созданного файла
            if (!file_exists(storage_path('app/public/' . $path))) {
                \Log::error('Файл создан, но не найден по указанному пути', ['path' => $path]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка доступа к сохраненному файлу'
                ], 500);
            }
            
            // Сохраняем путь к изображению во всех возможных местах
            session(['temp_certificate_cover' => $path]);
            \Log::info('Изображение успешно сохранено', ['path' => $path]);
            
            // Формируем URL для редиректа
            $redirectUrl = route('entrepreneur.certificates.create-with-iframe', $template);
            
            // Создаем несколько cookie разной длительности для надежности
            $cookie1min = Cookie::make('temp_certificate_cover', $path, 1); // 1 минута
            $cookie30min = Cookie::make('temp_certificate_cover_30', $path, 30); // 30 минут
            $cookie24h = Cookie::make('temp_certificate_cover_24h', $path, 1440); // 24 часа
            
            // Возвращаем ответ с несколькими cookie
            $response = response()
                ->json([
                    'success' => true,
                    'message' => 'Изображение успешно сохранено',
                    'path' => $path,
                    'redirect' => $redirectUrl . '?cover=' . urlencode($path),
                ])
                ->withCookie($cookie1min)
                ->withCookie($cookie30min)
                ->withCookie($cookie24h);
            
            \Log::info('Ответ с путем к изображению готов к отправке', [
                'path' => $path, 
                'redirect_url' => $redirectUrl . '?cover=' . urlencode($path)
            ]);
            
            return $response;
    
        } catch (\Exception $e) {
            \Log::error('Исключение при обработке изображения: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Загружает стикер, загруженный пользователем
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadSticker(Request $request)
    {
        $request->validate([
            'sticker' => 'required|image|max:2048', // Максимальный размер 2MB
        ]);

        $path = $request->file('sticker')->store('stickers', 'public');
        
        return response()->json([
            'success' => true,
            'url' => Storage::url($path)
        ]);
    }

    /**
     * Сохраняет проект для последующего редактирования
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveProject(Request $request)
    {
        $request->validate([
            'project_data' => 'required|string',
            'preview' => 'required|string'
        ]);

        $filename = 'project_' . Str::random(10) . '_' . time() . '.json';
        
        Storage::disk('public')->put('projects/' . $filename, $request->project_data);
        
        // Сохраняем превью проекта отдельно
        $previewData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->preview));
        $previewFilename = 'preview_' . pathinfo($filename, PATHINFO_FILENAME) . '.png';
        Storage::disk('public')->put('projects/previews/' . $previewFilename, $previewData);

        return response()->json([
            'success' => true,
            'filename' => $filename
        ]);
    }

    /**
     * Загружает ранее сохраненный проект
     *
     * @param string $filename
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadProject($filename)
    {
        if (!Storage::disk('public')->exists('projects/' . $filename)) {
            return response()->json([
                'success' => false,
                'message' => 'Проект не найден'
            ], 404);
        }

        $projectData = Storage::disk('public')->get('projects/' . $filename);

        return response()->json([
            'success' => true,
            'project_data' => $projectData
        ]);
    }

    /**
     * Возвращает список доступных фильтров
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilters()
    {
        $filters = [
            'normal' => ['name' => 'Обычный', 'params' => []],
            'vintage' => ['name' => 'Винтаж', 'params' => ['brightness' => -0.1, 'contrast' => 0.1, 'sepia' => true]],
            'sepia' => ['name' => 'Сепия', 'params' => ['sepia' => true]],
            'grayscale' => ['name' => 'Ч/Б', 'params' => ['grayscale' => true]],
            'lomo' => ['name' => 'Ломо', 'params' => ['brightness' => 0.05, 'contrast' => 0.2, 'saturation' => 0.3]],
            'clarity' => ['name' => 'Четкость', 'params' => ['contrast' => 0.3, 'sharpen' => true]],
        ];

        return response()->json([
            'success' => true,
            'filters' => $filters
        ]);
    }
}
