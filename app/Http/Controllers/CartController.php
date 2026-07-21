<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index(): View
    {
        return view('cart.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'addons' => ['sometimes', 'array'],
            'addons.*' => ['integer', 'exists:addons,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::active()->findOrFail($validated['product_id']);

        if (! $product->isAvailable()) {
            return back()->with('error', 'Este produto está indisponível no momento.');
        }

        $this->cart->add(
            $product,
            (int) $validated['quantity'],
            $validated['addons'] ?? [],
            $validated['notes'] ?? null,
        );

        return redirect()->route('cart.index')->with('success', 'Produto adicionado ao carrinho!');
    }

    public function update(Request $request, string $rowId): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:50'],
        ]);

        $this->cart->update($rowId, (int) $validated['quantity']);

        return redirect()->route('cart.index')->with('success', 'Carrinho atualizado.');
    }

    public function destroy(string $rowId): RedirectResponse
    {
        $this->cart->remove($rowId);

        return redirect()->route('cart.index')->with('success', 'Item removido do carrinho.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return redirect()->route('cart.index')->with('success', 'Carrinho esvaziado.');
    }
}
