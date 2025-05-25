<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    /**
     * Указывает, автоматически ли увеличивается ID.
     *
     * @var bool
     */
    public $incrementing = true;
    
    /**
     * Тип данных первичного ключа.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Имя первичного ключа таблицы.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'description',
        'category_id', 
        'image', 
        'template_path',  // Путь к HTML файлу шаблона
        'html_template',  // Добавляем поле для хранения HTML-контента шаблона
        'is_premium',
        'is_active',
        'fields',
        'sort_order',
        'document_type' // Добавляем поле для типа документа
    ];

    /**
     * Атрибуты, которые нужно приводить к определённому типу.
     *
     * @var array
     */
    protected $casts = [
        'fields' => 'array',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Категория шаблона
     */
    public function category()
    {
        return $this->belongsTo(TemplateCategory::class, 'category_id');
    }

    /**
     * Аксессор для получения URL изображения.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return asset('images/default-template.jpg');
    }

    /**
     * Аксессор для получения полного пути к файлу шаблона
     *
     * @return string
     */
    public function getTemplateFilePathAttribute()
    {
        $path = $this->template_path;
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
            return file_get_contents($this->template_file_path);
        }
        
        return '<div class="alert alert-danger">Шаблон не найден</div>';
    }

    /**
     * Связь с документами.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'certificate_template_id');
    }
    
    /**
     * Аксессор для получения типа документа из категории
     * 
     * @return string
     */
    public function getDocumentTypeAttribute()
    {
        // Если поле document_type заполнено у шаблона, используем его
        if (!empty($this->attributes['document_type'])) {
            return $this->attributes['document_type'];
        }
        
        // Иначе берем тип из категории
        if ($this->category) {
            return $this->category->document_type ?? 'certificate';
        }
        
        return 'certificate'; // Значение по умолчанию
    }
    
    /**
     * Аксессор для получения названия типа документа
     * 
     * @return string
     */
    public function getDocumentTypeLabelAttribute()
    {
        $types = TemplateCategory::getDocumentTypes();
        return $types[$this->document_type] ?? 'документ';
    }
    
    /**
     * Аксессор для получения названия документа с учетом его типа
     * 
     * @return string
     */
    public function getDocumentNameAttribute()
    {
        $typeLabels = [
            'certificate' => 'документ',
            'ticket' => 'Билет',
            'coupon' => 'Купон',
            'gift_card' => 'Подарочная карта',
            'invitation' => 'Приглашение',
            'membership' => 'Членская карта',
            'pass' => 'Пропуск',
            'other' => $this->name
        ];
        
        $type = $this->document_type;
        return $typeLabels[$type] ?? $this->name;
    }
}
