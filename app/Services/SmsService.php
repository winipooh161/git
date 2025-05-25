<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $apiId;
    protected $baseUrl;
    
    public function __construct()
    {
        $this->apiId = env('SMSRU_API_ID', '06120E86-0897-D299-58A1-FC969ECCDC09');
        $this->baseUrl = 'https://sms.ru/sms/send';
    }
    
    /**
     * Отправить SMS с кодом подтверждения на указанный номер
     *
     * @param string $phone Номер телефона получателя
     * @param string $code Код подтверждения
     * @return bool Успешность отправки
     */
    public function sendVerificationCode($phone, $code)
    {
        try {
            // Очищаем номер телефона от всех нецифровых символов
            $phone = preg_replace('/\D/', '', $phone);
            
            // Стандартизируем формат номера телефона для России
            if (strlen($phone) === 10) {
                $phone = '7' . $phone;
            } elseif (strlen($phone) === 11) {
                if (substr($phone, 0, 1) === '8') {
                    $phone = '7' . substr($phone, 1);
                }
            }
            
            // Проверяем правильность формата
            if (strlen($phone) !== 11 || substr($phone, 0, 1) !== '7') {
                Log::error("Неверный формат телефона: {$phone}");
                return false;
            }
            
            // В режиме разработки только логируем без реальной отправки
            if (config('app.debug')) {
                Log::info("ОТЛАДКА: SMS с кодом {$code} будет отправлен на номер {$phone}");
                return true;
            }
            
            // В рабочем режиме отправляем через SMS.ru API
            $response = Http::get($this->baseUrl, [
                'api_id' => $this->apiId,
                'to' => $phone,
                'msg' => "Ваш код подтверждения: {$code}",
                'json' => 1
            ]);
            
            $result = $response->json();
            Log::info("Ответ от SMS.RU: " . json_encode($result));
            
            if (isset($result['status']) && $result['status'] === 'OK' && 
                isset($result['sms'][$phone]['status']) && $result['sms'][$phone]['status'] === 'OK') {
                Log::info("SMS успешно отправлено на номер {$phone}");
                return true;
            }
            
            Log::error("Ошибка отправки SMS: " . json_encode($result));
            return false;
        } catch (\Exception $e) {
            Log::error("Исключение при отправке SMS: " . $e->getMessage());
            return false;
        }
    }
}
