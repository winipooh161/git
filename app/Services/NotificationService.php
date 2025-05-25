<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Создать уведомление о выдаче сертификата
     * 
     * @param Certificate $certificate
     * @return Notification|null
     */
    public function certificateIssued(Certificate $certificate)
    {
        try {
            if (!$certificate->user_id) {
                Log::warning('Попытка создать уведомление для сертификата без user_id', [
                    'certificate_id' => $certificate->id
                ]);
                return null;
            }
            
            $notification = $this->createNotification(
                $certificate->user_id,
                'Выдан новый сертификат',
                "Вы успешно выдали сертификат №{$certificate->certificate_number}",
                Notification::TYPE_CERTIFICATE_ISSUED,
                $certificate,
                [
                    'certificate_number' => $certificate->certificate_number,
                    'amount' => $certificate->amount,
                    'recipient_name' => $certificate->recipient_name,
                    'url' => route('entrepreneur.certificates.show', $certificate)
                ]
            );
            
            Log::info('Создано уведомление о выдаче сертификата', [
                'notification_id' => $notification->id,
                'user_id' => $certificate->user_id,
                'certificate_id' => $certificate->id
            ]);
            
            return $notification;
        } catch (\Exception $e) {
            Log::error('Ошибка при создании уведомления о выдаче сертификата', [
                'error' => $e->getMessage(),
                'certificate_id' => $certificate->id
            ]);
            return null;
        }
    }
    
    /**
     * Создать уведомление о получении сертификата
     * 
     * @param Certificate $certificate
     * @param User $recipient
     * @return Notification|null
     */
    public function certificateReceived(Certificate $certificate, User $recipient)
    {
        try {
            // Создаем уведомление для получателя о полученном сертификате
            $notification = $this->createNotification(
                $recipient->id,
                'Получен новый сертификат',
                "Вы получили сертификат №{$certificate->certificate_number} на сумму {$certificate->formatted_amount}",
                Notification::TYPE_CERTIFICATE_RECEIVED,
                $certificate,
                [
                    'certificate_number' => $certificate->certificate_number,
                    'amount' => $certificate->amount,
                    'sender_name' => $certificate->user->name ?? 'Неизвестный отправитель',
                    'url' => route('certificates.public', $certificate->uuid)
                ]
            );
            
            // Если это сертификат из серии, также уведомляем владельца серии
            if ($certificate->parent_id) {
                $parentCertificate = Certificate::find($certificate->parent_id);
                if ($parentCertificate && $parentCertificate->user_id) {
                    $ownerNotification = $this->createNotification(
                        $parentCertificate->user_id,
                        'Сертификат из серии активирован',
                        "Сертификат №{$certificate->certificate_number} из серии был получен пользователем {$recipient->name}",
                        Notification::TYPE_CERTIFICATE_RECEIVED,
                        $certificate,
                        [
                            'certificate_number' => $certificate->certificate_number,
                            'amount' => $certificate->amount,
                            'recipient_name' => $recipient->name,
                            'url' => route('entrepreneur.certificates.show', $parentCertificate),
                            'activated_count' => $parentCertificate->activated_copies_count,
                            'batch_size' => $parentCertificate->batch_size
                        ]
                    );
                    
                    Log::info('Создано уведомление для владельца о получении сертификата из серии', [
                        'notification_id' => $ownerNotification->id ?? 'null',
                        'user_id' => $parentCertificate->user_id,
                        'certificate_id' => $certificate->id
                    ]);
                }
            }
            
            Log::info('Создано уведомление о получении сертификата', [
                'notification_id' => $notification->id,
                'user_id' => $recipient->id,
                'certificate_id' => $certificate->id
            ]);
            
            return $notification;
        } catch (\Exception $e) {
            Log::error('Ошибка при создании уведомления о получении сертификата', [
                'error' => $e->getMessage(),
                'certificate_id' => $certificate->id,
                'recipient_id' => $recipient->id
            ]);
            return null;
        }
    }
    
    /**
     * Создать уведомление об использовании сертификата
     * 
     * @param Certificate $certificate
     * @return Notification|null
     */
    public function certificateUsed(Certificate $certificate)
    {
        try {
            $notifications = [];
            
            // Уведомление для предпринимателя
            if ($certificate->user_id) {
                $entNotification = $this->createNotification(
                    $certificate->user_id,
                    'Сертификат использован',
                    "Сертификат №{$certificate->certificate_number} был использован",
                    Notification::TYPE_CERTIFICATE_USED,
                    $certificate,
                    [
                        'certificate_number' => $certificate->certificate_number,
                        'amount' => $certificate->amount,
                        'recipient_name' => $certificate->recipient_name,
                        'url' => route('entrepreneur.certificates.show', $certificate)
                    ]
                );
                
                $notifications[] = $entNotification;
                
                Log::info('Создано уведомление для предпринимателя об использовании сертификата', [
                    'notification_id' => $entNotification->id,
                    'user_id' => $certificate->user_id,
                    'certificate_id' => $certificate->id
                ]);
            }
            
            // Уведомление для получателя, если есть email или телефон для идентификации
            $recipientUser = null;
            
            if ($certificate->recipient_email) {
                $recipientUser = User::where('email', $certificate->recipient_email)->first();
            } elseif ($certificate->recipient_phone) {
                $recipientUser = User::where('phone', $certificate->recipient_phone)->first();
            }
            
            if ($recipientUser) {
                $recipientNotification = $this->createNotification(
                    $recipientUser->id,
                    'Сертификат использован',
                    "Ваш сертификат №{$certificate->certificate_number} был использован",
                    Notification::TYPE_CERTIFICATE_USED,
                    $certificate,
                    [
                        'certificate_number' => $certificate->certificate_number,
                        'amount' => $certificate->amount,
                        'url' => route('certificates.public', $certificate->uuid)
                    ]
                );
                
                $notifications[] = $recipientNotification;
                
                Log::info('Создано уведомление для получателя об использовании сертификата', [
                    'notification_id' => $recipientNotification->id,
                    'user_id' => $recipientUser->id,
                    'certificate_id' => $certificate->id
                ]);
            }
            
            // Если это сертификат из серии, также уведомляем владельца серии
            if ($certificate->parent_id) {
                $parentCertificate = Certificate::find($certificate->parent_id);
                if ($parentCertificate && $parentCertificate->user_id && $parentCertificate->user_id !== $certificate->user_id) {
                    $ownerNotification = $this->createNotification(
                        $parentCertificate->user_id,
                        'Сертификат из серии использован',
                        "Сертификат №{$certificate->certificate_number} из серии был использован",
                        Notification::TYPE_CERTIFICATE_USED,
                        $certificate,
                        [
                            'certificate_number' => $certificate->certificate_number,
                            'amount' => $certificate->amount,
                            'url' => route('entrepreneur.certificates.show', $parentCertificate)
                        ]
                    );
                    
                    $notifications[] = $ownerNotification;
                    
                    Log::info('Создано уведомление для владельца серии об использовании сертификата', [
                        'notification_id' => $ownerNotification->id,
                        'user_id' => $parentCertificate->user_id,
                        'certificate_id' => $certificate->id
                    ]);
                }
            }
            
            return $notifications[0] ?? null;
        } catch (\Exception $e) {
            Log::error('Ошибка при создании уведомления об использовании сертификата', [
                'error' => $e->getMessage(),
                'certificate_id' => $certificate->id
            ]);
            return null;
        }
    }
    
    /**
     * Создать уведомление о просроченном сертификате
     * 
     * @param Certificate $certificate
     * @return Notification|null
     */
    public function certificateExpired(Certificate $certificate)
    {
        try {
            $notifications = [];
            
            // Уведомление для предпринимателя
            if ($certificate->user_id) {
                $entNotification = $this->createNotification(
                    $certificate->user_id,
                    'Сертификат просрочен',
                    "Срок действия сертификата №{$certificate->certificate_number} истек",
                    Notification::TYPE_CERTIFICATE_EXPIRED,
                    $certificate,
                    [
                        'certificate_number' => $certificate->certificate_number,
                        'valid_until' => $certificate->valid_until->format('d.m.Y'),
                        'url' => route('entrepreneur.certificates.show', $certificate)
                    ]
                );
                
                $notifications[] = $entNotification;
                
                Log::info('Создано уведомление для предпринимателя о просроченном сертификате', [
                    'notification_id' => $entNotification->id,
                    'user_id' => $certificate->user_id,
                    'certificate_id' => $certificate->id
                ]);
            }
            
            // Уведомление для получателя, если есть email или телефон для идентификации
            $recipientUser = null;
            
            if ($certificate->recipient_email) {
                $recipientUser = User::where('email', $certificate->recipient_email)->first();
            } elseif ($certificate->recipient_phone) {
                $recipientUser = User::where('phone', $certificate->recipient_phone)->first();
            }
            
            if ($recipientUser) {
                $recipientNotification = $this->createNotification(
                    $recipientUser->id,
                    'Срок действия сертификата истек',
                    "Срок действия вашего сертификата №{$certificate->certificate_number} истек",
                    Notification::TYPE_CERTIFICATE_EXPIRED,
                    $certificate,
                    [
                        'certificate_number' => $certificate->certificate_number,
                        'valid_until' => $certificate->valid_until->format('d.m.Y'),
                        'url' => route('certificates.public', $certificate->uuid)
                    ]
                );
                
                $notifications[] = $recipientNotification;
                
                Log::info('Создано уведомление для получателя о просроченном сертификате', [
                    'notification_id' => $recipientNotification->id,
                    'user_id' => $recipientUser->id,
                    'certificate_id' => $certificate->id
                ]);
            }
            
            return $notifications[0] ?? null;
        } catch (\Exception $e) {
            Log::error('Ошибка при создании уведомления о просроченном сертификате', [
                'error' => $e->getMessage(),
                'certificate_id' => $certificate->id
            ]);
            return null;
        }
    }
    
    /**
     * Создает системное уведомление для пользователя
     *
     * @param User $user
     * @param string $title
     * @param string $message
     * @return Notification|null
     */
    public function systemNotification(User $user, $title, $message)
    {
        try {
            $notification = $this->createNotification(
                $user->id,
                $title,
                $message,
                Notification::TYPE_SYSTEM,
                null,
                []
            );
            
            Log::info('Создано системное уведомление', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'title' => $title
            ]);
            
            return $notification;
        } catch (\Exception $e) {
            Log::error('Ошибка при создании системного уведомления', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'title' => $title
            ]);
            return null;
        }
    }
    
    /**
     * Создает новое уведомление с защитой от ошибок
     *
     * @param string $userId
     * @param string $title
     * @param string $message
     * @param string $type
     * @param mixed $related
     * @param array $data
     * @return Notification
     */
    protected function createNotification($userId, $title, $message, $type, $related = null, $data = [])
    {
        try {
            if (empty($userId)) {
                Log::error('Попытка создать уведомление без user_id', [
                    'title' => $title,
                    'type' => $type
                ]);
                throw new \Exception('Не указан пользователь для уведомления');
            }
            
            // Проверка существования пользователя
            $user = User::find($userId);
            if (!$user) {
                Log::error('Попытка создать уведомление для несуществующего пользователя', [
                    'user_id' => $userId,
                    'title' => $title
                ]);
                throw new \Exception('Пользователь не найден');
            }
            
            // Создание уведомления с обработкой ошибок
            $notification = new Notification([
                'user_id' => $userId,
                'uuid' => Str::uuid()->toString(),
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data
            ]);
            
            // Устанавливаем связанную модель, если она передана
            if ($related) {
                $notification->related()->associate($related);
            }
            
            $notification->save();
            
            return $notification;
        } catch (\Exception $e) {
            Log::error('Ошибка при создании уведомления', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'title' => $title,
                'type' => $type
            ]);
            
            // В случае критической ошибки возвращаем null
            return null;
        }
    }
}
