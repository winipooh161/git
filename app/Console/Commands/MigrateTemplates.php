<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\File;

class MigrateTemplates extends Command
{
    protected $signature = 'templates:migrate';
    protected $description = 'Перемещает шаблоны в новую структуру каталогов и обновляет пути в базе данных';

    public function handle()
    {
        $this->info('Начинаем миграцию шаблонов...');

        // Создаем директории для категорий, если их нет
        $categories = ['general', 'business', 'holiday', 'special'];
        foreach ($categories as $category) {
            $path = public_path("templates/{$category}");
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
                $this->info("Создана директория: {$category}");
            }
        }

        // Получаем все шаблоны из базы данных
        $templates = CertificateTemplate::all();
        $this->info("Найдено {$templates->count()} шаблонов для обработки");

        // Перемещаем шаблоны в соответствующие категории и обновляем пути
        foreach ($templates as $template) {
            // Обрабатываем только шаблоны, которые находятся в корневой директории
            if (strpos($template->template_path, '/') === false || substr_count($template->template_path, '/') <= 1) {
                $oldPath = public_path($template->template_path);
                $fileName = basename($template->template_path);
                $newRelativePath = "templates/general/{$fileName}";
                $newPath = public_path($newRelativePath);

                // Проверяем, существует ли файл
                if (File::exists($oldPath)) {
                    // Копируем файл в новую директорию
                    File::copy($oldPath, $newPath);
                    
                    // Обновляем запись в базе данных
                    $template->update(['template_path' => $newRelativePath]);
                    
                    $this->info("Перемещен шаблон: {$fileName} → general/{$fileName}");
                } else {
                    $this->warn("Файл не найден: {$oldPath}");
                }
            }
        }

        $this->info('Миграция шаблонов завершена.');
        return 0;
    }
}
