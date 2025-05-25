<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Отображение панели администратора.
     */
    public function index()
    {
        $usersCount = User::count();
        $entrepreneursCount = User::whereHas('roles', function ($query) {
            $query->where('slug', 'predprinimatel');
        })->count();
        
        $certificatesCount = Certificate::count();
        $templatesCount = CertificateTemplate::count();
        
        $latestUsers = User::latest()->take(5)->get();
        $latestCertificates = Certificate::with(['user', 'template'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'usersCount', 
            'entrepreneursCount', 
            'certificatesCount', 
            'templatesCount', 
            'latestUsers', 
            'latestCertificates'
        ));
    }
}
