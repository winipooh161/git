<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use App\Services\NotificationService;
use Carbon\Carbon;

class CheckExpiredCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет просроченные сертификаты и отправляет уведомления';

    /**
     * Сервис для работы с уведомлениями.
     *
     * @var \App\Services\NotificationService
     */
    protected $notificationService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Начинаем проверку просроченных сертификатов...');
        
        // Получаем сертификаты, срок действия которых истек вчера
        $yesterday = Carbon::yesterday()->endOfDay();
        $today = Carbon::today()->startOfDay();
        
        $expiredCertificates = Certificate::where('status', 'active')
            ->whereBetween('valid_until', [$yesterday, $today])
            ->get();
            
        $count = count($expiredCertificates);
        
        $this->info("Найдено {$count} просроченных сертификатов.");
        
        foreach ($expiredCertificates as $certificate) {
            // Обновляем статус сертификата
            $certificate->status = Certificate::STATUS_EXPIRED;
            $certificate->save();
            
            // Отправляем уведомление
            $notification = $this->notificationService->certificateExpired($certificate);
            
            if ($notification) {
                $this->info("Отправлено уведомление о просроченном сертификате #{$certificate->certificate_number}");
            } else {
                $this->warn("Не удалось создать уведомление для сертификата #{$certificate->certificate_number}");
            }
        }
        
        $this->info('Проверка просроченных сертификатов завершена.');
        
        return 0;
    }
}
