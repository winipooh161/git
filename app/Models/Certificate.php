<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    // Явно указываем использование автоинкремента
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'certificate_number',
        'uuid',
        'user_id',
        'certificate_template_id',
        'recipient_name',
        'recipient_email',
        'recipient_phone', // Добавляем поле для номера телефона получателя
        'amount',
        'message',
        'company_logo',
        'cover_image',
        'animation_effect_id', // Добавляем новое поле
        'custom_fields',
        'valid_from',
        'valid_until',
        'status',
        'used_at',
        'parent_id', // ID родительского сертификата
        'batch_size', // Общее количество сертификатов в серии
        'batch_number', // Номер в серии (для дочерних сертификатов)
        'is_batch_parent' // Флаг, указывающий, что это родительский сертификат серии
    ];

    // Даты для автоматического преобразования
    protected $dates = [
        'valid_from',
        'valid_until',
        'used_at',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'used_at' => 'datetime',
        'is_percent' => 'boolean',
    ];

    /**
     * Константы статусов сертификата
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELED = 'canceled';
    const STATUS_SERIES = 'series'; // Новый статус для сертификатов-серий

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Генерируем UUID при создании
        static::creating(function ($certificate) {
            $certificate->uuid = (string) Str::uuid();
            
            // Автоматически устанавливаем статус "series" для сертификатов-проводников
            if ($certificate->is_batch_parent && $certificate->batch_size > 1) {
                $certificate->status = self::STATUS_SERIES;
            }
        });
        
        // Обновляем счетчик активированных копий при изменении сертификата
        static::updated(function ($certificate) {
            // Только для дочерних сертификатов
            if ($certificate->parent_id && 
                ($certificate->isDirty('recipient_name') || 
                 $certificate->isDirty('recipient_email') || 
                 $certificate->isDirty('recipient_phone'))) {
                
                // Получаем родительский сертификат
                $parentCertificate = static::find($certificate->parent_id);
                if ($parentCertificate) {
                    // Получаем количество активированных копий
                    $activatedCount = static::where('parent_id', $parentCertificate->id)
                        ->where(function($query) {
                            $query->whereNotNull('recipient_name')
                                ->orWhereNotNull('recipient_email')
                                ->orWhereNotNull('recipient_phone');
                        })
                        ->count();
                    
                    // Обновляем счетчик в родительском сертификате
                    $parentCertificate->activated_copies_count = $activatedCount;
                    $parentCertificate->save();
                }
            }
        });
    }

    /**
     * Пользователь, создавший документ
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Шаблон, на основе которого создан документ
     */
    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    /**
     * Получить URL изображения обложки документа.
     *
     * @return string
     */
    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        
        // Возвращаем URL изображения шаблона, если обложка не задана
        return $this->template && $this->template->image 
            ? asset('storage/' . $this->template->image) 
            : asset('images/certificate-placeholder.jpg');
    }

    /**
     * Папки, в которых находится документ
     */
    public function folders()
    {
        return $this->belongsToMany(Folder::class, 'certificate_folder', 'certificate_id', 'folder_id')
            ->withTimestamps();
    }

    /**
     * Анимационный эффект документа - исправляем отношение
     */
    public function animationEffect()
    {
        return $this->belongsTo(AnimationEffect::class, 'animation_effect_id');
    }

    /**
     * Связь с деталями билета в кино
     */
    public function ticketDetail()
    {
        return $this->hasOne(TicketDetail::class);
    }
    
    /**
     * Проверяет, является ли документ билетом в кино
     *
     * @return bool
     */
    public function isMovieTicket()
    {
        if ($this->template && 
            ($this->template->document_type === 'ticket' || 
             ($this->template->category && $this->template->category->document_type === 'ticket'))) {
            return true;
        }
        
        return false;
    }

    /**
     * Проверяет, был ли сертификат уже кем-то получен/активирован
     *
     * @return bool
     */
    public function isClaimed()
    {
        // Сертификаты серии (родительские) ВСЕГДА считаются неполученными,
        // что позволяет другим пользователям получать копии
        if ($this->is_batch_parent || $this->status === self::STATUS_SERIES) {
            // Дополнительное логирование для отладки
            if (!empty($this->recipient_name) || !empty($this->recipient_email) || !empty($this->recipient_phone)) {
                \Log::error("КРИТИЧЕСКАЯ ОШИБКА: Сертификат-серия {$this->id} имеет данные получателя!", [
                    'recipient_name' => $this->recipient_name,
                    'recipient_email' => $this->recipient_email,
                    'recipient_phone' => $this->recipient_phone
                ]);
                
                // Обновляем валидность дынных
                $this->recipient_name = null;
                $this->recipient_email = null;
                $this->recipient_phone = null;
                
                // Сохраняем очищенные данные
                try {
                    $this->save();
                    \Log::info("Автоматически очищены данные получателя в сертификате-серии {$this->id}");
                } catch (\Exception $e) {
                    \Log::error("Не удалось очистить данные получателя в сертификате-серии: " . $e->getMessage());
                }
            }
            return false;
        }
        
        // Для обычных сертификатов и копий из серии - проверка на наличие получателя
        return !empty($this->recipient_name) || !empty($this->recipient_email) || !empty($this->recipient_phone);
    }

    /**
     * Проверяет, является ли сертификат серией (проводником)
     *
     * @return bool
     */
    public function isSeries()
    {
        return $this->is_batch_parent && $this->batch_size > 1;
    }

    /**
     * Получить данные для шаблона с учетом типа документа
     *
     * @return array
     */
    public function getTemplateData()
    {
        $data = [
            'certificate_number' => $this->certificate_number,
            'recipient_name' => $this->recipient_name,
            'recipient_email' => $this->recipient_email,
            'recipient_phone' => $this->recipient_phone,
            'amount' => $this->amount, // Передаем числовое значение
            'is_percent' => $this->is_percent, // Явно передаем флаг процентного типа
            'message' => $this->message,
            'company_logo' => $this->company_logo ? asset('storage/' . $this->company_logo) : null,
            'company_name' => $this->user->company_name ?? config('app.name'),
            'valid_from' => $this->formatDate($this->valid_from),
            'valid_until' => $this->formatDate($this->valid_until),
        ];
        
        // Добавляем custom_fields если они есть
        if (!empty($this->custom_fields) && is_array($this->custom_fields)) {
            $data = array_merge($data, $this->custom_fields);
        }
        
        // Если это билет в кино и есть детали билета, добавляем их
        if ($this->isMovieTicket() && $this->ticketDetail) {
            $data = array_merge($data, $this->ticketDetail->toTemplateData());
        }
        
        return $data;
    }
    
    /**
     * Форматирует дату с проверкой типа
     *
     * @param mixed $date Дата для форматирования
     * @param string $format Формат вывода
     * @return string Отформатированная дата
     */
    public function formatDate($date, $format = 'd.m.Y')
    {
        if (is_string($date)) {
            return $date;
        } elseif ($date instanceof \Carbon\Carbon) {
            return $date->format($format);
        } elseif (is_null($date)) {
            return '---';
        } else {
            return (string) $date;
        }
    }

    /**
     * Получить отформатированный номинал документа с учетом типа (деньги или процент)
     *
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        if ($this->is_percent) {
            return $this->amount . '%';
        } else {
            return number_format($this->amount, 0, '.', ' ') . ' ₽';
        }
    }

    /**
     * Получить родительский сертификат (если это копия из серии)
     */
    public function parent()
    {
        return $this->belongsTo(Certificate::class, 'parent_id');
    }

    /**
     * Получить копии этого сертификата (если это родительский сертификат серии)
     */
    public function copies()
    {
        return $this->hasMany(Certificate::class, 'parent_id');
    }

    /**
     * Получить количество активированных копий
     */
    public function getActivatedCopiesCountAttribute()
    {
        // Если есть значение в базе, используем его
        if (isset($this->attributes['activated_copies_count']) && is_numeric($this->attributes['activated_copies_count'])) {
            return (int)$this->attributes['activated_copies_count'];
        }
        
        // Если нет или это не числовое значение, вычисляем
        if (!$this->is_batch_parent) {
            return 0;
        }
        
        return $this->copies()->whereNotNull('recipient_name')->count();
    }

    /**
     * Получить прогресс активации в процентах
     */
    public function getActivationProgressPercentAttribute()
    {
        if (!$this->is_batch_parent || $this->batch_size <= 0) {
            return 0;
        }
        
        return round(($this->activated_copies_count / $this->batch_size) * 100);
    }

    /**
     * Получить строковое представление прогресса активации
     */
    public function getActivationProgressStringAttribute()
    {
        if (!$this->is_batch_parent) {
            return '';
        }
        
        return $this->activated_copies_count . '/' . $this->batch_size;
    }

    /**
     * Получить человекочитаемое представление статуса
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_USED => 'Использован',
            self::STATUS_EXPIRED => 'Истек',
            self::STATUS_CANCELED => 'Отменен',
            self::STATUS_SERIES => 'Серия', // Метка для нового статуса
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Получить класс цвета для статуса
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_USED => 'secondary',
            self::STATUS_EXPIRED => 'warning',
            self::STATUS_CANCELED => 'danger',
            self::STATUS_SERIES => 'primary', // Цвет для статуса "серия"
        ];

        return $colors[$this->status] ?? 'info';
    }
}
