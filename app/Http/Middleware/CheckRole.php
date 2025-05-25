<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Обрабатывает запрос, проверяя роль пользователя.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Если пользователь не авторизован
        if (!Auth::check()) {
            return redirect('login');
        }

        // Если роль передана через запятую, проверяем наличие любой из ролей
        if (strpos($role, ',') !== false) {
            $roles = explode(',', $role);
            if (Auth::user()->hasAnyRole($roles)) {
                return $next($request);
            }
        }
        // Иначе проверяем конкретную роль
        else if (Auth::user()->hasRole($role)) {
            return $next($request);
        }

        // Если роль не найдена, перенаправляем на страницу ошибки или домашнюю страницу
        return redirect()->route('home')->with('error', 'У вас нет доступа к этой странице.');
    }
}
