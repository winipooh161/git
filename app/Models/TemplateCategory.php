<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateCategory extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'directory_name',
        'description',
        'sort_order',
        'is_active',
        'document_type' // Добавляем новое поле
    ];

    /**
     * Атрибуты, которые нужно приводить к определённому типу.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
    
    /**
     * Получить все шаблоны, принадлежащие к данной категории.
     */
    public function templates()
    {
        return $this->hasMany(Template::class, 'category_id');
    }

    /**
     * Получить список доступных типов документов
     * 
     * @return array
     */
    public static function getDocumentTypes()
    {
        return [
            'certificate' => 'документ',
            'ticket' => 'Билет',
            'coupon' => 'Купон',
            'gift_card' => 'Подарочная карта',
            'invitation' => 'Приглашение',
            'membership' => 'Членская карта',
            'pass' => 'Пропуск',
            'other' => 'Другое'
        ];
    }
    
    /**
     * Получить название типа документа
     * 
     * @return string
     */
    public function getDocumentTypeLabelAttribute()
    {
        $types = self::getDocumentTypes();
        return $types[$this->document_type] ?? 'Другое';
    }
    
    /**
     * Получить категорию "Сертификаты" или создать ее если она не существует 
     * 
     * @return \App\Models\TemplateCategory
     */
    public static function getDefaultCategory()
    {
        $category = self::firstOrCreate(
            ['slug' => 'certificates'],
            [
                'name' => 'Сертификаты',
                'directory_name' => 'certificates',
                'description' => 'Стандартные сертификаты',
                'sort_order' => 1,
                'is_active' => true,
                'document_type' => 'certificate'
            ]
        );
        
        return $category;
    }
}
