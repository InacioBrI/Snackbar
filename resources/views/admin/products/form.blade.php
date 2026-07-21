@extends('layouts.admin')

@section('title', $product->exists ? 'Editar produto' : 'Novo produto')
@section('heading', $product->exists ? 'Editar produto' : 'Novo produto')

@section('content')
    <form action="{{ $product->exists ? route('admin.produtos.update', $product) : route('admin.produtos.store') }}" method="POST" enctype="multipart/form-data"
          class="grid max-w-4xl gap-5 lg:grid-cols-3">
        @csrf
        @if($product->exists) @method('PUT') @endif

        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 lg:col-span-2">
            <div>
                <label for="name" class="text-sm font-medium text-stone-700">Nome *</label>
                <input id="name" name="name" value="{{ old('name', $product->name) }}" required
                       class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="description" class="text-sm font-medium text-stone-700">Descrição</label>
                <textarea id="description" name="description" rows="3"
                          class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="category_id" class="text-sm font-medium text-stone-700">Categoria *</label>
                    <select id="category_id" name="category_id" required
                            class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">Selecione...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="price" class="text-sm font-medium text-stone-700">Preço (R$) *</label>
                    <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price) }}" required
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            @if($addons->isNotEmpty())
                <div>
                    <p class="text-sm font-medium text-stone-700">Adicionais disponíveis</p>
                    <div class="mt-2 grid gap-2 sm:grid-cols-2">
                        @foreach($addons as $addon)
                            <label class="flex items-center gap-2 rounded-xl border border-stone-200 px-3 py-2 text-sm">
                                <input type="checkbox" name="addons[]" value="{{ $addon->id }}"
                                       @checked(in_array($addon->id, old('addons', $selectedAddons)))
                                       class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                                {{ $addon->name }} <span class="text-stone-400">({{ brl($addon->price) }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-stone-200 bg-white p-6" x-data="{ track: {{ old('track_stock', $product->track_stock) ? 'true' : 'false' }} }">
                <p class="text-sm font-semibold text-stone-900">Publicação</p>
                <label class="mt-3 flex items-center gap-2 text-sm text-stone-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                    Produto ativo
                </label>
                <label class="mt-2 flex items-center gap-2 text-sm text-stone-700">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                    Destacar na home
                </label>
                <label class="mt-2 flex items-center gap-2 text-sm text-stone-700">
                    <input type="checkbox" name="track_stock" value="1" x-model="track" @checked(old('track_stock', $product->track_stock)) class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                    Controlar estoque
                </label>
                <div class="mt-3" x-show="track" x-cloak>
                    <label for="stock" class="text-sm font-medium text-stone-700">Quantidade em estoque</label>
                    <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock ?? 0) }}"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div class="mt-3">
                    <label for="sort_order" class="text-sm font-medium text-stone-700">Ordem</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $product->sort_order ?? 0) }}"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-6">
                <p class="text-sm font-semibold text-stone-900">Imagem</p>
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" class="mt-3 aspect-square w-full rounded-xl object-cover" alt="">
                @endif
                <input type="file" name="image" accept="image/*" class="mt-3 w-full text-sm text-stone-600 file:mr-3 file:rounded-full file:border-0 file:bg-amber-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-amber-700 hover:file:bg-amber-100">
                <p class="mt-2 text-xs text-stone-400">JPG/PNG até 4MB.</p>
            </div>
        </div>

        <div class="flex gap-3 lg:col-span-3">
            <button class="rounded-full bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">Salvar produto</button>
            <a href="{{ route('admin.produtos.index') }}" class="rounded-full border border-stone-300 px-6 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50">Cancelar</a>
        </div>
    </form>
@endsection
