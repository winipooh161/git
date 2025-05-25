<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_days',
        'features',
        'is_active',
        'sticks_amount', // Добавляем количество стиков
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'sticks_amount' => 'integer',
    ];

    /**
     * Связь с пользовательскими подписками
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Только активные подписки на этот тариф
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(UserSubscription::class)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            });
    }
    
    /**
     * Получить пользователей с этим тарифом
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, UserSubscription::class, 
            'subscription_plan_id', 'id', 'id', 'user_id'
        )->where('user_subscriptions.is_active', true);
    }

    /**
     * Возвращает количество стиков, выдаваемых за этот тариф
     * 
     * @return int
     */
    public function getSticksAmountAttribute()
    {
        if (isset($this->attributes['sticks_amount'])) {
            return $this->attributes['sticks_amount'];
        }

        // Возвращаем количество стиков по умолчанию в зависимости от тарифа
        switch ($this->slug) {
            case 'start':
                return 5; // базовый тариф - 5 стиков
            case 'standard':
                return 20; // стандартный тариф - 20 стиков 
            case 'premium':
                return 50; // премиум тариф - 50 стиков
            case 'vip':
                return 100; // VIP тариф - 100 стиков
            default:
                return 5; // По умолчанию
        }
    }
}
