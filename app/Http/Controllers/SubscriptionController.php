<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Страница с тарифами для выбора пользователем
     */
    public function index()
    {
        // Получаем все активные тарифы, отсортированные по цене
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();
            
        // Получаем текущий тариф пользователя
        $currentPlan = Auth::user()->currentPlan();
        
        // Получаем активную подписку пользователя
        $activeSubscription = Auth::user()->activeSubscription;
        
        return view('subscription.plans', compact('plans', 'currentPlan', 'activeSubscription'));
    }
    
    /**
     * Подписка на выбранный тариф
     */
    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        // Валидация запроса
        $request->validate([
            'payment_method' => 'required|in:card,qiwi,yoomoney',
        ]);
        
        // В реальном приложении здесь должна быть интеграция с платежной системой
        
        // Деактивируем текущие активные подписки
        Auth::user()->subscriptions()
            ->where('is_active', true)
            ->update(['is_active' => false]);
        
        // Создаем новую подписку
        $subscription = new UserSubscription();
        $subscription->user_id = Auth::id();
        $subscription->subscription_plan_id = $plan->id;
        $subscription->start_date = now();
        
        // Устанавливаем дату окончания, если тариф имеет ограниченный срок действия
        if ($plan->duration_days) {
            $subscription->end_date = now()->addDays($plan->duration_days);
        }
        
        $subscription->is_active = true;
        $subscription->payment_id = 'demo_payment_' . uniqid(); // В реальном приложении будет ID транзакции
        $subscription->save();
        
        // Начисляем стики в зависимости от тарифа
        $sticksToAdd = $this->getSticksForPlan($plan);
        Auth::user()->addSticks($sticksToAdd);
        
        return redirect()->route('profile.index')
            ->with('success', "Вы успешно подписались на тариф {$plan->name} и получили {$sticksToAdd} стиков!");
    }

    /**
     * Возвращает количество стиков для указанного тарифа
     *
     * @param SubscriptionPlan $plan
     * @return int Количество стиков
     */
    private function getSticksForPlan(SubscriptionPlan $plan)
    {
        switch ($plan->slug) {
            case 'start':
                return 5; // базовый тариф - 5 стиков единоразово
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
    
    /**
     * Отмена текущей подписки
     */
    public function cancel()
    {
        $activeSubscription = Auth::user()->activeSubscription;
        
        if (!$activeSubscription) {
            return back()->with('error', 'У вас нет активной подписки для отмены.');
        }
        
        // Отмечаем подписку как неактивную
        $activeSubscription->is_active = false;
        $activeSubscription->save();
        
        // Назначаем базовый тариф Start
        $startPlan = SubscriptionPlan::where('slug', 'start')->first();
        
        if ($startPlan) {
            $subscription = new UserSubscription();
            $subscription->user_id = Auth::id();
            $subscription->subscription_plan_id = $startPlan->id;
            $subscription->start_date = now();
            $subscription->is_active = true;
            $subscription->save();
        }
        
        return redirect()->route('profile.index')
            ->with('success', 'Ваша подписка была отменена. Вам назначен базовый тариф.');
    }
}
