<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PublicCertificateController extends Controller
{
    /**
     * Отображает публичную страницу документа по UUID.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        // Ищем сертификат по UUID
        $certificate = Certificate::where('uuid', $uuid)->first();
        
        // Если сертификат не найден или статус не активен и не серия, показываем ошибку 404
        if (!$certificate || 
            ($certificate->status !== Certificate::STATUS_ACTIVE && 
             $certificate->status !== Certificate::STATUS_SERIES)) {
            return abort(404, 'Сертификат не найден или истек срок его действия');
        }

        // Логгируем просмотр сертификата и более подробную информацию для отладки
        Log::info("Просмотр публичного сертификата {$certificate->id} с UUID {$uuid}", [
            'is_batch_parent' => $certificate->is_batch_parent ? 'true' : 'false',
            'batch_size' => $certificate->batch_size,
            'activated_copies' => $certificate->activated_copies_count,
            'user_id' => Auth::id() ?? 'гость',
            'parent_id' => $certificate->parent_id,
            'is_claimed' => $certificate->isClaimed() ? 'да' : 'нет'
        ]);

        // Проверяем, получен ли уже сертификат - используем метод isClaimed()
        $isClaimed = $certificate->isClaimed();
        
        // Если это сертификат серии и еще есть доступные экземпляры, разрешаем доступ всем
        $isSeriesWithAvailableCopies = ($certificate->is_batch_parent || $certificate->status === Certificate::STATUS_SERIES) && 
                                       ($certificate->activated_copies_count < $certificate->batch_size);
        
        // Если сертификат уже получен и это не серия с доступными копиями, проверяем права доступа
        if ($isClaimed && !$isSeriesWithAvailableCopies && Auth::check()) {
            $user = Auth::user();
            $hasAccess = false;
            
            // Проверяем, совпадает ли email или телефон текущего пользователя с получателем
            if ($certificate->recipient_email && $certificate->recipient_email === $user->email) {
                $hasAccess = true;
            }
            
            if ($certificate->recipient_phone && $certificate->recipient_phone === $user->phone) {
                $hasAccess = true;
            }
            
            // Владелец сертификата (создатель) также имеет доступ
            if ($certificate->user_id === $user->id) {
                $hasAccess = true;
            }
            
            // Если у пользователя нет доступа к этому сертификату
            if (!$hasAccess) {
                return abort(403, 'У вас нет доступа к этому сертификату');
            }
        }
        
        // Подготовка данных для шаблона
        $data = $certificate->getTemplateData();
        
        // Явно указываем, что режим не редактируемый
        $data['editable'] = false;
        
        // Если у документа нет обложки, используем запасное изображение
        if (!$certificate->cover_image || !file_exists(public_path('storage/' . $certificate->cover_image))) {
            // Устанавливаем запасное изображение обложки
            $certificate->cover_image = 'default_certificate_cover.jpg';
        }
        
        // Загружаем связанную модель шаблона для доступа к его свойствам
        $certificate->load('template');
        
        // Отображаем публичную страницу с данными сертификата
        return view('certificates.public', [
            'certificate' => $certificate,
            'data' => $data,
            'isClaimed' => $isClaimed, // Передаем результат метода isClaimed()
            'certificateData' => json_encode($data), // Добавляем данные в формате JSON для передачи в iframe
            'isSeriesWithAvailableCopies' => $isSeriesWithAvailableCopies // Передаем флаг доступности серии
        ]);
    }

    /**
     * Проверяет статус сертификата и возвращает JSON-ответ.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus($uuid)
    {
        $certificate = Certificate::where('uuid', $uuid)->first();
        
        if (!$certificate) {
            return response()->json(['status' => 'not_found'], 404);
        }
        
        return response()->json([
            'status' => $certificate->status,
            'valid_until' => $certificate->valid_until->format('Y-m-d'),
            'is_expired' => $certificate->valid_until->isPast()
        ]);
    }
}
