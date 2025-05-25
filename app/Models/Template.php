<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'file_path',
        'thumbnail',
        'is_active',
        'is_premium',
        'settings',
        'fields',
        'options',
        'sort_order',
    ];

    /**
     * Атрибуты, которые нужно приводить к определённому типу.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'settings' => 'array',
        'fields' => 'array',
        'options' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Получить категорию, к которой относится этот шаблон.
     */
    public function category()
    {
        return $this->belongsTo(TemplateCategory::class, 'category_id');
    }

    /**
     * документы, созданные на основе этого шаблона.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Scope для получения только активных шаблонов.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для сортировки шаблонов по порядку.
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Аксессор для получения полного пути к файлу шаблона
     *
     * @return string
     */
    public function getTemplateFilePathAttribute()
    {
        // Учитываем, что файл теперь имеет расширение .php
        $path = $this->file_path;
        if (!str_ends_with($path, '.php')) {
            $path = str_replace('.html', '.php', $path);
        }
        return public_path($path);
    }

    /**
     * Аксессор для получения HTML содержимого шаблона
     *
     * @return string
     */
    public function getHtmlTemplateAttribute()
    {
        if (file_exists($this->template_file_path)) {
            // Чтение PHP файла как содержимое, без его интерпретации
            return file_get_contents($this->template_file_path);
        }
        
        return '<div class="alert alert-danger">Шаблон не найден</div>';
    }
}
