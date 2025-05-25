<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLevel
{
    /**
     * Проверка уровня подписки пользователя.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $level  Минимальный уровень подписки (start, vip, premium, individual)
     * @param  string  $redirectRoute  Маршрут для перенаправления при несоответствии (опционально)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $level, string $redirectRoute = null): Response
    {
        if (!$request->user() || !$request->user()->hasSubscriptionLevel($level)) {
            // Если указан кастомный маршрут, перенаправляем на него
            if ($redirectRoute) {
                return redirect()->route($redirectRoute)
                    ->with('error', "Для доступа к этой функции требуется тариф {$level} или выше");
            }
            
            // По умолчанию перенаправляем на страницу тарифов
            return redirect()->route('subscription.plans')
                ->with('error', "Для доступа к этой функции требуется тариф {$level} или выше");
        }
        
        return $next($request);
    }
}
