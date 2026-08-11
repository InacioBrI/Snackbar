<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\DailyMenuService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(private DailyMenuService $dailyMenu) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $todayMenu = $this->dailyMenu->today();
        $todayProducts = $this->dailyMenu->productsToday();

        if ($search !== '') {
            $todayProducts = $todayProducts->filter(
                fn (Product $p) => str_contains(mb_strtolower($p->name.' '.$p->description), mb_strtolower($search))
            )->values();
        }

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
            ->get()
            ->filter(fn (Category $c) => $c->activeProducts->isNotEmpty())
            ->values();

        return view('menu.index', compact('categories', 'search', 'todayMenu', 'todayProducts'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'addons' => fn ($q) => $q->where('is_active', true)]);

        $related = $this->dailyMenu->hasMenuToday()
            ? $this->dailyMenu->productsToday()->where('id', '!=', $product->id)->take(4)->values()
            : Product::active()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->take(4)
                ->get();

        return view('menu.show', compact('product', 'related'));
    }
}
