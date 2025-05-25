<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoldersController extends Controller
{
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:user']);
    }

    /**
     * Сохранение новой папки.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:50',
        ]);
        
        // Проверяем количество папок (ограничение до 5 папок)
        $foldersCount = Folder::where('user_id', Auth::id())->count();
        if ($foldersCount >= 5) {
            return redirect()->back()->with('error', 'Вы достигли лимита в 5 папок');
        }

        $folder = Folder::create([
            'name' => $validated['name'],
            'color' => $validated['color'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Папка успешно создана');
    }

    /**
     * Удаление папки.
     */
    public function destroy(Folder $folder)
    {
        // Проверяем, что папка принадлежит текущему пользователю
        if ($folder->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'У вас нет прав для удаления этой папки');
        }

        // Удаляем связи документов с папкой перед удалением самой папки
        $folder->certificates()->detach();
        
        // Удаляем папку
        $folder->delete();

        return redirect()->back()->with('success', 'Папка успешно удалена');
    }
}
