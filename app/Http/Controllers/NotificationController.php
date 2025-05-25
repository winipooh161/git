<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class NotificationController extends Controller
{
    /**
     * Construct a new controller instance
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Получение списка уведомлений для текущего пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->notifications();
        
        // Фильтрация по типу
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Фильтрация по статусу прочтения
        if ($request->has('read')) {
            if ($request->read === '0') {
                $query->whereNull('read_at');
            } elseif ($request->read === '1') {
                $query->whereNotNull('read_at');
            }
        }
        
        // Сортировка от новых к старым
        $query->latest();
        
        // Получаем уведомления с пагинацией
        $notifications = $query->paginate(10);
        
        // Если это AJAX запрос, возвращаем только HTML для уведомлений
        if ($request->ajax()) {
            $view = View::make('notifications.partials._notifications_list', compact('notifications'))->render();
            return response()->json([
                'html' => $view,
                'has_more' => $notifications->hasMorePages(),
                'unread_count' => $user->notifications()->whereNull('read_at')->count()
            ]);
        }
        
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Пометить уведомление как прочитанное
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        
        // Проверка прав доступа
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->markAsRead();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'unread_count' => Auth::user()->notifications()->whereNull('read_at')->count()
            ]);
        }
        
        return back()->with('success', 'Уведомление отмечено прочитанным');
    }
    
    /**
     * Пометить все уведомления как прочитанные
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        // Помечаем все непрочитанные уведомления как прочитанные
        $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0
            ]);
        }
        
        return back()->with('success', 'Все уведомления отмечены прочитанными');
    }
    
    /**
     * Удаление уведомления
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        
        // Проверка прав доступа
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->delete();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => Auth::user()->notifications()->whereNull('read_at')->count()
            ]);
        }
        
        return back()->with('success', 'Уведомление удалено');
    }
    
    /**
     * Получение списка непрочитанных уведомлений для отображения в модальном окне
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUnread(Request $request)
    {
        $user = Auth::user();
        
        // Получаем последние 10 непрочитанных уведомлений
        $notifications = $user->notifications()
                             ->whereNull('read_at')
                             ->latest()
                             ->take(10)
                             ->get();
        
        // Получаем общее количество непрочитанных уведомлений
        $unread_count = $user->notifications()->whereNull('read_at')->count();
        
        if ($request->ajax()) {
            $view = View::make('notifications.partials._modal_list', compact('notifications'))->render();
            return response()->json([
                'html' => $view,
                'unread_count' => $unread_count
            ]);
        }
        
        return view('notifications.unread', compact('notifications', 'unread_count'));
    }
}
