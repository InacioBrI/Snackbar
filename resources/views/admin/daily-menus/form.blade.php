@extends('layouts.admin')

@section('title', $dailyMenu->exists ? 'Editar cardápio do dia' : 'Novo cardápio do dia')
@section('heading', $dailyMenu->exists ? 'Editar cardápio do dia' : 'Novo cardápio do dia')

@php
    $oldProducts = old('products', array_keys($selected));
    $oldSoldOut = old('sold_out', collect($selected)->filter(fn ($p) => $p['is_sold_out'])->keys()->all());
@endphp

@section('content')
    <form action="{{ $dailyMenu->exists ? route('admin.cardapio-do-dia.update', $dailyMenu) : route('admin.cardapio-do-dia.store') }}" method="POST"
          class="grid max-w-4xl gap-5 lg:grid-cols-3">
        @csrf
        @if($dailyMenu->exists) @method('PUT') @endif

        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 lg:col-span-2">
            <div>
                <p class="text-sm font-semibold text-stone-900">Pratos do dia *</p>
                <p class="text-xs text-stone-500">Marque os pratos que estarão disponíveis nesta data. Use "esgotado" para tirar um prato do ar sem removê-lo do cardápio.</p>

                @if($products->isEmpty())
                    <p class="mt-4 rounded-xl border border-dashed border-stone-300 p-4 text-sm text-stone-500">
                        Nenhum prato ativo cadastrado.
                        <a href="{{ route('admin.produtos.create') }}" class="font-semibold text-amber-600 hover:underline">Cadastre um prato</a> primeiro.
                    </p>
                @else
                    <div class="mt-3 divide-y divide-stone-100 rounded-xl border border-stone-200">
                        @foreach($products as $product)
                            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                                <label class="flex items-center gap-3 text-sm text-stone-700">
                                    <input type="checkbox" name="products[]" value="{{ $product->id }}"
                                           @checked(in_array($product->id, array_map('intval', (array) $oldProducts), true))
                                           class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                                    <span>
                                        <span class="font-medium text-stone-800">{{ $product->name }}</span>
                                        <span class="text-stone-400">— {{ $product->category?->name }} · {{ brl($product->price) }}</span>
                                    </span>
                                </label>
                                <label class="flex items-center gap-2 text-xs text-stone-500">
                                    <input type="checkbox" name="sold_out[]" value="{{ $product->id }}"
                                           @checked(in_array($product->id, array_map('intval', (array) $oldSoldOut), true))
                                           class="h-4 w-4 rounded border-stone-300 text-red-500 focus:ring-red-500">
                                    Esgotado
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-stone-200 bg-white p-6">
                <div x-data="{ menuDate: '{{ old('menu_date', optional($dailyMenu->menu_date)->format('Y-m-d')) }}' }">
                    <label for="menu_date" class="text-sm font-medium text-stone-700">Data *</label>
                    <input id="menu_date" name="menu_date" type="date" required x-model="menuDate"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <p class="mt-1 text-xs text-stone-400" x-show="menuDate" x-cloak>
                        Data selecionada: <span class="font-medium text-stone-600" x-text="menuDate.split('-').reverse().join('/')"></span>
                    </p>
                </div>
                <div class="mt-3">
                    <label for="title" class="text-sm font-medium text-stone-700">Título</label>
                    <input id="title" name="title" value="{{ old('title', $dailyMenu->title) }}" placeholder="Ex.: Almoço de terça"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div class="mt-3">
                    <label for="notes" class="text-sm font-medium text-stone-700">Observações</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('notes', $dailyMenu->notes) }}</textarea>
                </div>
                <label class="mt-4 flex items-center gap-2 text-sm text-stone-700">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $dailyMenu->is_published)) class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                    Publicar no site
                </label>
                <p class="mt-2 text-xs text-stone-400">Quando o cardápio da data de hoje está publicado, apenas os pratos marcados ficam disponíveis para pedido.</p>
            </div>
        </div>

        <div class="flex gap-3 lg:col-span-3">
            <button class="rounded-full bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">Salvar cardápio</button>
            <a href="{{ route('admin.cardapio-do-dia.index') }}" class="rounded-full border border-stone-300 px-6 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50">Cancelar</a>
        </div>
    </form>
@endsection
