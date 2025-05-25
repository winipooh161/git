<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Здесь будут данные для панели управления документами
        $stats = [
            'total_certificates' => 0,
            'active_certificates' => 0,
            'expired_certificates' => 0,
            'revenue' => 0,
        ];
        
        $recentCertificates = []; // В будущем здесь будут последние созданные документы
        
        return view('home', [
            'stats' => $stats,
            'recentCertificates' => $recentCertificates,
        ]);
    }
}
