<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TemplateCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SeedTemplateCategories extends Command
{
    /**
     * Имя и сигнатура консольной команды.
     *
     * @var string
     */
    protected $signature = 'templates:seed-categories';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Создание категорий шаблонов на основе структуры директорий';

    /**
     * Выполнение консольной команды.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Начинаем создание категорий шаблонов...');
        
        $templatesDir = public_path('templates');
        
        if (!File::exists($templatesDir)) {
            $this->error("Директория {$templatesDir} не существует!");
            return 1;
        }
        
        // Получаем все поддиректории в папке templates
        $directories = File::directories($templatesDir);
        $this->info("Найдено " . count($directories) . " директорий");
        
        // Добавляем общую категорию для шаблонов в корневой папке
        $generalCategory = TemplateCategory::firstOrCreate(
            ['slug' => 'general'],
            [
                'name' => 'Общие шаблоны',
                'directory_name' => 'general',
                'description' => 'Стандартные шаблоны документов',
                'sort_order' => 0,
                'is_active' => true
            ]
        );
        
        $this->info("Создана/обновлена категория: {$generalCategory->name}");
        
        // Проверяем существующие или создаем новые категории на основе директорий
        $sortOrder = 1;
        foreach ($directories as $directory) {
            $dirName = basename($directory);
            
            // Пропускаем служебные директории
            if (in_array($dirName, ['.', '..', 'general'])) {
                continue;
            }
            
            // Формируем имя категории
            $categoryName = Str::title(str_replace(['-', '_'], ' ', $dirName));
            
            // Создаем или обновляем категорию
            $category = TemplateCategory::firstOrCreate(
                ['directory_name' => $dirName],
                [
                    'name' => $categoryName,
                    'slug' => Str::slug($categoryName),
                    'description' => "Шаблоны документов категории {$categoryName}",
                    'sort_order' => $sortOrder++,
                    'is_active' => true
                ]
            );
            
            $this->info("Создана/обновлена категория: {$category->name}");
        }
        
        $this->info('Категории шаблонов успешно обновлены!');
        return 0;
    }
}
