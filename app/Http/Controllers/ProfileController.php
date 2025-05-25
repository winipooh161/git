<?php

namespace App\Http\Controllers;

use App\Models\User; // Добавляем импорт модели User
use App\Services\ImageService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class ProfileController extends Controller
{
    protected $imageService;
    protected $smsService;
    
    public function __construct(ImageService $imageService, SmsService $smsService)
    {
        $this->imageService = $imageService;
        $this->smsService = $smsService;
    }
    
    /**
     * Показать страницу профиля пользователя.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Обновить общую информацию о пользователе.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'company' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'company_logo' => 'nullable|image|max:2048',
        ]);
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->company = $validated['company'] ?? null;
        
        // Обработка аватара с использованием сервиса сжатия изображений
        if ($request->hasFile('avatar')) {
            // Удаляем старый аватар, если он существует
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }
            
            // Сжимаем и сохраняем новый аватар
            $user->avatar = $this->imageService->createAvatar($request->file('avatar'), 'avatars');
        }
        
        // Обработка логотипа компании
        if ($request->hasFile('company_logo')) {
            // Удаляем старый логотип, если он существует
            if ($user->company_logo && Storage::exists('public/' . $user->company_logo)) {
                Storage::delete('public/' . $user->company_logo);
            }
            
            // Сжимаем и сохраняем новый логотип
            $user->company_logo = $this->imageService->createLogo($request->file('company_logo'), 'company_logos');
        }
        
        $user->save();
        
        return back()->with('success', 'Профиль успешно обновлен.');
    }

    /**
     * Обновить пароль пользователя.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Текущий пароль указан неверно.');
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        return back()->with('success', 'Пароль успешно изменен.');
    }

    /**
     * Обновить настройки уведомлений.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();
        
        // Получаем настройки уведомлений из формы
        $notificationPreferences = $request->input('notification_preferences', []);
        
        // Сохраняем настройки уведомлений в JSON формате
        $user->notification_preferences = json_encode($notificationPreferences);
        $user->save();
        
        return back()->with('success', 'Настройки уведомлений обновлены.');
    }

    /**
     * Показать страницу настроек пользователя.
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    /**
     * Инициировать смену номера телефона
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiatePhoneChange(Request $request)
    {
        Log::info('Начало initiatePhoneChange, данные запроса:', $request->all());
        
        try {
            $phone = $request->input('phone');
            
            if (empty($phone)) {
                return response()->json(['success' => false, 'message' => 'Номер телефона не указан'], 400);
            }
            
            // Форматирование телефона в стандартный вид
            $phone = preg_replace('/\D/', '', $phone); // Очищаем от всех нецифровых символов
            
            // Нормализуем номер телефона для России
            if (strlen($phone) === 10) {
                // Добавляем код страны если передан номер без кода (10 цифр)
                $phone = '7' . $phone;
            } else if (strlen($phone) === 11) {
                // Если первая цифра 8, заменяем на 7 (код России)
                if (substr($phone, 0, 1) === '8') {
                    $phone = '7' . substr($phone, 1);
                }
            }
            
            // Проверяем корректность номера после нормализации
            if (strlen($phone) !== 11 || substr($phone, 0, 1) !== '7') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Некорректный формат номера телефона. Используйте российский формат.'
                ], 400);
            }
            
            Log::info("Нормализованный номер телефона: {$phone}");
            
            $user = Auth::user();
            
            // Проверка на уникальность телефона
            if ($phone !== preg_replace('/\D/', '', $user->phone) && 
                User::where('phone', 'LIKE', '%' . substr($phone, -10) . '%')->where('id', '!=', $user->id)->exists()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Этот номер телефона уже используется другим пользователем'
                ], 400);
            }
            
            // Форматируем телефон для отображения
            $formattedPhone = '+' . substr($phone, 0, 1) . ' (' . substr($phone, 1, 3) . ') ' . 
                              substr($phone, 4, 3) . '-' . substr($phone, 7, 2) . '-' . substr($phone, 9, 2);
            
            $code = sprintf('%04d', rand(0, 9999)); // Генерация 4-значного кода
            
            Log::info("Сгенерирован код: {$code} для номера: {$formattedPhone}");
            
            // Сохраняем код и данные для верификации
            $user->phone_verification_code = $code;
            $user->phone_verification_expires_at = now()->addMinutes(15);
            $user->phone_pending_change = $formattedPhone;
            $user->save();
            
            // Для отладки просто выводим код в лог
            Log::info("Код для телефона {$formattedPhone}: {$code}");
            
            // В режиме разработки пропускаем реальную отправку SMS
            return response()->json([
                'success' => true, 
                'message' => 'Код подтверждения отправлен на указанный номер',
                'debug_code' => config('app.debug') ? $code : null // Только для режима отладки
            ]);
            
        } catch (\Exception $e) {
            Log::error("Исключение в initiatePhoneChange: " . $e->getMessage());
            Log::error("Стек вызовов: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Произошла ошибка сервера: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Подтвердить смену номера телефона
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyPhoneChange(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->phone_verification_code || !$user->phone_pending_change || 
            !$user->phone_verification_expires_at || $user->phone_verification_expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'Срок действия кода истек или запрос на смену номера не был инициирован.'], 400);
        }
        
        $enteredCode = $request->input('code1') . $request->input('code2') . 
                       $request->input('code3') . $request->input('code4');
        
        if ($enteredCode != $user->phone_verification_code) {
            return response()->json(['success' => false, 'message' => 'Неверный код подтверждения.'], 400);
        }
        
        // Код подтвержден, меняем номер телефона
        $user->phone = $user->phone_pending_change;
        $user->phone_verification_code = null;
        $user->phone_verification_expires_at = null;
        $user->phone_pending_change = null;
        $user->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Номер телефона успешно изменен.',
            'phone' => $user->phone
        ]);
    }

    /**
     * Инициировать процесс проверки номера телефона
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiatePhoneVerification(Request $request)
    {
        // Валидация запроса
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:/^\+?\d+$/'],
            'email' => ['required', 'string', 'email']
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный формат телефона или email',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Получаем номер телефона и email
        $phone = $request->phone;
        $email = $request->email;
        
        // Проверяем, что email принадлежит пользователю
        $user = Auth::user();
        if ($user->email !== $email) {
            return response()->json([
                'success' => false,
                'message' => 'Указанный email не соответствует вашему профилю'
            ], 422);
        }
        
        // Генерируем случайный код
        $verificationCode = rand(1000, 9999);
        
        // Сохраняем код в сессии для последующей проверки
        session(['phone_verification_code' => $verificationCode]);
        session(['phone_to_verify' => $phone]);
        session(['phone_verification_attempts' => 0]);
        
        // Отправляем код на email пользователя
        try {
            Mail::to($email)->send(new VerificationCodeMail($verificationCode));
            
            // Логируем событие
            Log::info('Код верификации телефона отправлен на email', [
                'user_id' => $user->id,
                'phone' => $phone,
                'email' => $email
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Код верификации отправлен на ваш email',
                'debug_code' => config('app.debug') ? $verificationCode : null // В режиме отладки возвращаем код
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка отправки кода верификации по email', [
                'user_id' => $user->id,
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Не удалось отправить код верификации. Попробуйте позже.'
            ], 500);
        }
    }
}
