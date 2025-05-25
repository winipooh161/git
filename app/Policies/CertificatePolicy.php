<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificatePolicy
{
    use HandlesAuthorization;

    /**
     * Проверка доступа перед выполнением любого действия.
     * Администратор имеет полный доступ ко всем документам.
     *
     * @param User $user
     * @return bool|void
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Определяет, может ли пользователь просматривать список всех документов.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('predprinimatel');
    }

    /**
     * Определяет, может ли пользователь просматривать документ.
     *
     * @param User $user
     * @param Certificate $certificate
     * @return bool
     */
    public function view(User $user, Certificate $certificate)
    {
        // Владелец документа может его просматривать
        return $user->id === $certificate->user_id;
    }

    /**
     * Определяет, может ли пользователь просматривать документ, как получатель.
     * Получатель документа определяется по email или телефону.
     *
     * @param User $user
     * @param Certificate $certificate
     * @return bool
     */
    public function viewAsRecipient(User $user, Certificate $certificate)
    {
        // Проверяем совпадение по телефону или email
        if ($user->phone && $certificate->recipient_phone === $user->phone) {
            return true;
        }
        
        if ($user->email && $certificate->recipient_email === $user->email) {
            return true;
        }
        
        return false;
    }

    /**
     * Определяет, может ли пользователь создавать документы.
     */
    public function create(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('predprinimatel');
    }

    /**
     * Определяет, может ли пользователь обновлять документ.
     * Телефон получателя нельзя изменить после создания.
     *
     * @param User $user
     * @param Certificate $certificate
     * @return bool
     */
    public function update(User $user, Certificate $certificate)
    {
        // Только владелец может редактировать документ
        return $user->id === $certificate->user_id;
    }

    /**
     * Определяет, может ли пользователь удалять документ.
     *
     * @param User $user
     * @param Certificate $certificate
     * @return bool
     */
    public function delete(User $user, Certificate $certificate)
    {
        // Только владелец может отменять (удалять) документ
        // И только если он активен
        return $user->id === $certificate->user_id && $certificate->status === 'active';
    }
    
    /**
     * Определяет, может ли пользователь отметить документ как использованный.
     * 
     * @param User $user
     * @param Certificate $certificate
     * @return bool
     */
    public function markAsUsed(User $user, Certificate $certificate)
    {
        // Только владелец может отмечать документы как использованные
        return $user->id === $certificate->user_id && $certificate->status === 'active';
    }

    /**
     * Определяет, может ли обычный пользователь отметить документ как использованный.
     * Метод для пользователей НЕ существует, так как пользователи больше не могут активировать документы.
     */
    // public function markAsUsedByUser(User $user, Certificate $certificate)
    // {
    //     // Метод удален, т.к. пользователи больше не могут активировать документы
    //     return false;
    // }
}
