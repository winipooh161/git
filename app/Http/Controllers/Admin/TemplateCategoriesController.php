<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateCategoriesController extends Controller
{
    /**
     * Создание нового экземпляра контроллера.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Отображение списка категорий шаблонов.
     */
    public function index()
    {
        $categories = TemplateCategory::orderBy('sort_order')
            ->withCount('templates')
            ->paginate(10);
        return view('admin.template-categories.index', compact('categories'));
    }

    /**
     * Показать форму для создания категории.
     */
    public function create()
    {
        $documentTypes = TemplateCategory::getDocumentTypes();
        return view('admin.template-categories.create', compact('documentTypes'));
    }

    /**
     * Сохранить новую категорию.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'directory_name' => ['required', 'string', 'max:255', 'unique:template_categories'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'document_type' => ['required', 'string'], // Добавляем валидацию для типа документа
        ]);

        $slug = Str::slug($validatedData['name']);
        
        // Проверяем уникальность слага
        $count = 0;
        $originalSlug = $slug;
        while (TemplateCategory::where('slug', $slug)->exists()) {
            $count++;
            $slug = "{$originalSlug}-{$count}";
        }

        $category = TemplateCategory::create([
            'name' => $validatedData['name'],
            'slug' => $slug,
            'description' => $validatedData['description'] ?? null,
            'directory_name' => $validatedData['directory_name'],
            'sort_order' => $validatedData['sort_order'] ?? 0,
            'is_active' => $request->has('is_active'),
            'document_type' => $validatedData['document_type'], // Сохраняем тип документа
        ]);

        // Создаем директорию для шаблонов категории, если её еще нет
        $directory = public_path('templates/' . $validatedData['directory_name']);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return redirect()->route('admin.template-categories.index')
            ->with('success', 'Категория шаблонов успешно создана.');
    }

    /**
     * Показать форму для редактирования категории.
     */
    public function edit(TemplateCategory $templateCategory)
    {
        $documentTypes = TemplateCategory::getDocumentTypes();
        return view('admin.template-categories.edit', compact('templateCategory', 'documentTypes'));
    }

    /**
     * Обновить категорию.
     */
    public function update(Request $request, TemplateCategory $templateCategory)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'directory_name' => ['required', 'string', 'max:255', 'unique:template_categories,directory_name,' . $templateCategory->id],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'document_type' => ['required', 'string'], // Добавляем валидацию для типа документа
        ]);

        $templateCategory->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
            'directory_name' => $validatedData['directory_name'],
            'sort_order' => $validatedData['sort_order'] ?? 0,
            'is_active' => $request->has('is_active'),
            'document_type' => $validatedData['document_type'], // Обновляем тип документа
        ]);

        // Если изменено имя директории, создаем новую директорию
        $directory = public_path('templates/' . $validatedData['directory_name']);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return redirect()->route('admin.template-categories.index')
            ->with('success', 'Категория шаблонов успешно обновлена.');
    }

    /**
     * Переключение статуса активности категории.
     */
    public function toggleActive(Request $request, TemplateCategory $templateCategory)
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $templateCategory->update([
            'is_active' => $request->is_active,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Удалить категорию.
     */
    public function destroy(TemplateCategory $templateCategory)
    {
        // Проверяем, есть ли шаблоны в этой категории
        if ($templateCategory->templates()->count() > 0) {
            return back()->with('error', 'Невозможно удалить категорию, так как в ней есть шаблоны.');
        }
        
        $templateCategory->delete();
        
        return redirect()->route('admin.template-categories.index')
            ->with('success', 'Категория шаблонов успешно удалена.');
    }
}
