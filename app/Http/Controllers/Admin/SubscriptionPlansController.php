<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlansController extends Controller
{
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    
    /**
     * Отображение списка тарифов
     */
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('price')->get();
        return view('admin.subscription-plans.index', compact('plans'));
    }
    
    /**
     * Показать форму для создания тарифа.
     */
    public function create()
    {
        return view('admin.subscription-plans.create');
    }
    
    /**
     * Сохранить новый тариф.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:subscription_plans',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        
        // Конвертируем features в JSON, если передан массив
        if (isset($validated['features']) && is_array($validated['features'])) {
            $validated['features'] = json_encode($validated['features']);
        }
        
        // Устанавливаем is_active в false, если не передано
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        SubscriptionPlan::create($validated);
        
        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Тариф успешно создан');
    }
    
    /**
     * Показать форму для редактирования тарифа.
     */
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.edit', compact('subscriptionPlan'));
    }
    
    /**
     * Обновить тариф.
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:subscription_plans,slug,' . $subscriptionPlan->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        
        // Конвертируем features в JSON, если передан массив
        if (isset($validated['features']) && is_array($validated['features'])) {
            $validated['features'] = json_encode($validated['features']);
        }
        
        // Устанавливаем is_active в false, если не передано
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        $subscriptionPlan->update($validated);
        
        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Тариф успешно обновлен');
    }
    
    /**
     * Удалить тариф.
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        // Проверяем, есть ли активные пользователи на этом тарифе
        $usersCount = $subscriptionPlan->activeSubscriptions()->count();
        
        if ($usersCount > 0) {
            return redirect()->route('admin.subscription-plans.index')
                ->with('error', "Невозможно удалить тариф, так как на нем находится {$usersCount} пользователей");
        }
        
        $subscriptionPlan->delete();
        
        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Тариф успешно удален');
    }
}
