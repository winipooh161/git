<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Указываем, что ID не инкрементируется автоматически
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * Тип первичного ключа
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', // Добавляем поле id для возможности массового присвоения
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'company_name',
        'company_logo',
        'company_description',
        'company_address',
        'company_phone',
        'company_email',
        'company_website',
        'sticks_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'notification_preferences' => 'array',
        'phone_verification_expires_at' => 'datetime',
    ];

    /**
     * Роли пользователя
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * документы, созданные пользователем
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Папки, принадлежащие пользователю
     */
    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    /**
     * Проверка наличия роли у пользователя
     */
    public function hasRole($roleName): bool
    {
        foreach ($this->roles as $role) {
            if ($role->slug === $roleName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверка наличия любой из указанных ролей
     */
    public function hasAnyRole($roleNames): bool
    {
        if (is_string($roleNames)) {
            return $this->hasRole($roleNames);
        }

        foreach ($roleNames as $roleName) {
            if ($this->hasRole($roleName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Получить URL логотипа компании.
     *
     * @return string
     */
    public function getCompanyLogoUrlAttribute()
    {
        if ($this->company_logo) {
            return asset('storage/' . $this->company_logo);
        }
        
        return asset('images/default-logo.png');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        // Используем email, если он есть, иначе используем заглушку с телефоном
        return $this->email ?? $this->phone . '@example.com';
    }
    
    /**
     * Связь со всеми подписками пользователя
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }
    
    /**
     * Получить активную подписку пользователя
     */
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->latest();
    }
    
    /**
     * Получить текущий тариф пользователя
     */
    public function currentPlan()
    {
        $activeSubscription = $this->activeSubscription;
        
        if ($activeSubscription) {
            return $activeSubscription->subscriptionPlan;
        }
        
        // Если нет активной подписки, возвращаем базовый тариф Start
        return SubscriptionPlan::where('slug', 'start')->first();
    }
    
    /**
     * Проверить уровень подписки пользователя
     */
    public function hasSubscriptionLevel($level)
    {
        $levels = [
            'start' => 1,
            'vip' => 2,
            'premium' => 3,
            'individual' => 4
        ];
        
        $currentPlan = $this->currentPlan();
        if (!$currentPlan) return false;
        
        $currentLevel = $levels[$currentPlan->slug] ?? 0;
        $requiredLevel = $levels[$level] ?? 999;
        
        return $currentLevel >= $requiredLevel;
    }
    
    /**
     * Автоматически назначаем базовый тариф при создании пользователя
     */
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($user) {
            // Находим базовый тариф
            $startPlan = SubscriptionPlan::where('slug', 'start')->first();
            
            if ($startPlan) {
                // Создаем подписку для нового пользователя
                UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $startPlan->id,
                    'start_date' => now(),
                    'end_date' => null, // бессрочная
                    'is_active' => true
                ]);
            }
        });
    }

    /**
     * Проверяет, есть ли у пользователя достаточное количество стиков
     *
     * @param int $count Количество необходимых стиков
     * @return bool
     */
    public function hasEnoughSticks($count = 1)
    {
        return $this->sticks >= $count;
    }

    /**
     * Списывает стики у пользователя
     *
     * @param int $count Количество списываемых стиков
     * @return int Оставшееся количество стиков
     */
    public function useSticks($count = 1)
    {
        $count = max(1, intval($count)); // Гарантируем, что значение положительное и целое
    
        if ($this->sticks < $count) {
            throw new \Exception('Недостаточно стиков для списания');
        }
        
        $this->decrement('sticks', $count);
        $this->refresh(); // Обновляем модель из базы данных
        
        // Логируем операцию
        \Log::info("Списано {$count} стик(ов) у пользователя {$this->id}, осталось: {$this->sticks}");
        
        // Добавляем запись в историю использования стиков, если есть такая таблица
        if (class_exists('App\Models\StickUsage')) {
            \App\Models\StickUsage::create([
                'user_id' => $this->id,
                'amount' => -$count,
                'reason' => 'Создание сертификатов',
                'balance_after' => $this->sticks
            ]);
        }
        
        return $this->sticks;
    }

    /**
     * Добавляет стики пользователю
     *
     * @param int $count Количество добавляемых стиков
     * @return int Новое количество стиков
     */
    public function addSticks($count = 1)
    {
        $count = max(1, intval($count)); // Гарантируем, что значение положительное и целое
        
        $this->increment('sticks', $count);
        $this->refresh(); // Обновляем модель из базы данных
        
        // Логируем операцию
        \Log::info("Добавлено {$count} стик(ов) пользователю {$this->id}, всего: {$this->sticks}");
        
        // Добавляем запись в историю использования стиков, если есть такая таблица
        if (class_exists('App\Models\StickUsage')) {
            \App\Models\StickUsage::create([
                'user_id' => $this->id,
                'amount' => $count,
                'reason' => 'Пополнение баланса',
                'balance_after' => $this->sticks
            ]);
        }
        
        return $this->sticks;
    }

    /**
     * Уведомления пользователя
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Получает количество непрочитанных уведомлений
     * 
     * @return int
     */
    public function unreadNotificationsCount()
    {
        return $this->notifications()->whereNull('read_at')->count();
    }
}
