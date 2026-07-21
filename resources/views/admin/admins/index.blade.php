@extends('layouts.admin')

@section('title', 'Administradores')
@section('heading', 'Administradores')

@section('content')
    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.administradores.create') }}" class="rounded-full bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">+ Novo administrador</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach($admins as $admin)
                    <tr>
                        <td class="px-4 py-3 font-medium text-stone-800">{{ $admin->name }}
                            @if(auth('admin')->id() === $admin->id)<span class="ml-1 text-xs text-stone-400">(você)</span>@endif
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $admin->email }}</td>
                        <td class="px-4 py-3">
                            @if($admin->is_active)
                                <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Ativo</span>
                            @else
                                <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-500">Inativo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.administradores.edit', $admin) }}" class="rounded-lg border border-stone-200 px-3 py-1 text-xs font-medium text-stone-600 hover:border-amber-400 hover:text-amber-600">Editar</a>
                                @if(auth('admin')->id() !== $admin->id)
                                    <form action="{{ route('admin.administradores.destroy', $admin) }}" method="POST" onsubmit="return confirm('Excluir este administrador?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg border border-stone-200 px-3 py-1 text-xs font-medium text-red-600 hover:border-red-300 hover:bg-red-50">Excluir</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $admins->links() }}</div>
@endsection
