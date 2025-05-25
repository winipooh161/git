<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TemplatesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\AnimationEffectsController;
use App\Http\Controllers\Admin\TemplateCategoriesController;
use App\Http\Controllers\Admin\TelegramBotController;
use App\Http\Controllers\Admin\SubscriptionPlansController;
use App\Http\Controllers\Admin\UserSubscriptionsController;
use App\Http\Controllers\Entrepreneur\CertificatesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Здесь определены все маршруты для административной панели
|
*/

// Маршруты для администратора
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UsersController::class);
    Route::resource('templates', TemplatesController::class);
    
    // Маршрут для переключения активности шаблона
    Route::post('/templates/{template}/toggle-active', [TemplatesController::class, 'toggleActive'])
        ->name('templates.toggle-active');
    
    // Для отладки: явно прописываем роут для создания шаблона
    Route::post('/templates', [TemplatesController::class, 'store'])->name('templates.store');
    
    // Маршруты для тарифов
    Route::resource('subscription-plans', SubscriptionPlansController::class);
    
    // Маршруты для подписок пользователей
    Route::resource('user-subscriptions', UserSubscriptionsController::class);
});

// Маршруты для анимационных эффектов (админка)
Route::prefix('admin/animation-effects')->name('admin.animation-effects.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AnimationEffectsController::class, 'index'])->name('index');
    Route::get('/create', [AnimationEffectsController::class, 'create'])->name('create');
    Route::post('/', [AnimationEffectsController::class, 'store'])->name('store');
    Route::get('/{animationEffect}/edit', [AnimationEffectsController::class, 'edit'])->name('edit');
    Route::put('/{animationEffect}', [AnimationEffectsController::class, 'update'])->name('update');
    Route::delete('/{animationEffect}', [AnimationEffectsController::class, 'destroy'])->name('destroy');
    Route::post('/{animationEffect}/toggle-status', [AnimationEffectsController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/{animationEffect}/preview', [AnimationEffectsController::class, 'preview'])->name('preview');
});

// Админ - категории шаблонов
Route::prefix('admin/template-categories')->name('admin.template-categories.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [TemplateCategoriesController::class, 'index'])->name('index');
    Route::get('/create', [TemplateCategoriesController::class, 'create'])->name('create');
    Route::post('/', [TemplateCategoriesController::class, 'store'])->name('store');
    Route::get('/{templateCategory}/edit', [TemplateCategoriesController::class, 'edit'])->name('edit');
    Route::put('/{templateCategory}', [TemplateCategoriesController::class, 'update'])->name('update');
    Route::delete('/{templateCategory}', [TemplateCategoriesController::class, 'destroy'])->name('destroy');
    Route::post('/{templateCategory}/toggle-active', [TemplateCategoriesController::class, 'toggleActive'])->name('toggle-active');
});

// Дополнительно: создаем отдельную группу маршрутов для документов админа
Route::prefix('admin/certificates')->name('admin.certificates.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [CertificatesController::class, 'index'])->name('index');
    Route::get('/select-template', [CertificatesController::class, 'selectTemplate'])
        ->name('select-template');
    Route::get('/create/{template}', [CertificatesController::class, 'create'])
        ->name('create');
    Route::post('/{template}', [CertificatesController::class, 'store'])
        ->name('store');
    Route::get('/{certificate}', [CertificatesController::class, 'show'])
        ->name('show');
    Route::get('/{certificate}/edit', [CertificatesController::class, 'edit'])
        ->name('edit');
    Route::put('/{certificate}', [CertificatesController::class, 'update'])
        ->name('update');
    Route::delete('/{certificate}', [CertificatesController::class, 'destroy'])
        ->name('destroy');
    Route::post('/{certificate}/send-email', [CertificatesController::class, 'sendEmail'])
        ->name('send-email');
});

// Маршруты для управления Telegram ботом (админка)
Route::prefix('admin/telegram')->name('admin.telegram.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [TelegramBotController::class, 'index'])->name('index');
    Route::post('/set-webhook', [TelegramBotController::class, 'setWebhook'])->name('setWebhook');
    Route::post('/delete-webhook', [TelegramBotController::class, 'deleteWebhook'])->name('deleteWebhook');
    Route::post('/send-test-message', [TelegramBotController::class, 'sendTestMessage'])->name('sendTestMessage');
});

