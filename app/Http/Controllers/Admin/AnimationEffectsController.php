<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimationEffect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnimationEffectsController extends Controller
{
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct()
    {
        // Исключаем метод getEffects из проверки авторизации
        $this->middleware(['auth', 'role:admin'])->except(['getEffects']);
    }

    /**
     * Отображение списка анимационных эффектов.
     */
    public function index()
    {
        $animationEffects = AnimationEffect::orderBy('sort_order')->paginate(10);
        return view('admin.animation-effects.index', compact('animationEffects'));
    }

    /**
     * Показать форму для создания анимационного эффекта.
     */
    public function create()
    {
        return view('admin.animation-effects.create');
    }

    /**
     * Сохранить новый анимационный эффект.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'particles' => 'required|string',
            'description' => 'nullable|string',
            'direction' => 'required|string',
            'speed' => 'required|string',
            'color' => 'nullable|string',
            'size_min' => 'required|integer|min:8',
            'size_max' => 'required|integer|min:8',
            'quantity' => 'required|integer|min:10',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean'
        ]);

        $validatedData['slug'] = Str::slug($request->name);
        $validatedData['is_active'] = $request->has('is_active') ? true : false;
        $validatedData['particles'] = explode(',', $request->particles);

        AnimationEffect::create($validatedData);

        return redirect()->route('admin.animation-effects.index')
            ->with('success', 'Анимационный эффект успешно создан');
    }

    /**
     * Показать форму для редактирования анимационного эффекта.
     */
    public function edit(AnimationEffect $animationEffect)
    {
        return view('admin.animation-effects.edit', compact('animationEffect'));
    }

    /**
     * Обновить анимационный эффект.
     */
    public function update(Request $request, AnimationEffect $animationEffect)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'particles' => 'required|string',
            'description' => 'nullable|string',
            'direction' => 'required|string',
            'speed' => 'required|string',
            'color' => 'nullable|string',
            'size_min' => 'required|integer|min:8',
            'size_max' => 'required|integer|min:8',
            'quantity' => 'required|integer|min:10',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean'
        ]);

        $validatedData['is_active'] = $request->has('is_active') ? true : false;
        $validatedData['particles'] = explode(',', $request->particles);

        $animationEffect->update($validatedData);

        return redirect()->route('admin.animation-effects.index')
            ->with('success', 'Анимационный эффект успешно обновлен');
    }

    /**
     * Удалить эффект.
     */
    public function destroy(AnimationEffect $animationEffect)
    {
        $animationEffect->delete();
        
        return redirect()->route('admin.animation-effects.index')
            ->with('success', 'Анимационный эффект успешно удален');
    }

    /**
     * Изменить статус (активировать/деактивировать) эффекта.
     */
    public function toggleStatus(AnimationEffect $animationEffect)
    {
        $animationEffect->is_active = !$animationEffect->is_active;
        $animationEffect->save();
        
        return redirect()->route('admin.animation-effects.index')
            ->with('success', 'Статус эффекта успешно изменен');
    }

    /**
     * Предварительный просмотр анимационного эффекта.
     */
    public function preview(AnimationEffect $animationEffect)
    {
        return view('admin.animation-effects.preview', compact('animationEffect'));
    }
    
    /**
     * Возвращает список анимационных эффектов в формате JSON.
     * Этот метод доступен публично и не требует авторизации.
     */
    public function getEffects()
    {
        try {
            // Проверяем, запрошен ли конкретный эффект по ID
            $requestedId = request()->has('id') ? (int)request()->input('id') : null;
            \Log::info('Запрос анимационных эффектов', ['requested_id' => $requestedId]);
            
            // Если запрашивается конкретный эффект
            if ($requestedId) {
                $effect = AnimationEffect::find($requestedId);
                
                if ($effect) {
                    \Log::info('Найден запрашиваемый эффект', ['id' => $effect->id, 'name' => $effect->name]);
                    return response()->json([$this->formatEffectForResponse($effect)]);
                } else {
                    \Log::warning('Запрашиваемый эффект не найден', ['id' => $requestedId]);
                }
            }
            
            // Пробуем получить все активные эффекты из базы
            $effects = AnimationEffect::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            
            \Log::info('Загружено эффектов из базы: ' . count($effects));
                
            // Если эффектов нет, возвращаем демонстрационные
            if ($effects->isEmpty()) {
                \Log::info('Эффектов в базе нет, возвращаем демонстрационные');
                $effects = AnimationEffect::getDefaultEffects();
                
                // Если был запрос конкретного эффекта, ищем его среди демонстрационных
                if ($requestedId) {
                    foreach ($effects as $effect) {
                        if (isset($effect['id']) && $effect['id'] === $requestedId) {
                            \Log::info('Найден запрашиваемый эффект среди демонстрационных', ['id' => $effect['id']]);
                            return response()->json([$effect]);
                        }
                    }
                }
            }
            
            // Обрабатываем каждый эффект для обеспечения корректности данных
            $processedEffects = collect($effects)->map(function($effect) {
                return $this->formatEffectForResponse($effect);
            })->toArray();
            
            \Log::info('Отправляется эффектов: ' . count($processedEffects));
            return response()->json($processedEffects);
            
        } catch (\Exception $e) {
            \Log::error('Ошибка при получении анимационных эффектов: ' . $e->getMessage());
            
            // В случае любой ошибки возвращаем демонстрационные эффекты
            $fallbackEffects = AnimationEffect::getDefaultEffects();
            return response()->json($fallbackEffects);
        }
    }

    /**
     * Форматирует эффект для ответа API
     */
    private function formatEffectForResponse($effect)
    {
        // Преобразуем ID в число для гарантии корректности сравнения
        $id = isset($effect['id']) ? intval($effect['id']) : (isset($effect->id) ? intval($effect->id) : 1);
        
        // Формируем результат с гарантированными значениями по умолчанию
        $result = [
            'id' => $id,
            'name' => $effect['name'] ?? $effect->name ?? 'Эффект',
            'slug' => $effect['slug'] ?? $effect->slug ?? 'effect',
            'description' => $effect['description'] ?? $effect->description ?? 'Описание эффекта',
            'type' => $effect['type'] ?? $effect->type ?? 'confetti',
            'direction' => $effect['direction'] ?? $effect->direction ?? 'center',
            'speed' => $effect['speed'] ?? $effect->speed ?? 'normal',
            'quantity' => isset($effect['quantity']) ? intval($effect['quantity']) : (isset($effect->quantity) ? intval($effect->quantity) : 50),
            'size_min' => isset($effect['size_min']) ? intval($effect['size_min']) : (isset($effect->size_min) ? intval($effect->size_min) : 16),
            'size_max' => isset($effect['size_max']) ? intval($effect['size_max']) : (isset($effect->size_max) ? intval($effect->size_max) : 32),
        ];
        
        // Обеспечиваем, что particles всегда массив
        if (isset($effect['particles'])) {
            if (is_string($effect['particles'])) {
                $result['particles'] = explode(',', $effect['particles']);
            } elseif (is_array($effect['particles'])) {
                $result['particles'] = $effect['particles'];
            } else {
                $result['particles'] = ['✨', '🎉', '🎊'];
            }
        } elseif (isset($effect->particles)) {
            if (is_string($effect->particles)) {
                $result['particles'] = explode(',', $effect->particles);
            } elseif (is_array($effect->particles)) {
                $result['particles'] = $effect->particles;
            } else {
                $result['particles'] = ['✨', '🎉', '🎊'];
            }
        } else {
            $result['particles'] = ['✨', '🎉', '🎊'];
        }
        
        return $result;
    }
}
