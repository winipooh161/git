<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'parent_id',
        'position'
    ];

    /**
     * Атрибуты для скрытия из сериализации.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];

    /**
     * Отношения для автоматической загрузки.
     *
     * @var array<int, string>
     */
    protected $with = ['children'];

    /**
     * Получить пользователя, которому принадлежит папка.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить родительскую папку, если есть.
     */
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Получить дочерние папки.
     */
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id')
            ->orderBy('position');
    }

    /**
     * Проверить, является ли папка корневой.
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    /**
     * Получить полный путь к папке в виде массива названий папок.
     */
    public function getPathArray()
    {
        $path = [$this->name];
        $current = $this;
        
        while ($parent = $current->parent) {
            $path[] = $parent->name;
            $current = $parent;
        }
        
        return array_reverse($path);
    }

    /**
     * Получить полный путь к папке в виде строки.
     */
    public function getPathString($separator = ' / ')
    {
        return implode($separator, $this->getPathArray());
    }

    /**
     * Получить сертификаты, находящиеся в папке
     */
    public function certificates()
    {
        return $this->belongsToMany(Certificate::class, 'certificate_folder');
    }

    /**
     * Получить все доступные папки пользователя с учетом вложенности
     */
    public static function getFolderTree($userId, $parentId = null, $level = 0)
    {
        $folders = self::where('user_id', $userId)
                      ->where('parent_id', $parentId)
                      ->orderBy('position')
                      ->get();
        
        $result = [];
        
        foreach ($folders as $folder) {
            $folder->level = $level;
            $result[] = $folder;
            
            // Рекурсивно получаем дочерние папки
            $children = self::getFolderTree($userId, $folder->id, $level + 1);
            $result = array_merge($result, $children);
        }
        
        return $result;
    }
}
