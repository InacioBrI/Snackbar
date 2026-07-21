<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index(): View
    {
        $admins = Admin::orderBy('name')->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.form', ['admin' => new Admin(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:admins,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        Admin::create($validated);

        return redirect()->route('admin.administradores.index')->with('success', 'Administrador criado com sucesso.');
    }

    public function edit(Admin $admin): View
    {
        return view('admin.admins.form', compact('admin'));
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('admins', 'email')->ignore($admin->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('admin.administradores.index')->with('success', 'Administrador atualizado com sucesso.');
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        if (Auth::guard('admin')->id() === $admin->id) {
            return back()->with('error', 'Você não pode excluir a sua própria conta.');
        }

        if (Admin::count() <= 1) {
            return back()->with('error', 'É necessário manter ao menos um administrador.');
        }

        $admin->delete();

        return back()->with('success', 'Administrador excluído.');
    }
}
