<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Проверяем, существует ли таблица
        if (Schema::hasTable('certificate_folder')) {
            // Получаем информацию о столбце id
            $columns = DB::select("SHOW COLUMNS FROM certificate_folder WHERE Field = 'id'");
            
            // Проверяем, есть ли столбец id и является ли он AUTO_INCREMENT
            if (empty($columns) || strpos($columns[0]->Extra ?? '', 'auto_increment') === false) {
                // Создаем временную таблицу с правильной структурой
                Schema::create('certificate_folder_temp', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('certificate_id');
                    $table->unsignedBigInteger('folder_id');
                    $table->timestamps();
                    
                    $table->foreign('certificate_id')->references('id')->on('certificates')->onDelete('cascade');
                    $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
                });
                
                // Копируем данные из старой таблицы
                if (Schema::hasColumn('certificate_folder', 'id')) {
                    // Копируем данные с сохранением id
                    DB::statement('INSERT INTO certificate_folder_temp (id, certificate_id, folder_id, created_at, updated_at) 
                                  SELECT id, certificate_id, folder_id, created_at, updated_at FROM certificate_folder');
                } else {
                    // Копируем данные без id (будут сгенерированы автоматически)
                    DB::statement('INSERT INTO certificate_folder_temp (certificate_id, folder_id, created_at, updated_at) 
                                  SELECT certificate_id, folder_id, COALESCE(created_at, NOW()), COALESCE(updated_at, NOW()) FROM certificate_folder');
                }
                
                // Удаляем старую таблицу
                Schema::drop('certificate_folder');
                
                // Переименовываем временную таблицу в правильное имя
                Schema::rename('certificate_folder_temp', 'certificate_folder');
                
                // Сбрасываем автоинкрементное значение, начиная с максимального id + 1
                $maxId = DB::table('certificate_folder')->max('id') ?? 0;
                DB::statement("ALTER TABLE certificate_folder AUTO_INCREMENT = " . ($maxId + 1));
            }
        } else {
            // Если таблицы нет, создаем ее с нуля
            Schema::create('certificate_folder', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('certificate_id');
                $table->unsignedBigInteger('folder_id');
                $table->timestamps();
                
                $table->foreign('certificate_id')->references('id')->on('certificates')->onDelete('cascade');
                $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // В случае отката мы ничего не делаем, так как это исправление структуры
    }
};
