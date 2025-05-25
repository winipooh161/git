<?php

// Загружаем Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Начинаем исправление таблицы certificates...\n";

try {
    // Проверяем текущее состояние таблицы
    $tableInfo = DB::select("SHOW COLUMNS FROM certificates WHERE Field = 'id'");
    
    echo "Текущая структура поля id: " . json_encode($tableInfo[0]) . "\n";
    
    // Отключаем проверку внешних ключей
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
    
    // Шаг 1: Удаляем первичный ключ
    echo "Удаляем первичный ключ...\n";
    DB::statement('ALTER TABLE certificates DROP PRIMARY KEY');
    
    // Шаг 2: Изменяем колонку id, добавляем AUTO_INCREMENT и делаем её первичным ключом
    echo "Добавляем AUTO_INCREMENT к полю id и устанавливаем его как PRIMARY KEY...\n";
    DB::statement('ALTER TABLE certificates MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    
    // Шаг 3: Сбрасываем AUTO_INCREMENT в начало, если нужно
    echo "Сбрасываем AUTO_INCREMENT до 1...\n";
    DB::statement('ALTER TABLE certificates AUTO_INCREMENT = 1');
    
    // Включаем обратно проверку внешних ключей
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    
    // Проверяем обновленное состояние таблицы
    $updatedTableInfo = DB::select("SHOW COLUMNS FROM certificates WHERE Field = 'id'");
    echo "Обновленная структура поля id: " . json_encode($updatedTableInfo[0]) . "\n";
    
    echo "Исправление таблицы certificates успешно выполнено!\n";
    
} catch (Exception $e) {
    echo "Произошла ошибка: " . $e->getMessage() . "\n";
    echo "Трассировка ошибки: " . $e->getTraceAsString() . "\n";
    
    // В случае ошибки включаем обратно проверку внешних ключей
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    } catch (Exception $e2) {
        echo "Ошибка при восстановлении FOREIGN_KEY_CHECKS: " . $e2->getMessage() . "\n";
    }
}
