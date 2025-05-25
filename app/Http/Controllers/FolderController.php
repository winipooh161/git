<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FolderController extends Controller
{
    /**
     * Добавляет документ в папку
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $certificateId
     * @param  int  $folderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCertificateToFolder(Request $request, $certificateId, $folderId)
    {
        try {
            $user = Auth::user();
            
            // Проверяем, что папка и документ принадлежат пользователю
            $folder = Folder::where('user_id', $user->id)
                ->where('id', $folderId)
                ->first();
                
            $certificate = Certificate::where('id', $certificateId)
                ->first();
                
            if (!$folder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Папка не найдена или не принадлежит текущему пользователю'
                ], 404);
            }
            
            if (!$certificate) {
                return response()->json([
                    'success' => false,
                    'message' => 'документ не найден'
                ], 404);
            }
            
            // Проверяем, не находится ли документ уже в этой папке
            if ($certificate->folders()->where('folder_id', $folder->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'документ уже находится в этой папке'
                ], 200);
            }
            
            // Добавляем документ в папку
            $certificate->folders()->attach($folder->id);
            
            return response()->json([
                'success' => true,
                'message' => 'документ успешно добавлен в папку'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении документа в папку: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Удаляет документ из папки
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $certificateId
     * @param  int  $folderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeCertificateFromFolder(Request $request, $certificateId, $folderId)
    {
        try {
            $user = Auth::user();
            
            // Проверяем, что папка принадлежит пользователю
            $folder = Folder::where('user_id', $user->id)
                ->where('id', $folderId)
                ->first();
                
            $certificate = Certificate::where('id', $certificateId)
                ->first();
                
            if (!$folder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Папка не найдена или не принадлежит текущему пользователю'
                ], 404);
            }
            
            if (!$certificate) {
                return response()->json([
                    'success' => false,
                    'message' => 'документ не найден'
                ], 404);
            }
            
            // Удаляем документ из папки
            $certificate->folders()->detach($folder->id);
            
            return response()->json([
                'success' => true,
                'message' => 'документ успешно удален из папки'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении документа из папки: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Получить папки, в которых находится документ
     *
     * @param  int  $certificateId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCertificateFolders($certificateId)
    {
        try {
            $user = Auth::user();
            
            $certificate = Certificate::findOrFail($certificateId);
            
            // Получаем все папки пользователя
            $allFolders = Folder::where('user_id', $user->id)->get();
            
            // Для каждой папки проверяем, содержит ли она документ
            $folders = $allFolders->map(function($folder) use ($certificate) {
                $hasThisCertificate = $certificate->folders()->where('folder_id', $folder->id)->exists();
                
                return [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'color' => $folder->color,
                    'has_certificate' => $hasThisCertificate
                ];
            });
            
            return response()->json([
                'success' => true,
                'folders' => $folders
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении папок документа: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Создать новую папку
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string',
        ]);

        // Изменяем способ создания папки с использованием метода create()
        $folder = Folder::create([
            'name' => $validated['name'],
            'color' => $validated['color'],
            'user_id' => Auth::id(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Папка успешно создана',
                'folder' => $folder
            ]);
        }

        return redirect()->route('user.certificates.index')
                       ->with('success', 'Папка успешно создана');
    }
    
    /**
     * Удаляет папку и все связи документов с ней
     *
     * @param int $id ID папки для удаления
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Находим папку по ID, убеждаемся что она принадлежит текущему пользователю
        $folder = Folder::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        // Отсоединяем все документы от папки (удаляем связи)
        $folder->certificates()->detach();
        
        // Удаляем саму папку
        $folder->delete();
        
        // Проверяем, является ли запрос AJAX-запросом
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Папка \"{$folder->name}\" успешно удалена"
            ]);
        }
        
        // Для обычного HTML-запроса возвращаем редирект
        return redirect()
            ->route('user.certificates.index')
            ->with('success', "Папка \"{$folder->name}\" успешно удалена");
    }
    
    /**
     * Добавляет сертификат в папку
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCertificate(Request $request)
    {
        try {
            $validated = $request->validate([
                'folder_id' => 'required|exists:folders,id',
                'certificate_id' => 'required|exists:certificates,id',
            ]);
            
            // Проверяем, что папка принадлежит пользователю
            $folder = Folder::where('id', $validated['folder_id'])
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$folder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Папка не найдена или у вас нет к ней доступа'
                ], 403);
            }
            
            // Проверяем, что сертификат принадлежит пользователю или доступен ему
            $certificate = Certificate::where('id', $validated['certificate_id'])
                ->where(function($query) {
                    $query->where('user_id', Auth::id())
                        ->orWhere('recipient_email', Auth::user()->email)
                        ->orWhere('recipient_phone', Auth::user()->phone);
                })
                ->first();
                
            if (!$certificate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Документ не найден или у вас нет к нему доступа'
                ], 403);
            }
            
            // Проверяем, не находится ли сертификат уже в этой папке
            $exists = DB::table('certificate_folder')
                ->where('folder_id', $folder->id)
                ->where('certificate_id', $certificate->id)
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'success' => true,
                    'message' => 'Документ уже находится в этой папке'
                ]);
            }
            
            // Используем метод attach из отношения many-to-many вместо прямой вставки
            // Это позволит Laravel правильно обработать структуру таблицы
            $folder->certificates()->attach($certificate->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Документ успешно добавлен в папку'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении документа в папку: ' . $e->getMessage()
            ], 500);
        }
    }
}
