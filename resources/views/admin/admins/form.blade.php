@extends('layouts.admin')

@section('title', $admin->exists ? 'Editar administrador' : 'Novo administrador')
@section('heading', $admin->exists ? 'Editar administrador' : 'Novo administrador')

@section('content')
    <form action="{{ $admin->exists ? route('admin.administradores.update', $admin) : route('admin.administradores.store') }}" method="POST"
          class="max-w-xl space-y-5 rounded-2xl border border-stone-200 bg-white p-6">
        @csrf
        @if($admin->exists) @method('PUT') @endif

        <div>
            <label for="name" class="text-sm font-medium text-stone-700">Nome *</label>
            <input id="name" name="name" value="{{ old('name', $admin->name) }}" required
                   class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <div>
            <label for="email" class="text-sm font-medium text-stone-700">E-mail *</label>
            <input id="email" name="email" type="email" value="{{ old('email', $admin->email) }}" required
                   class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="text-sm font-medium text-stone-700">Senha {{ $admin->exists ? '' : '*' }}</label>
                <input id="password" name="password" type="password" {{ $admin->exists ? '' : 'required' }}
                       class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                @if($admin->exists)<p class="mt-1 text-xs text-stone-400">Deixe em branco para manter a senha atual.</p>@endif
            </div>
            <div>
                <label for="password_confirmation" class="text-sm font-medium text-stone-700">Confirmar senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm text-stone-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $admin->is_active ?? true)) class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
            Conta ativa
        </label>

        <div class="flex gap-3 pt-2">
            <button class="rounded-full bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">Salvar</button>
            <a href="{{ route('admin.administradores.index') }}" class="rounded-full border border-stone-300 px-6 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50">Cancelar</a>
        </div>
    </form>
@endsection
