@extends('layouts.admin')

@section('title', $category->exists ? 'Editar categoria' : 'Nova categoria')
@section('heading', $category->exists ? 'Editar categoria' : 'Nova categoria')

@section('content')
    <form action="{{ $category->exists ? route('admin.categorias.update', $category) : route('admin.categorias.store') }}" method="POST"
          class="max-w-2xl space-y-5 rounded-2xl border border-stone-200 bg-white p-6">
        @csrf
        @if($category->exists) @method('PUT') @endif

        <div>
            <label for="name" class="text-sm font-medium text-stone-700">Nome *</label>
            <input id="name" name="name" value="{{ old('name', $category->name) }}" required
                   class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <div>
            <label for="description" class="text-sm font-medium text-stone-700">Descrição</label>
            <textarea id="description" name="description" rows="3"
                      class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('description', $category->description) }}</textarea>
        </div>
        <div>
            <label for="sort_order" class="text-sm font-medium text-stone-700">Ordem de exibição</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                   class="mt-1 w-32 rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <label class="flex items-center gap-2 text-sm text-stone-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active)) class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
            Categoria ativa
        </label>

        <div class="flex gap-3 pt-2">
            <button class="rounded-full bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">Salvar</button>
            <a href="{{ route('admin.categorias.index') }}" class="rounded-full border border-stone-300 px-6 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50">Cancelar</a>
        </div>
    </form>
@endsection
