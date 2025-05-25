<?php

namespace App\Http\Controllers\Entrepreneur;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:predprinimatel']);
    }

    /**
     * Отображение панели предпринимателя.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Устанавливаем количество карточек на страницу - 6 штук
        $perPage = 6;
        
        $totalCertificates = $user->certificates()->count();
        $activeCertificates = $user->certificates()->where('status', 'active')->count();
        $expiredCertificates = $user->certificates()->where('status', 'expired')->count();
        $usedCertificates = $user->certificates()->where('status', 'used')->count();
        
        $totalAmount = $user->certificates()
            ->where('status', '!=', 'canceled')
            ->sum('amount');
        
        // Получаем сертификаты с пагинацией
        $certificates = $user->certificates()
            ->with('template')
            ->latest()
            ->paginate($perPage);
        
        // Если это AJAX-запрос (для бесконечной прокрутки)
        if ($request->ajax()) {
            return response()->json([
                'certificates' => $certificates->items(),
                'has_more_pages' => $certificates->hasMorePages(),
                'current_page' => $certificates->currentPage(),
                'last_page' => $certificates->lastPage(),
                'next_page_url' => $certificates->nextPageUrl()
            ]);
        }
            
        $recentCertificates = $user->certificates()
            ->with('template')
            ->latest()
            ->take(5)
            ->get();

        return view('entrepreneur.dashboard', compact(
            'totalCertificates',
            'activeCertificates',
            'expiredCertificates',
            'usedCertificates',
            'totalAmount',
            'recentCertificates',
            'certificates'
        ));
    }
}
