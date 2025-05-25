<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'read_at',
        'related_id',
        'related_type'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Константы для типов уведомлений
     */
    const TYPE_CERTIFICATE_ISSUED = 'certificate_issued';
    const TYPE_CERTIFICATE_RECEIVED = 'certificate_received';
    const TYPE_CERTIFICATE_USED = 'certificate_used';
    const TYPE_CERTIFICATE_EXPIRED = 'certificate_expired';
    const TYPE_SYSTEM = 'system';

    /**
     * Boot метод для автоматического создания UUID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($notification) {
            if (!isset($notification->uuid)) {
                $notification->uuid = Str::uuid()->toString();
            }
        });
    }

    /**
     * Получает пользователя, которому принадлежит уведомление
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получает связанную модель (полиморфная связь)
     */
    public function related()
    {
        return $this->morphTo();
    }

    /**
     * Помечает уведомление как прочитанное
     *
     * @return $this
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }

        return $this;
    }

    /**
     * Scope для получения только непрочитанных уведомлений
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Проверяет, прочитано ли уведомление
     *
     * @return bool
     */
    public function isRead()
    {
        return $this->read_at !== null;
    }
    
    /**
     * Получает иконку в зависимости от типа уведомления
     *
     * @return string
     */
    public function getIconClass()
    {
        switch ($this->type) {
            case self::TYPE_CERTIFICATE_ISSUED:
                return 'fa-certificate text-primary';
            case self::TYPE_CERTIFICATE_RECEIVED:
                return 'fa-gift text-success';
            case self::TYPE_CERTIFICATE_USED:
                return 'fa-check-circle text-info';
            case self::TYPE_CERTIFICATE_EXPIRED:
                return 'fa-exclamation-triangle text-warning';
            case self::TYPE_SYSTEM:
                return 'fa-bell text-secondary';
            default:
                return 'fa-bell text-secondary';
        }
    }
    
    /**
     * Получает CSS класс для бейджа уведомления в зависимости от типа
     * 
     * @return string
     */
    public function getBadgeClass()
    {
        switch ($this->type) {
            case self::TYPE_CERTIFICATE_ISSUED:
                return 'bg-primary';
            case self::TYPE_CERTIFICATE_RECEIVED:
                return 'bg-success';
            case self::TYPE_CERTIFICATE_USED:
                return 'bg-info';
            case self::TYPE_CERTIFICATE_EXPIRED:
                return 'bg-warning';
            case self::TYPE_SYSTEM:
                return 'bg-secondary';
            default:
                return 'bg-secondary';
        }
    }
}
