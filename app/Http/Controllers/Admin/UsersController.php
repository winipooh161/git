<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Отображение списка пользователей.
     */
    public function index()
    {
        $users = User::with(['roles', 'activeSubscription.subscriptionPlan'])->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Показать форму для создания пользователя.
     */
    public function create()
    {
        $roles = Role::all();
        $subscriptionPlans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        return view('admin.users.create', compact('roles', 'subscriptionPlans'));
    }

    /**
     * Сохранить нового пользователя.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
            'subscription_plan_id' => ['nullable', 'exists:subscription_plans,id'],
        ]);

        $user = User::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->roles()->sync($request->roles);

        // Если выбран тариф, назначаем его
        if ($request->filled('subscription_plan_id')) {
            $this->assignSubscriptionPlan($user, $request->subscription_plan_id, true);
        } else {
            // Иначе назначаем базовый тариф START
            $startPlan = SubscriptionPlan::where('slug', 'start')->first();
            if ($startPlan) {
                $this->assignSubscriptionPlan($user, $startPlan->id, true);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно создан.');
    }

    /**
     * Показать форму для редактирования пользователя.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        $subscriptionPlans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        
        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'subscriptionPlans'));
    }

    /**
     * Обновить данные пользователя.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
            'subscription_plan_id' => ['nullable', 'exists:subscription_plans,id'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Обновить пароль, если он указан
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['string', 'min:8', 'confirmed'],
            ]);
            
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Обновляем роли пользователя
        $user->roles()->sync($request->roles);

        // Если выбран новый тариф, назначаем его
        if ($request->filled('subscription_plan_id')) {
            $deactivateOthers = (bool) $request->input('deactivate_other_subscriptions', true);
            $this->assignSubscriptionPlan($user, $request->subscription_plan_id, $deactivateOthers);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно обновлен.');
    }

    /**
     * Удалить пользователя.
     */
    public function destroy(User $user)
    {
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно удален.');
    }
    
    /**
     * Назначить тариф пользователю
     * 
     * @param User $user Пользователь
     * @param int $planId ID тарифа
     * @param bool $deactivateOthers Деактивировать другие тарифы
     * @return void
     */
    protected function assignSubscriptionPlan(User $user, $planId, $deactivateOthers = true)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        
        // Деактивируем другие подписки, если требуется
        if ($deactivateOthers) {
            $user->subscriptions()->update(['is_active' => false]);
        }
        
        // Рассчитываем даты начала и окончания
        $startDate = now();
        $endDate = $plan->duration_days ? $startDate->copy()->addDays($plan->duration_days) : null;
        
        // Создаем новую подписку
        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true
        ]);
    }
}
