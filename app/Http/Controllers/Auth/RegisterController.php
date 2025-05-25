<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Генерируем UUID без дополнительных символов
        $uuid = Str::uuid()->toString();
        
        // Убедимся, что UUID не содержит пробелов или лишних символов
        $uuid = trim($uuid);
        
        $user = User::create([
            'id' => $uuid,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'avatar' => 'avatars/default.png', // Стандартная аватарка для всех новых пользователей
        ]);

        // Назначаем роль "клиент" новому пользователю
        $userRole = Role::where('slug', 'user')->first();
        if ($userRole) {
            $user->roles()->attach($userRole->id);
        }
        
        // Также назначаем роль "предприниматель" для возможности переключения
        $predprinimatelRole = Role::where('slug', 'predprinimatel')->first();
        if ($predprinimatelRole) {
            $user->roles()->attach($predprinimatelRole->id);
        }

        // Назначаем базовый тариф START новому пользователю
        $startPlan = SubscriptionPlan::where('slug', 'start')->first();
        if ($startPlan) {
            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $startPlan->id,
                'start_date' => now(),
                'end_date' => null, // бессрочная
                'is_active' => true
            ]);
            
            // Добавляем 5 стиков новому пользователю по тарифу start
            if ($startPlan->sticks_amount > 0) {
                $user->sticks = $startPlan->sticks_amount;
                $user->save();
            } else {
                // Если в плане не указано количество стиков, добавляем 5 по умолчанию
                $user->stick = 5;
                $user->save();
            }
        }

        return $user;
    }
}
