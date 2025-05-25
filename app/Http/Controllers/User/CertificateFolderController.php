<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateFolderController extends Controller
{
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:user']);
    }

    /**
     * Сохранить новую папку.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|in:primary,success,danger,warning,info,dark'
        ]);

        // Проверяем количество существующих папок у пользователя
        $userFoldersCount = Folder::where('user_id', Auth::id())->count();
        
        // Если у пользователя уже 5 папок, запрещаем создание новой
        if ($userFoldersCount >= 5) {
            return redirect()->back()->with('error', 'Вы не можете создать больше 5 папок. Пожалуйста, удалите существующие папки, прежде чем создавать новые.');
        }

        $folder = new Folder([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        $folder->user()->associate(Auth::user());
        $folder->save();

        return redirect()->back()->with('success', 'Папка успешно создана');
    }

    /**
     * Добавить документ в папку.
     */
    public function addCertificate(Request $request, Certificate $certificate, Folder $folder)
    {
        // Проверка доступа
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этой папке'
            ], 403);
        }

        // Проверка, принадлежит ли документ пользователю
        $userPhone = Auth::user()->phone;
        $userEmail = Auth::user()->email;
        
        $isUsersCertificate = ($userPhone && $certificate->recipient_phone === $userPhone) 
            || ($userEmail && $certificate->recipient_email === $userEmail);
            
        if (!$isUsersCertificate) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этому документу'
            ], 403);
        }

        // Добавляем документ в папку
        $certificate->folders()->syncWithoutDetaching([$folder->id]);

        return response()->json([
            'success' => true,
            'message' => 'документ добавлен в папку "' . $folder->name . '"'
        ]);
    }

    /**
     * Получить список папок, в которых находится документ
     */
    public function getCertificateFolders(Certificate $certificate)
    {
        try {
            // Проверка принадлежности документа пользователю
            $userPhone = Auth::user()->phone;
            $userEmail = Auth::user()->email;
            
            $isUsersCertificate = ($userPhone && $certificate->recipient_phone === $userPhone) 
                || ($userEmail && $certificate->recipient_email === $userEmail);
                
            if (!$isUsersCertificate) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому документу'
                ], 403);
            }

            // Исправляем неоднозначность id, явно указывая таблицу
            $folders = $certificate->folders()
                ->where('certificate_folders.user_id', Auth::id())
                ->select(['certificate_folders.id', 'certificate_folders.name', 'certificate_folders.color'])
                ->get();

            return response()->json([
                'success' => true,
                'folders' => $folders
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка при получении папок документа: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении папок',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить документ из папки.
     */
    public function removeCertificate(Certificate $certificate, Folder $folder)
    {
        // Проверка доступа
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этой папке'
            ], 403);
        }

        // Проверяем принадлежность документа пользователю
        $userPhone = Auth::user()->phone;
        $userEmail = Auth::user()->email;
        
        $isUsersCertificate = ($userPhone && $certificate->recipient_phone === $userPhone) 
            || ($userEmail && $certificate->recipient_email === $userEmail);
            
        if (!$isUsersCertificate) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этому документу'
            ], 403);
        }

        // Удаляем документ из папки
        $certificate->folders()->detach($folder->id);

        return response()->json([
            'success' => true,
            'message' => 'документ удален из папки "' . $folder->name . '"'
        ]);
    }

    /**
     * Обновить папку.
     */
    public function update(Request $request, Folder $folder)
    {
        // Проверка доступа
        if ($folder->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'У вас нет доступа к этой папке');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|in:primary,success,danger,warning,info,dark'
        ]);

        $folder->update([
            'name' => $request->name,
            'color' => $request->color
        ]);

        return redirect()->back()->with('success', 'Папка успешно обновлена');
    }

    /**
     * Удалить папку.
     */
    public function destroy(Folder $folder)
    {
        // Проверка доступа
        if ($folder->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'У вас нет доступа к этой папке');
        }

        // Удаляем связи с документами, а затем саму папку
        $folder->certificates()->detach();
        $folder->delete();

        return redirect()->back()->with('success', 'Папка успешно удалена');
    }

    /**
     * Получение списка папок с информацией о принадлежности документа к ним
     */
    public function getFolders(Certificate $certificate)
    {
        // Проверяем доступ к документу
        if ($certificate->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этому документу'
            ], 403);
        }
        
        // Получаем все папки пользователя
        $folders = Auth::user()->folders;
        
        // Получаем ID папок, в которых находится документ
        $certificateFolderIds = $certificate->folders()->pluck('folders.id')->toArray();
        
        // Добавляем флаг has_certificate к каждой папке
        $folders = $folders->map(function ($folder) use ($certificateFolderIds) {
            $folder->has_certificate = in_array($folder->id, $certificateFolderIds);
            return $folder;
        });
        
        return response()->json([
            'success' => true,
            'folders' => $folders,
            'certificate_id' => $certificate->id
        ]);
    }
    
    /**
     * Добавление документа в папку
     */
    public function addToFolder(Certificate $certificate, Folder $folder)
    {
        // Проверяем доступ к документу и папке
        if ($certificate->user_id != Auth::id() || $folder->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этому документу или папке'
            ], 403);
        }
        
        // Проверяем, что документ еще не в этой папке
        if (!$certificate->folders()->where('folders.id', $folder->id)->exists()) {
            $certificate->folders()->attach($folder->id);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'документ добавлен в папку'
        ]);
    }
    
    /**
     * Удаление документа из папки
     */
    public function removeFromFolder(Certificate $certificate, Folder $folder)
    {
        // Проверяем доступ к документу и папке
        if ($certificate->user_id != Auth::id() || $folder->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этому документу или папке'
            ], 403);
        }
        
        $certificate->folders()->detach($folder->id);
        
        return response()->json([
            'success' => true,
            'message' => 'документ удален из папки'
        ]);
    }
}
