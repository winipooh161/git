<?php

namespace Database\Seeders;

use App\Models\StickyPackage;
use App\Models\StickySubscription;
use Illuminate\Database\Seeder;

class StickyPackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем пакеты стиков
        StickyPackage::create([
            'name' => 'Start',
            'description' => 'Начальный пакет стиков для малого бизнеса',
            'amount' => 20,
            'price' => 100,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        StickyPackage::create([
            'name' => 'Growth',
            'description' => 'Пакет для растущего бизнеса',
            'amount' => 100,
            'price' => 450,
            'is_active' => true,
            'sort_order' => 20,
        ]);

        StickyPackage::create([
            'name' => 'Pro',
            'description' => 'Профессиональный пакет стиков',
            'amount' => 300,
            'price' => 1200,
            'is_active' => true,
            'sort_order' => 30,
        ]);

        StickyPackage::create([
            'name' => 'Max',
            'description' => 'Максимальный пакет стиков для крупного бизнеса',
            'amount' => 1000,
            'price' => 3500,
            'is_active' => true,
            'sort_order' => 40,
        ]);
        
        // Создаем подписки
        StickySubscription::create([
            'name' => 'Базовая',
            'description' => 'Подписка с ограниченным количеством стиков в месяц',
            'price' => 990,
            'monthly_limit' => 500,
            'unlimited_stickies' => false,
            'discount_percent' => 10,
            'is_active' => true,
            'sort_order' => 10,
            'features' => [
                'До 500 стиков в месяц',
                'Доступ к базе шаблонов',
                'Техническая поддержка',
                'Сохранение до 10 шаблонов'
            ]
        ]);

        StickySubscription::create([
            'name' => 'Безлимитная',
            'description' => 'Безлимитная подписка для активного использования',
            'price' => 2490,
            'monthly_limit' => null,
            'unlimited_stickies' => true,
            'discount_percent' => 15,
            'is_active' => true,
            'sort_order' => 20,
            'features' => [
                'Безлимитное количество стиков',
                'Полная база шаблонов',
                'Приоритетная поддержка',
                'Сохранение неограниченного числа шаблонов',
                'Дополнительные настройки документов'
            ]
        ]);
    }
}
