<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DiagnoseCertificateFolders extends Command
{
    protected $signature = 'diagnose:certificate-folders';
    protected $description = 'Диагностика и исправление связей между документами и папками';

    public function handle()
    {
        $this->info('Начинаем диагностику связей между документами и папками...');

        // 1. Проверка структуры таблиц
        $this->info('Проверка структуры таблиц...');
        
        if (!Schema::hasTable('certificates')) {
            $this->error('Таблица certificates не существует!');
            return 1;
        }
        
        if (!Schema::hasTable('certificate_folders')) {
            $this->error('Таблица certificate_folders не существует!');
            return 1;
        }
        
        if (!Schema::hasTable('certificate_folder')) {
            $this->error('Таблица связей certificate_folder не существует!');
            $this->info('Создание таблицы связей...');
            
            Schema::create('certificate_folder', function ($table) {
                $table->id();
                $table->unsignedBigInteger('certificate_id');
                $table->unsignedBigInteger('certificate_folder_id');
                $table->timestamps();
                
                $table->foreign('certificate_id')
                    ->references('id')
                    ->on('certificates')
                    ->onDelete('cascade');
                    
                $table->foreign('certificate_folder_id')
                    ->references('id')
                    ->on('certificate_folders')
                    ->onDelete('cascade');
                    
                $table->unique(['certificate_id', 'certificate_folder_id']);
            });
            
            $this->info('Таблица связей создана успешно!');
        }
        
        // 2. Проверка целостности данных
        $this->info('Проверка целостности данных...');
        
        // Проверяем корректность внешних ключей
        $invalidLinks = DB::table('certificate_folder')
            ->leftJoin('certificates', 'certificate_folder.certificate_id', '=', 'certificates.id')
            ->leftJoin('certificate_folders', 'certificate_folder.certificate_folder_id', '=', 'certificate_folders.id')
            ->whereNull('certificates.id')
            ->orWhereNull('certificate_folders.id')
            ->count();
            
        if ($invalidLinks > 0) {
            $this->warn("Найдено {$invalidLinks} некорректных связей. Удаление...");
            
            // Удаляем некорректные связи
            $deleted = DB::table('certificate_folder')
                ->leftJoin('certificates', 'certificate_folder.certificate_id', '=', 'certificates.id')
                ->leftJoin('certificate_folders', 'certificate_folder.certificate_folder_id', '=', 'certificate_folders.id')
                ->whereNull('certificates.id')
                ->orWhereNull('certificate_folders.id')
                ->delete();
                
            $this->info("Удалено {$deleted} некорректных записей.");
        } else {
            $this->info('Все связи корректны.');
        }
        
        $this->info('Диагностика завершена успешно!');
        return 0;
    }
}
