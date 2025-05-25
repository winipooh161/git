<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'start_date',
        'end_date',
        'is_active',
        'payment_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с тарифом
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Проверка, истек ли срок подписки
     */
    public function isExpired()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Количество оставшихся дней подписки
     */
    public function daysLeft()
    {
        if (!$this->end_date) {
            return null; // Бессрочная подписка
        }
        
        return max(0, now()->diffInDays($this->end_date, false));
    }
}
