<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class RoleSwitcherController extends Controller
{
    /**
     * Обработка запроса на переключение роли пользователя.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function switchRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string|in:predprinimatel,user',
        ]);

        $requestedRole = $request->role;
        $user = Auth::user();
        
        // Проверяем, что у пользователя есть роль для переключения
        // или это администратор (у которого есть доступ ко всем ролям)
        if ($user->hasRole($requestedRole) || $user->hasRole('admin')) {
            // Устанавливаем активную роль в сессии
            session(['active_role' => $requestedRole]);
            
            // Перенаправляем на соответствующую домашнюю страницу роли
            if ($requestedRole == 'predprinimatel') {
                return redirect()->route('entrepreneur.certificates.index');
            } elseif ($requestedRole == 'user') {
                return redirect()->route('user.certificates.index');
            }
        }
    }
}
