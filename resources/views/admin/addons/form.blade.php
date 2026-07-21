@extends('layouts.admin')

@section('title', $addon->exists ? 'Editar adicional' : 'Novo adicional')
@section('heading', $addon->exists ? 'Editar adicional' : 'Novo adicional')

@section('content')
    <form action="{{ $addon->exists ? route('admin.adicionais.update', $addon) : route('admin.adicionais.store') }}" method="POST"
          class="max-w-xl space-y-5 rounded-2xl border border-stone-200 bg-white p-6">
        @csrf
        @if($addon->exists) @method('PUT') @endif

        <div>
            <label for="name" class="text-sm font-medium text-stone-700">Nome *</label>
            <input id="name" name="name" value="{{ old('name', $addon->name) }}" required
                   class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <div>
            <label for="price" class="text-sm font-medium text-stone-700">Preço (R$) *</label>
            <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $addon->price) }}" required
                   class="mt-1 w-40 rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <label class="flex items-center gap-2 text-sm text-stone-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $addon->is_active)) class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
            Adicional ativo
        </label>

        <div class="flex gap-3 pt-2">
            <button class="rounded-full bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">Salvar</button>
            <a href="{{ route('admin.adicionais.index') }}" class="rounded-full border border-stone-300 px-6 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50">Cancelar</a>
        </div>
    </form>
@endsection
