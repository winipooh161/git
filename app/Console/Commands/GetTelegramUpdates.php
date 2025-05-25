<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramBotService;
use Illuminate\Support\Facades\Log;

class GetTelegramUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:updates {count=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получить последние обновления от Telegram Bot API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = "7861225545:AAGMlMVmNh-QkrtLVj8H3qpUsiDLp3XAj-I";
        $telegramService = new TelegramBotService($token);
        
        $count = $this->argument('count');
        
        $this->info("Получение последних {$count} обновлений от Telegram...");
        
        $updates = $telegramService->getUpdates($count);
        
        if ($updates['ok']) {
            $this->info("Получено " . count($updates['result']) . " обновлений");
            
            foreach ($updates['result'] as $update) {
                $this->outputUpdate($update);
            }
            
            if (empty($updates['result'])) {
                $this->warn("Обновлений не найдено. Возможно webhook настроен и получает все сообщения.");
            }
        } else {
            $this->error("Ошибка при получении обновлений: " . ($updates['description'] ?? 'Неизвестная ошибка'));
        }
    }
    
    /**
     * Вывести информацию об обновлении
     */
    private function outputUpdate($update)
    {
        $this->line("----------------");
        $this->info("ID обновления: " . $update['update_id']);
        
        if (isset($update['message'])) {
            $message = $update['message'];
            $chat = $message['chat'];
            
            $this->line("Чат ID: " . $chat['id']);
            $this->line("От: " . ($chat['first_name'] ?? '') . " " . ($chat['last_name'] ?? '') . 
                       " (" . ($chat['username'] ?? 'без имени пользователя') . ")");
            
            if (isset($message['text'])) {
                $this->line("Текст: " . $message['text']);
            }
            
            $this->line("Дата: " . date('Y-m-d H:i:s', $message['date']));
        }
        
        $this->line("----------------");
    }
}
