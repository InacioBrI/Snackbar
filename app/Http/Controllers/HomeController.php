<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Product::active()
            ->where('is_featured', true)
            ->with('category')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        return view('home', compact('featured', 'categories'));
    }
}
