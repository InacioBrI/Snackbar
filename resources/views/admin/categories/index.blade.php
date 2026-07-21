@extends('layouts.admin')

@section('title', 'Categorias')
@section('heading', 'Categorias')

@section('content')
    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.categorias.create') }}" class="rounded-full bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">+ Nova categoria</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr>
                    <th class="px-4 py-3">Ordem</th>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Produtos</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-4 py-3 text-stone-500">{{ $category->sort_order }}</td>
                        <td class="px-4 py-3 font-medium text-stone-800">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-stone-500">{{ $category->products_count }}</td>
                        <td class="px-4 py-3">
                            @if($category->is_active)
                                <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Ativa</span>
                            @else
                                <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-500">Inativa</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.categorias.edit', $category) }}" class="rounded-lg border border-stone-200 px-3 py-1 text-xs font-medium text-stone-600 hover:border-amber-400 hover:text-amber-600">Editar</a>
                                <form action="{{ route('admin.categorias.destroy', $category) }}" method="POST" onsubmit="return confirm('Excluir esta categoria?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg border border-stone-200 px-3 py-1 text-xs font-medium text-red-600 hover:border-red-300 hover:bg-red-50">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-stone-400">Nenhuma categoria cadastrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
@endsection
