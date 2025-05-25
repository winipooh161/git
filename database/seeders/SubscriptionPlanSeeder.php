<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Start',
                'slug' => 'start',
                'description' => 'Базовый тариф, доступный всем пользователям',
                'price' => 0,
                'duration_days' => null, // бессрочный
                'features' => json_encode([
                    'Создание до 3 документов',
                    'Базовые шаблоны',
                    'Ограниченная техподдержка'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'VIP',
                'slug' => 'vip',
                'description' => 'Тариф VIP с расширенными возможностями',
                'price' => 990,
                'duration_days' => 30, // 1 месяц
                'features' => json_encode([
                    'Создание до 10 документов',
                    'Расширенный набор шаблонов',
                    'Приоритетная техподдержка'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Премиум тариф для активных пользователей',
                'price' => 1990,
                'duration_days' => 30, // 1 месяц
                'features' => json_encode([
                    'Неограниченное количество документов',
                    'Доступ ко всем шаблонам',
                    'Экспресс-поддержка',
                    'Аналитика использования'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Individual',
                'slug' => 'individual',
                'description' => 'Индивидуальный тариф с персональными настройками',
                'price' => 9990,
                'duration_days' => 365, // 1 год
                'features' => json_encode([
                    'Все функции Premium',
                    'Персональный менеджер',
                    'Разработка индивидуальных шаблонов',
                    'Интеграция с вашими системами'
                ]),
                'is_active' => true
            ],
        ];
        
        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
