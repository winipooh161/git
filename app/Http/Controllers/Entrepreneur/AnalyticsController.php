<?php

namespace App\Http\Controllers\Entrepreneur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Показать страницу статистики.
     *
     * @return \Illuminate\Http\Response
     */
    public function statistics()
    {
        // Получаем документы текущего пользователя
        $user = Auth::user();
        $certificates = Certificate::where('user_id', $user->id)->get();
        
        // Базовая статистика 
        $totalCertificates = $certificates->count();
        $activeCertificates = $certificates->where('status', 'active')->count();
        $usedCertificates = $certificates->where('status', 'used')->count();
        $expiredCertificates = $certificates->where('status', 'expired')->count();
        $canceledCertificates = $certificates->where('status', 'canceled')->count();
        
        // Общая сумма документов
        $totalAmount = $certificates->sum('amount');
        
        // Статистика по шаблонам
        $templateStats = $certificates->groupBy('template_id')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'used' => $group->where('status', 'used')->count(),
                    'amount' => $group->sum('amount'),
                    'template_name' => $group->first()->template->name ?? 'Неизвестный'
                ];
            });
        
        // Топ документов по сумме
        $topCertificates = Certificate::where('user_id', $user->id)
            ->orderBy('amount', 'desc')
            ->with('template')
            ->take(5)
            ->get();
            
        // Статистика по месяцам
        $monthlyStats = $certificates->groupBy(function($certificate) {
            return $certificate->created_at->format('Y-m');
        })->map(function($group) {
            return [
                'created' => $group->count(),
                'used' => $group->where('status', 'used')->count(),
                'amount' => $group->sum('amount'),
                'conversion' => $group->count() > 0 
                    ? round(($group->where('status', 'used')->count() / $group->count()) * 100, 1) 
                    : 0
            ];
        });
        
        // Статистика по получателям (топ-5)
        $topRecipients = [];
        if ($totalCertificates > 0) {
            $topRecipients = Certificate::where('user_id', $user->id)
                ->select('recipient_name as name', 'recipient_email as email', 
                         DB::raw('COUNT(*) as certificates_count'),
                         DB::raw('SUM(amount) as total_amount'),
                         DB::raw('AVG(amount) as average_amount'),
                         DB::raw('MAX(created_at) as last_activity'))
                ->groupBy('recipient_name', 'recipient_email')
                ->orderBy('certificates_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    $item->last_activity = Carbon::parse($item->last_activity);
                    return $item;
                });
        }
        
        // Средний чек и количество документов в месяц
        $certificatesPerMonth = 0;
        $avgActivationTime = null;
        $conversionChange = null;
        
        if ($totalCertificates > 0) {
            // Расчет среднего количества документов в месяц
            $firstCertDate = Certificate::where('user_id', $user->id)
                ->orderBy('created_at', 'asc')
                ->first()->created_at;
                
            $months = max(1, Carbon::now()->diffInMonths($firstCertDate) + 1);
            $certificatesPerMonth = round($totalCertificates / $months, 1);
            
            // Расчет среднего времени активации (для документов со статусом used)
            $usedCerts = $certificates->where('status', 'used');
            if ($usedCerts->count() > 0) {
                $avgDays = 0;
                foreach ($usedCerts as $cert) {
                    if ($cert->used_at) {
                        $avgDays += $cert->created_at->diffInDays($cert->used_at);
                    }
                }
                $avgActivationTime = round($avgDays / $usedCerts->count());
            }
            
            // Расчет изменения конверсии по сравнению с прошлым периодом
            // Сравниваем текущий месяц с предыдущим
            $currentMonth = Carbon::now()->format('Y-m');
            $prevMonth = Carbon::now()->subMonth()->format('Y-m');
            
            $currConversion = isset($monthlyStats[$currentMonth]) ? $monthlyStats[$currentMonth]['conversion'] : 0;
            $prevConversion = isset($monthlyStats[$prevMonth]) ? $monthlyStats[$prevMonth]['conversion'] : 0;
            
            if ($prevConversion > 0) {
                $conversionChange = round($currConversion - $prevConversion, 1);
            }
        }
        
        // Передаем данные в представление
        return view('entrepreneur.analytics.statistics', compact(
            'totalCertificates',
            'activeCertificates',
            'usedCertificates',
            'expiredCertificates',
            'canceledCertificates', 
            'totalAmount',
            'templateStats',
            'topCertificates',
            'topRecipients',
            'monthlyStats',
            'certificatesPerMonth',
            'avgActivationTime',
            'conversionChange'
        ));
    }

    /**
     * Показать страницу отчетов.
     *
     * @return \Illuminate\Http\Response
     */
    public function reports()
    {
        // Получаем документы текущего пользователя
        $user = Auth::user();
        $certificates = Certificate::where('user_id', $user->id)->get();
        
        // Общая статистика для отчетов
        $certificatesTotal = $certificates->count();
        $certificatesActive = $certificates->where('status', 'active')->count();
        $certificatesUsed = $certificates->where('status', 'used')->count();
        $totalAmount = $certificates->sum('amount');
        
        // Группировка для ежемесячного отчета
        $monthlyStats = $certificates->groupBy(function($certificate) {
            return $certificate->created_at->format('Y-m');
        })->map(function($group) {
            return [
                'created' => $group->count(),
                'used' => $group->where('status', 'used')->count(),
                'amount' => $group->sum('amount'),
                'conversion' => $group->count() > 0 
                    ? round(($group->where('status', 'used')->count() / $group->count()) * 100, 1) 
                    : 0
            ];
        });
        
        // Передаем данные в представление
        return view('entrepreneur.analytics.reports', compact(
            'certificatesTotal',
            'certificatesActive',
            'certificatesUsed',
            'totalAmount',
            'monthlyStats'
        ));
    }
}
