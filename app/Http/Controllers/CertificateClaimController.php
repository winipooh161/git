<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CertificateClaimController extends Controller
{
    protected $notificationService;
    
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    /**
     * Активация (получение) сертификата пользователем
     *
     * @param string $uuid UUID сертификата
     * @return \Illuminate\Http\Response
     */
    public function claim($uuid)
    {
        try {
            // Проверяем, что пользователь авторизован
            if (!Auth::check()) {
                return redirect()->route('certificates.public', $uuid)
                    ->with('error', 'Для получения сертификата необходимо авторизоваться.');
            }
            
            // Находим сертификат по UUID
            $certificate = Certificate::where('uuid', $uuid)->first();
            
            // Если сертификат не найден, показываем ошибку
            if (!$certificate) {
                Log::error("Не найден сертификат с UUID: {$uuid}");
                return redirect()->route('certificates.public', $uuid)
                    ->with('error', 'Сертификат не найден.');
            }
            
            // Получаем текущего пользователя
            $user = Auth::user();
            
            // Детальное логирование для отладки
            Log::info("ДЕТАЛЬНАЯ ДИАГНОСТИКА АКТИВАЦИИ СЕРТИФИКАТА", [
                'user_id' => $user->id,
                'certificate_id' => $certificate->id,
                'certificate_uuid' => $uuid,
                'is_batch_parent' => $certificate->is_batch_parent,
                'status' => $certificate->status,
                'batch_size' => $certificate->batch_size,
                'activated_copies' => $certificate->activated_copies_count,
            ]);
            
            // Проверяем доступность активации сертификата
            if ($certificate->status === 'used' || $certificate->status === 'expired' || $certificate->status === 'canceled') {
                Log::warning("Попытка активации сертификата с недопустимым статусом: {$certificate->status}");
                return redirect()->route('certificates.public', $uuid)
                    ->with('error', 'Этот сертификат уже использован или срок его действия истек.');
            }
            
            // Проверка и обработка сертификата серии
            if ($certificate->is_batch_parent || $certificate->status === Certificate::STATUS_SERIES) {
                Log::info("Обработка сертификата серии: {$certificate->id}");
                
                // Проверяем, доступны ли еще копии для активации
                if ($certificate->activated_copies_count >= $certificate->batch_size) {
                    Log::warning("Серия исчерпана: {$certificate->id}, активировано {$certificate->activated_copies_count}/{$certificate->batch_size}");
                    return redirect()->route('certificates.public', $uuid)
                        ->with('error', 'Все сертификаты из этой серии уже получены.');
                }
                
                // Создаем новую копию для пользователя
                $copy = $this->createCertificateCopy($certificate, $certificate->activated_copies_count + 1);
                
                if (!$copy) {
                    Log::error("Ошибка при создании копии сертификата серии: {$certificate->id}");
                    return redirect()->route('certificates.public', $uuid)
                        ->with('error', 'Произошла ошибка при создании копии сертификата. Пожалуйста, попробуйте позже.');
                }
                
                // Активируем копию для текущего пользователя
                $copy->recipient_name = $user->name;
                $copy->recipient_email = $user->email;
                $copy->recipient_phone = $user->phone;
                $copy->save();
                
                // Создаем уведомление о получении сертификата для пользователя
                $this->notificationService->certificateReceived($copy, $user);
                
                // Обновляем счетчик активированных копий в родительском сертификате
                $certificate->activated_copies_count = $certificate->activated_copies_count + 1;
                
                // Гарантируем, что данные пользователя не попадают в родительский сертификат
                if ($certificate->recipient_name || $certificate->recipient_email || $certificate->recipient_phone) {
                    $certificate->recipient_name = null;
                    $certificate->recipient_email = null;
                    $certificate->recipient_phone = null;
                }
                
                $certificate->save();
                
                Log::info("УСПЕХ: Создана копия {$copy->id} сертификата серии {$certificate->id}", [
                    'copy_uuid' => $copy->uuid,
                    'user_id' => $user->id,
                    'activated_count' => $certificate->activated_copies_count,
                    'batch_size' => $certificate->batch_size
                ]);
                
                // Перенаправляем на страницу просмотра полученного сертификата
                return redirect()->route('certificates.public', $copy->uuid)
                    ->with('success', 'Поздравляем! Вы успешно получили сертификат из серии.');
            } 
            // Обработка обычного сертификата
            else {
                // Проверяем, не активирован ли он уже
                if ($certificate->isClaimed()) {
                    return redirect()->route('certificates.public', $uuid)
                        ->with('error', 'Этот сертификат уже получен другим пользователем.');
                }
                
                // Если не активирован, активируем его
                $certificate->recipient_name = $user->name;
                $certificate->recipient_email = $user->email;
                $certificate->recipient_phone = $user->phone;
                $certificate->save();
                
                // Создаем уведомление о получении сертификата
                $this->notificationService->certificateReceived($certificate, $user);
                
                Log::info("Пользователь {$user->id} успешно активировал обычный сертификат {$certificate->id}");
                
                return redirect()->route('certificates.public', $uuid)
                    ->with('success', 'Поздравляем! Сертификат успешно активирован на ваше имя.');
            }
        } catch (\Exception $e) {
            // Логирование всех непойманных ошибок
            Log::error("КРИТИЧЕСКАЯ ОШИБКА при активации сертификата {$uuid}: " . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Перенаправление с сообщением об ошибке
            return redirect()->route('certificates.public', $uuid)
                ->with('error', 'Произошла непредвиденная ошибка при активации сертификата. Пожалуйста, попробуйте позже.');
        }
    }

    /**
     * Создает копию сертификата для серии
     * 
     * @param Certificate $parent Родительский сертификат
     * @param int $batchNumber Номер в серии
     * @return Certificate|null Созданная копия или null в случае ошибки
     */
    protected function createCertificateCopy($parent, $batchNumber)
    {
        try {
            // Генерация уникального номера документа
            $certificateNumber = $this->generateCertificateNumber();
            
            Log::info("Создание копии сертификата из серии", [
                'parent_id' => $parent->id,
                'parent_uuid' => $parent->uuid,
                'batch_number' => $batchNumber,
                'new_certificate_number' => $certificateNumber
            ]);
            
            // Создаем копию сертификата на основе родительского
            $copy = new Certificate([
                'certificate_number' => $certificateNumber,
                'user_id' => $parent->user_id,
                'certificate_template_id' => $parent->certificate_template_id,
                'amount' => $parent->amount,
                'is_percent' => $parent->is_percent,
                'message' => $parent->message,
                'valid_from' => $parent->valid_from,
                'valid_until' => $parent->valid_until,
                'status' => 'active', // Всегда устанавливаем активный статус для копии
                'animation_effect_id' => $parent->animation_effect_id,
                'batch_size' => $parent->batch_size,
                'batch_number' => $batchNumber,
                'parent_id' => $parent->id,
                'is_batch_parent' => false // Копия не является родительским сертификатом
            ]);
            
            // Генерируем UUID для копии
            $copy->uuid = (string) Str::uuid();
            
            // Копируем логотип компании и обложку, если они есть
            $copy->company_logo = $parent->company_logo;
            $copy->cover_image = $parent->cover_image;
            
            $copy->save();
            
            Log::info("Успешно создана копия сертификата {$copy->id} для серии {$parent->id}, UUID новой копии: {$copy->uuid}");
            
            return $copy;
        } catch (\Exception $e) {
            Log::error("Ошибка при создании копии сертификата: " . $e->getMessage(), [
                'parent_id' => $parent->id,
                'batch_number' => $batchNumber,
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return null;
        }
    }

    /**
     * Генерирует уникальный номер для сертификата
     * 
     * @return string
     */
    private function generateCertificateNumber()
    {
        $prefix = 'SN';
        $date = now()->format('ymd');
        $random = strtoupper(Str::random(4));
        
        return $prefix . $date . $random;
    }
}
