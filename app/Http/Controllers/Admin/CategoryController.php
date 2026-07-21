<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Category::create($data);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validateData($request, $category);
        $category->update($data);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Não é possível excluir uma categoria que possui produtos.');
        }

        $category->delete();

        return back()->with('success', 'Categoria excluída.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request, ?Category $category = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['name'], $category);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }

    private function uniqueSlug(string $name, ?Category $category): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Category::where('slug', $slug)
            ->when($category, fn ($q) => $q->where('id', '!=', $category->id))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
