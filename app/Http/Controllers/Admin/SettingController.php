<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'about' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'hours' => ['nullable', 'string', 'max:255'],
            'service_fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['in:pix,credit,debit'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        foreach (['name', 'about', 'phone', 'address', 'hours', 'service_fee_percent'] as $key) {
            Setting::set($key, $validated[$key] ?? '');
        }

        Setting::set('payment_methods', implode(',', $validated['payment_methods']));

        if ($request->hasFile('logo')) {
            $old = Setting::get('logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('logo', $request->file('logo')->store('store', 'public'));
        }

        return back()->with('success', 'Configurações salvas com sucesso.');
    }
}
