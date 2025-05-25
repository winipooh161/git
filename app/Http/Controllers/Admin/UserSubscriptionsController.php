<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserSubscriptionsController extends Controller
{
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    
    /**
     * Отображение списка подписок пользователей.
     */
    public function index()
    {
        $subscriptions = UserSubscription::with(['user', 'subscriptionPlan'])
            ->where('is_active', true)
            ->latest()
            ->paginate(15);
            
        return view('admin.user-subscriptions.index', compact('subscriptions'));
    }
    
    /**
     * Показать форму для назначения подписки.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        
        return view('admin.user-subscriptions.create', compact('users', 'plans'));
    }
    
    /**
     * Сохранить новую подписку.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);
        
        // Устанавливаем is_active в false, если не передано
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        // Деактивируем текущие активные подписки пользователя
        UserSubscription::where('user_id', $validated['user_id'])
            ->where('is_active', true)
            ->update(['is_active' => false]);
        
        // Создаем новую подписку
        UserSubscription::create($validated);
        
        return redirect()->route('admin.user-subscriptions.index')
            ->with('success', 'Подписка успешно создана');
    }
    
    /**
     * Показать форму для редактирования подписки.
     */
    public function edit(UserSubscription $userSubscription)
    {
        $users = User::orderBy('name')->get();
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        
        return view('admin.user-subscriptions.edit', compact('userSubscription', 'users', 'plans'));
    }
    
    /**
     * Обновить подписку.
     */
    public function update(Request $request, UserSubscription $userSubscription)
    {
        $validated = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);
        
        // Устанавливаем is_active в false, если не передано
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        // Если подписку активируют, деактивируем другие активные подписки того же пользователя
        if ($validated['is_active'] && !$userSubscription->is_active) {
            UserSubscription::where('user_id', $userSubscription->user_id)
                ->where('id', '!=', $userSubscription->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }
        
        $userSubscription->update($validated);
        
        return redirect()->route('admin.user-subscriptions.index')
            ->with('success', 'Подписка успешно обновлена');
    }
    
    /**
     * Удалить подписку.
     */
    public function destroy(UserSubscription $userSubscription)
    {
        $userSubscription->delete();
        
        return redirect()->route('admin.user-subscriptions.index')
            ->with('success', 'Подписка успешно удалена');
    }
}
