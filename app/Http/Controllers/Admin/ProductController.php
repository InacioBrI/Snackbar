<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->when($request->query('q'), function ($query, $q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->when($request->query('category'), function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['is_active' => true]),
            'categories' => Category::orderBy('name')->get(),
            'addons' => Addon::orderBy('name')->get(),
            'selectedAddons' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $product = Product::create($data);
        $product->addons()->sync($request->input('addons', []));

        return redirect()->route('admin.produtos.index')->with('success', 'Produto criado com sucesso.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'addons' => Addon::orderBy('name')->get(),
            'selectedAddons' => $product->addons()->pluck('addons.id')->all(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateData($request, $product);
        $product->update($data);
        $product->addons()->sync($request->input('addons', []));

        return redirect()->route('admin.produtos.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Produto excluído.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'track_stock' => ['sometimes', 'boolean'],
            'addons' => ['sometimes', 'array'],
            'addons.*' => ['integer', 'exists:addons,id'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['name'], $product);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['track_stock'] = $request->boolean('track_stock');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['stock'] = $validated['stock'] ?? 0;

        if ($request->hasFile('image')) {
            if ($product && $product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($validated['image']);
        }

        unset($validated['addons']);

        return $validated;
    }

    private function uniqueSlug(string $name, ?Product $product): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Product::where('slug', $slug)
            ->when($product, fn ($q) => $q->where('id', '!=', $product->id))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
