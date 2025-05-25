<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    protected $telegramService;
    protected $token = "7861225545:AAGMlMVmNh-QkrtLVj8H3qpUsiDLp3XAj-I";

    public function __construct()
    {
        $this->telegramService = new TelegramBotService($this->token);
    }

    /**
     * Отображение страницы настроек и статуса Telegram бота
     */
    public function index()
    {
        try {
            // Получаем информацию о боте
            $botInfo = $this->telegramService->getMe();
            
            // Получаем информацию о webhook
            $webhookInfo = $this->telegramService->getWebhookInfo();
            
            // Получаем последние логи бота
            $logs = $this->getLatestLogs();
            
            return view('admin.telegram.index', [
                'botInfo' => $botInfo,
                'webhookInfo' => $webhookInfo,
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            return view('admin.telegram.index', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Установка вебхука
     */
    public function setWebhook(Request $request)
    {
        try {
            $url = route('telegram.webhook');
            $result = $this->telegramService->setWebhook($url);
            
            Log::info('Установка вебхука из админ-панели', [
                'url' => $url,
                'result' => $result
            ]);
            
            if ($result['ok']) {
                return redirect()->route('admin.telegram.index')
                    ->with('success', 'Вебхук успешно установлен');
            } else {
                return redirect()->route('admin.telegram.index')
                    ->with('error', 'Ошибка установки вебхука: ' . ($result['description'] ?? 'Неизвестная ошибка'));
            }
        } catch (\Exception $e) {
            Log::error('Ошибка установки вебхука из админ-панели', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.telegram.index')
                ->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Удаление вебхука
     */
    public function deleteWebhook()
    {
        try {
            $result = $this->telegramService->deleteWebhook();
            
            Log::info('Удаление вебхука из админ-панели', [
                'result' => $result
            ]);
            
            if ($result['ok']) {
                return redirect()->route('admin.telegram.index')
                    ->with('success', 'Вебхук успешно удален');
            } else {
                return redirect()->route('admin.telegram.index')
                    ->with('error', 'Ошибка удаления вебхука: ' . ($result['description'] ?? 'Неизвестная ошибка'));
            }
        } catch (\Exception $e) {
            Log::error('Ошибка удаления вебхука из админ-панели', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.telegram.index')
                ->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Отправка тестового сообщения
     */
    public function sendTestMessage(Request $request)
    {
        try {
            $request->validate([
                'chat_id' => 'required|numeric',
                'message' => 'required|string|max:255'
            ]);

            $result = $this->telegramService->sendMessage(
                $request->chat_id,
                $request->message
            );
            
            Log::info('Отправка тестового сообщения из админ-панели', [
                'chat_id' => $request->chat_id,
                'message' => $request->message,
                'result' => $result
            ]);
            
            if ($result['ok']) {
                return redirect()->route('admin.telegram.index')
                    ->with('success', 'Тестовое сообщение успешно отправлено');
            } else {
                return redirect()->route('admin.telegram.index')
                    ->with('error', 'Ошибка отправки сообщения: ' . ($result['description'] ?? 'Неизвестная ошибка'));
            }
        } catch (\Exception $e) {
            Log::error('Ошибка отправки тестового сообщения из админ-панели', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.telegram.index')
                ->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Получение последних логов телеграм бота
     */
    private function getLatestLogs($lines = 50)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!file_exists($logPath)) {
            return [];
        }
        
        // Получить последние строки из файла лога
        $file = new \SplFileObject($logPath, 'r');
        $file->seek(PHP_INT_MAX); // Переходим в конец файла
        $totalLines = $file->key(); // Получаем общее количество строк
        
        $telegramLogs = [];
        
        // Читаем последние строки из файла
        $start = max(0, $totalLines - 1000); // Берем последние 1000 строк для поиска
        $file->seek($start);
        
        while (!$file->eof()) {
            $line = $file->current();
            if (strpos($line, 'Telegram') !== false || strpos($line, 'telegram') !== false) {
                $telegramLogs[] = $line;
                
                // Если собрали достаточно логов, останавливаемся
                if (count($telegramLogs) >= $lines) {
                    break;
                }
            }
            $file->next();
        }
        
        return $telegramLogs;
    }
}
