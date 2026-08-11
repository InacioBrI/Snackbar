<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMenu;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DailyMenuController extends Controller
{
    public function index(): View
    {
        $menus = DailyMenu::withCount('products')
            ->orderByDesc('menu_date')
            ->paginate(15);

        return view('admin.daily-menus.index', [
            'menus' => $menus,
            'todayMenu' => DailyMenu::forDate(today())->first(),
        ]);
    }

    public function create(): View
    {
        return view('admin.daily-menus.form', [
            'dailyMenu' => new DailyMenu(['menu_date' => today(), 'is_published' => true]),
            'products' => $this->products(),
            'selected' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $menu = DailyMenu::create($data['attributes']);
        $menu->products()->sync($data['products']);

        return redirect()->route('admin.cardapio-do-dia.index')->with('success', 'Cardápio do dia criado com sucesso.');
    }

    public function edit(DailyMenu $dailyMenu): View
    {
        $selected = $dailyMenu->products()
            ->get()
            ->mapWithKeys(fn (Product $product) => [$product->id => [
                'is_sold_out' => (bool) $product->pivot->is_sold_out,
                'sort_order' => (int) $product->pivot->sort_order,
            ]])
            ->all();

        return view('admin.daily-menus.form', [
            'dailyMenu' => $dailyMenu,
            'products' => $this->products(),
            'selected' => $selected,
        ]);
    }

    public function update(Request $request, DailyMenu $dailyMenu): RedirectResponse
    {
        $data = $this->validateData($request, $dailyMenu);

        $dailyMenu->update($data['attributes']);
        $dailyMenu->products()->sync($data['products']);

        return redirect()->route('admin.cardapio-do-dia.index')->with('success', 'Cardápio do dia atualizado.');
    }

    public function destroy(DailyMenu $dailyMenu): RedirectResponse
    {
        $dailyMenu->delete();

        return back()->with('success', 'Cardápio do dia excluído.');
    }

    /**
     * @return Collection<int, Product>
     */
    private function products()
    {
        return Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array{attributes: array<string, mixed>, products: array<int, array<string, mixed>>}
     */
    private function validateData(Request $request, ?DailyMenu $dailyMenu = null): array
    {
        $validated = $request->validate([
            'menu_date' => [
                'required',
                'date',
                Rule::unique('daily_menus')->ignore($dailyMenu?->id),
            ],
            'title' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['sometimes', 'boolean'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['integer', 'exists:products,id'],
            'sold_out' => ['sometimes', 'array'],
            'sold_out.*' => ['integer'],
        ], [
            'products.required' => 'Selecione pelo menos um prato para o cardápio do dia.',
            'products.min' => 'Selecione pelo menos um prato para o cardápio do dia.',
            'menu_date.required' => 'Informe a data do cardápio.',
            'menu_date.date' => 'Informe uma data válida.',
            'menu_date.unique' => 'Já existe um cardápio cadastrado para esta data.',
            'title.max' => 'O título deve ter no máximo 150 caracteres.',
            'notes.max' => 'As observações devem ter no máximo 1000 caracteres.',
        ]);

        $soldOut = array_map('intval', $request->input('sold_out', []));

        $products = [];
        foreach (array_values($validated['products']) as $index => $productId) {
            $products[(int) $productId] = [
                'is_sold_out' => in_array((int) $productId, $soldOut, true),
                'sort_order' => $index,
            ];
        }

        return [
            'attributes' => [
                'menu_date' => $validated['menu_date'],
                'title' => $validated['title'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'is_published' => $request->boolean('is_published'),
            ],
            'products' => $products,
        ];
    }
}
