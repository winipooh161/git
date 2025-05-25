<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;

class ManageSubscriptions extends Command
{
    /**
     * Имя и сигнатура консольной команды.
     *
     * @var string
     */
    protected $signature = 'subscriptions:manage {--deactivate-expired : Деактивировать истекшие подписки}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Управление подписками пользователей';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('deactivate-expired')) {
            $this->deactivateExpiredSubscriptions();
        } else {
            $this->info('Укажите действие для выполнения. Например: --deactivate-expired');
        }
    }

    /**
     * Деактивировать истекшие подписки и назначить базовый тариф.
     */
    protected function deactivateExpiredSubscriptions()
    {
        // Находим истекшие подписки
        $expiredSubscriptions = UserSubscription::where('is_active', true)
            ->where('end_date', '<', Carbon::now())
            ->get();
            
        if ($expiredSubscriptions->isEmpty()) {
            $this->info('Нет истекших подписок.');
            return;
        }
        
        $this->info("Найдено {$expiredSubscriptions->count()} истекших подписок.");
        
        // Получаем базовый тариф
        $startPlan = SubscriptionPlan::where('slug', 'start')->first();
        
        if (!$startPlan) {
            $this->error('Базовый тариф не найден!');
            return;
        }
        
        foreach ($expiredSubscriptions as $subscription) {
            // Деактивируем истекшую подписку
            $subscription->update(['is_active' => false]);
            
            // Создаем новую подписку на базовый тариф
            UserSubscription::create([
                'user_id' => $subscription->user_id,
                'subscription_plan_id' => $startPlan->id,
                'start_date' => now(),
                'end_date' => null,
                'is_active' => true,
            ]);
            
            $this->info("Подписка #{$subscription->id} деактивирована, назначен базовый тариф пользователю #{$subscription->user_id}");
        }
        
        $this->info('Обработка истекших подписок завершена.');
    }
}
