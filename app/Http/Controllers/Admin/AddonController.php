<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function index(): View
    {
        $addons = Addon::withCount('products')->orderBy('name')->paginate(15);

        return view('admin.addons.index', compact('addons'));
    }

    public function create(): View
    {
        return view('admin.addons.form', ['addon' => new Addon(['is_active' => true, 'price' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Addon::create($this->validateData($request));

        return redirect()->route('admin.adicionais.index')->with('success', 'Adicional criado com sucesso.');
    }

    public function edit(Addon $addon): View
    {
        return view('admin.addons.form', compact('addon'));
    }

    public function update(Request $request, Addon $addon): RedirectResponse
    {
        $addon->update($this->validateData($request));

        return redirect()->route('admin.adicionais.index')->with('success', 'Adicional atualizado com sucesso.');
    }

    public function destroy(Addon $addon): RedirectResponse
    {
        $addon->delete();

        return back()->with('success', 'Adicional excluído.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
