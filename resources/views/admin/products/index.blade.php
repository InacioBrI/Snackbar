@extends('layouts.admin')

@section('title', 'Produtos')
@section('heading', 'Produtos')

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form action="{{ route('admin.produtos.index') }}" method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar produto..."
                   class="rounded-full border border-stone-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            <select name="category" class="rounded-full border border-stone-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <option value="">Todas categorias</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="rounded-full border border-stone-300 px-5 py-2 text-sm font-medium text-stone-600 hover:bg-stone-50">Filtrar</button>
        </form>
        <a href="{{ route('admin.produtos.create') }}" class="rounded-full bg-amber-500 px-5 py-2.5 text-center text-sm font-semibold text-white hover:bg-amber-600">+ Novo produto</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr>
                    <th class="px-4 py-3">Produto</th>
                    <th class="px-4 py-3">Categoria</th>
                    <th class="px-4 py-3">Preço</th>
                    <th class="px-4 py-3">Estoque</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($products as $product)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-stone-100">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" class="h-full w-full object-cover" alt="">
                                    @else
                                        <div class="grid h-full w-full place-items-center">🍽️</div>
                                    @endif
                                </div>
                                <span class="font-medium text-stone-800">{{ $product->name }}
                                    @if($product->is_featured)<span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700">Destaque</span>@endif
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-stone-500">{{ $product->category->name }}</td>
                        <td class="px-4 py-3 text-stone-700">{{ brl($product->price) }}</td>
                        <td class="px-4 py-3 text-stone-500">{{ $product->track_stock ? $product->stock : '—' }}</td>
                        <td class="px-4 py-3">
                            @if($product->is_active)
                                <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Ativo</span>
                            @else
                                <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-500">Inativo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.produtos.edit', $product) }}" class="rounded-lg border border-stone-200 px-3 py-1 text-xs font-medium text-stone-600 hover:border-amber-400 hover:text-amber-600">Editar</a>
                                <form action="{{ route('admin.produtos.destroy', $product) }}" method="POST" onsubmit="return confirm('Excluir este produto?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg border border-stone-200 px-3 py-1 text-xs font-medium text-red-600 hover:border-red-300 hover:bg-red-50">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-stone-400">Nenhum produto encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection
