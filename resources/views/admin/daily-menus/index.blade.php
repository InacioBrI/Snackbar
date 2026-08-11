@extends('layouts.admin')

@section('title', 'Cardápio do dia')
@section('heading', 'Cardápio do dia')

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-stone-500">
            @if($todayMenu)
                Cardápio de hoje ({{ $todayMenu->menu_date->format('d/m/Y') }}):
                <span class="font-medium text-stone-700">{{ $todayMenu->products_count ?? $todayMenu->products()->count() }} pratos</span>
                @if(! $todayMenu->is_published) <span class="text-amber-600">— ainda não publicado</span> @endif
            @else
                Nenhum cardápio cadastrado para hoje.
            @endif
        </p>
        <a href="{{ route('admin.cardapio-do-dia.create') }}" class="rounded-full bg-amber-500 px-5 py-2.5 text-center text-sm font-semibold text-white hover:bg-amber-600">+ Novo cardápio do dia</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3">Título</th>
                    <th class="px-4 py-3">Pratos</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($menus as $menu)
                    <tr>
                        <td class="px-4 py-3 font-medium text-stone-800">{{ $menu->menu_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $menu->title ?: '—' }}</td>
                        <td class="px-4 py-3 text-stone-500">{{ $menu->products_count }}</td>
                        <td class="px-4 py-3">
                            @if($menu->is_published)
                                <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Publicado</span>
                            @else
                                <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-500">Rascunho</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.cardapio-do-dia.edit', $menu) }}" class="rounded-lg border border-stone-200 px-3 py-1 text-xs font-medium text-stone-600 hover:border-amber-400 hover:text-amber-600">Editar</a>
                                <form action="{{ route('admin.cardapio-do-dia.destroy', $menu) }}" method="POST" onsubmit="return confirm('Excluir este cardápio do dia?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg border border-stone-200 px-3 py-1 text-xs font-medium text-red-600 hover:border-red-300 hover:bg-red-50">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-stone-400">Nenhum cardápio do dia cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $menus->links() }}</div>
@endsection
