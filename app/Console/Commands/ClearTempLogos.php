<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ClearTempLogos extends Command
{
    /**
     * Имя и сигнатура консольной команды.
     *
     * @var string
     */
    protected $signature = 'temp:clear-logos';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Очистка временных логотипов старше 24 часов';

    /**
     * Выполнение консольной команды.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Начинаем очистку временных логотипов...');
        
        // Получаем все файлы из директории временных логотипов
        $files = Storage::disk('public')->files('temp/logos');
        $count = 0;
        
        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(Storage::disk('public')->lastModified($file));
            
            // Удаляем файлы старше 24 часов
            if ($lastModified->diffInHours(now()) >= 24) {
                Storage::disk('public')->delete($file);
                $count++;
            }
        }
        
        $this->info("Очистка завершена. Удалено {$count} временных файлов.");
        
        return 0;
    }
}
