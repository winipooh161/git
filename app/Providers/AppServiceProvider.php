<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SmsService;
use App\Services\ImageService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SmsService::class, function ($app) {
            return new SmsService();
        });
        
        // Регистрируем ImageService, если не зарегистрирован
        if (!$this->app->bound(ImageService::class)) {
            $this->app->singleton(ImageService::class, function ($app) {
                return new ImageService();
            });
        }

        // Правильно регистрируем NotificationService как singleton
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Создаем директории для временных файлов при запуске приложения
        $this->ensureDirectoriesExist([
            storage_path('app/public/temp'),
            storage_path('app/public/temp/certificates'),
            storage_path('app/public/certificate_covers'),
            storage_path('app/public/company_logos')
        ]);

        // Добавляем переменную с количеством непрочитанных уведомлений для всех аутентифицированных запросов
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $unreadNotificationsCount = auth()->user()->unreadNotificationsCount();
                $view->with('unreadNotificationsCount', $unreadNotificationsCount);
            }
        });
    }

    /**
     * Проверяет существование директорий и создает их при необходимости
     */
    protected function ensureDirectoriesExist(array $paths)
    {
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }
}
