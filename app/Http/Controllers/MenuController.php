<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $categories = Category::where('is_active', true)
            ->with(['activeProducts' => function ($query) use ($search) {
                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                }
            }])
            ->orderBy('sort_order')
            ->get();

        if ($search !== '') {
            $categories = $categories->filter(fn (Category $c) => $c->activeProducts->isNotEmpty())->values();
        }

        return view('menu.index', compact('categories', 'search'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'addons' => fn ($q) => $q->where('is_active', true)]);

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('menu.show', compact('product', 'related'));
    }
}
