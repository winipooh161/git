<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixAnimationEffectsTable extends Command
{
    /**
     * Имя и сигнатура консольной команды.
     *
     * @var string
     */
    protected $signature = 'db:fix-animation-effects';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Исправляет структуру таблицы animation_effects, добавляя автоинкремент к полю id';

    /**
     * Выполнить консольную команду.
     */
    public function handle()
    {
        $this->info('Начинаем исправление таблицы animation_effects...');

        // Проверяем, существует ли таблица
        if (!Schema::hasTable('animation_effects')) {
            $this->error('Таблица animation_effects не существует!');
            return 1;
        }

        // Проверяем текущую структуру таблицы
        $this->info('Проверка текущей структуры таблицы...');
        $columns = DB::select('SHOW COLUMNS FROM animation_effects');
        $idColumn = array_filter($columns, function($column) {
            return $column->Field === 'id';
        });

        // Если колонка id существует и не является AUTO_INCREMENT
        if ($idColumn && !str_contains(strtoupper($idColumn[0]->Extra ?? ''), 'AUTO_INCREMENT')) {
            $this->info('Обнаружена колонка id без автоинкремента. Исправляем...');
            
            // Создаем временную таблицу с правильной структурой
            $this->info('Создание временной таблицы...');
            DB::statement('
                CREATE TABLE animation_effects_temp (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) UNIQUE NOT NULL,
                    type VARCHAR(255) NOT NULL,
                    particles JSON,
                    description TEXT NULL,
                    direction VARCHAR(255) NOT NULL,
                    speed VARCHAR(255) NOT NULL,
                    color VARCHAR(255) NULL,
                    size_min INT NOT NULL,
                    size_max INT NOT NULL,
                    quantity INT NOT NULL,
                    sort_order INT DEFAULT 0,
                    is_active BOOLEAN DEFAULT 1,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ');
            
            // Копируем данные, игнорируя значение id, чтобы оно было сгенерировано автоматически
            $this->info('Копирование данных в новую таблицу...');
            DB::statement('
                INSERT INTO animation_effects_temp 
                (name, slug, type, particles, description, direction, speed, color, size_min, size_max, quantity, sort_order, is_active, created_at, updated_at)
                SELECT name, slug, type, particles, description, direction, speed, color, size_min, size_max, quantity, sort_order, is_active, created_at, updated_at 
                FROM animation_effects
            ');
            
            // Удаляем старую таблицу и переименовываем временную
            $this->info('Замена старой таблицы на новую...');
            DB::statement('DROP TABLE animation_effects');
            DB::statement('RENAME TABLE animation_effects_temp TO animation_effects');
            
            $this->info('Исправление завершено успешно! Таблица animation_effects теперь имеет автоинкремент для id.');
        } else if ($idColumn && str_contains(strtoupper($idColumn[0]->Extra ?? ''), 'AUTO_INCREMENT')) {
            $this->info('Колонка id уже имеет AUTO_INCREMENT. Исправление не требуется.');
        } else {
            $this->error('Не удалось найти колонку id в таблице animation_effects.');
            return 1;
        }

        return 0;
    }
}
