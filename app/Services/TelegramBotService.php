<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    protected $token;
    protected $apiBaseUrl = 'https://api.telegram.org/bot';

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Отправка сообщения
     */
    public function sendMessage($chatId, $text, $keyboard = null)
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
            
            Log::debug('Подготовка отправки сообщения с клавиатурой', [
                'chat_id' => $chatId,
                'text' => $text,
                'keyboard' => json_encode($keyboard)
            ]);
        } else {
            Log::debug('Подготовка отправки простого сообщения', [
                'chat_id' => $chatId,
                'text' => $text
            ]);
        }

        try {
            // Добавляем более явную обработку отправки сообщения
            Log::debug('Отправка запроса к API Telegram', ['endpoint' => 'sendMessage', 'data' => $data]);
            
            $result = $this->sendRequest('sendMessage', $data);
            
            if (!isset($result['ok']) || !$result['ok']) {
                Log::error('Ошибка отправки сообщения в Telegram API', [
                    'chat_id' => $chatId, 
                    'error' => $result['description'] ?? 'Неизвестная ошибка',
                    'result' => $result
                ]);
            } else {
                Log::debug('Сообщение успешно отправлено через API Telegram', [
                    'chat_id' => $chatId,
                    'message_id' => $result['result']['message_id'] ?? null
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Исключение при отправке сообщения через Telegram API', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Ответ на callback query
     */
    public function answerCallbackQuery($callbackQueryId, $text = null, $showAlert = false)
    {
        $data = [
            'callback_query_id' => $callbackQueryId,
            'show_alert' => $showAlert
        ];
        
        if ($text) {
            $data['text'] = $text;
        }
        
        return $this->sendRequest('answerCallbackQuery', $data);
    }

    /**
     * Изменение существующего сообщения
     */
    public function editMessageText($chatId, $messageId, $text, $keyboard = null)
    {
        $data = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];
        
        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }
        
        return $this->sendRequest('editMessageText', $data);
    }

    /**
     * Установка вебхука
     */
    public function setWebhook($url)
    {
        return $this->sendRequest('setWebhook', ['url' => $url]);
    }

    /**
     * Удаление вебхука
     */
    public function deleteWebhook()
    {
        return $this->sendRequest('deleteWebhook');
    }

    /**
     * Получение информации о вебхуке
     */
    public function getWebhookInfo()
    {
        return $this->sendRequest('getWebhookInfo');
    }

    /**
     * Получение информации о боте
     */
    public function getMe()
    {
        return $this->sendRequest('getMe');
    }
    
    /**
     * Получение обновлений (для тестирования)
     */
    public function getUpdates($limit = 100)
    {
        return $this->sendRequest('getUpdates', [
            'limit' => $limit,
            'timeout' => 0,
            'allowed_updates' => json_encode(['message', 'callback_query'])
        ]);
    }

    /**
     * Отправка запроса в Telegram API
     */
    protected function sendRequest($method, $params = [])
    {
        $url = $this->apiBaseUrl . $this->token . '/' . $method;
        
        try {
            // Увеличиваем таймаут для надежности
            $response = Http::timeout(30)->post($url, $params);
            
            if (!$response->successful()) {
                Log::error('Неудачный HTTP-ответ от Telegram API', [
                    'method' => $method,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
            
            $result = $response->json();
            
            if (!$result['ok']) {
                Log::error('Telegram API вернул ошибку', [
                    'method' => $method, 
                    'params' => $params, 
                    'error_code' => $result['error_code'] ?? 'unknown',
                    'description' => $result['description'] ?? 'Нет описания'
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Исключение при запросе к Telegram API', [
                'method' => $method, 
                'params' => $params, 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
