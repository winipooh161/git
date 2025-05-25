<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;

class ExistingUserSubscriptionsSeeder extends Seeder
{
    /**
     * Назначение базового тарифа всем существующим пользователям
     */
    public function run(): void
    {
        // Получаем базовый тариф
        $startPlan = SubscriptionPlan::where('slug', 'start')->first();
        
        if (!$startPlan) {
            $this->command->error('Базовый тариф не найден! Сначала запустите SubscriptionPlanSeeder.');
            return;
        }
        
        // Получаем всех пользователей без активной подписки
        $usersWithoutSubscription = User::whereDoesntHave('subscriptions')->get();
        
        $this->command->info("Найдено {$usersWithoutSubscription->count()} пользователей без подписки.");
        
        foreach ($usersWithoutSubscription as $user) {
            // Создаем подписку на базовый тариф
            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $startPlan->id,
                'start_date' => now(),
                'end_date' => null, // бессрочная
                'is_active' => true
            ]);
            
            $this->command->info("Базовый тариф назначен пользователю {$user->email}");
        }
        
        $this->command->info('Базовый тариф успешно назначен всем существующим пользователям.');
    }
}
