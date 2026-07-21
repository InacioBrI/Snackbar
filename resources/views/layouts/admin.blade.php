<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel') - {{ $storeSettings['name'] ?? 'Lanchonete' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 font-sans text-stone-800" x-data="{ sidebar: false }">
    @php
        $nav = [
            ['admin.dashboard', 'Dashboard', '📊', ['admin.dashboard']],
            ['admin.pedidos.index', 'Pedidos', '🧾', ['admin.pedidos.*']],
            ['admin.produtos.index', 'Produtos', '🍔', ['admin.produtos.*']],
            ['admin.categorias.index', 'Categorias', '🗂️', ['admin.categorias.*']],
            ['admin.adicionais.index', 'Adicionais', '➕', ['admin.adicionais.*']],
            ['admin.relatorios.index', 'Relatórios', '📈', ['admin.relatorios.*']],
            ['admin.administradores.index', 'Administradores', '👥', ['admin.administradores.*']],
            ['admin.configuracoes.edit', 'Configurações', '⚙️', ['admin.configuracoes.*']],
        ];
    @endphp

    <div x-show="sidebar" @click="sidebar = false" x-cloak class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-stone-200 bg-white transition-transform lg:translate-x-0">
        <div class="flex items-center gap-2 border-b border-stone-200 px-5 py-4">
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-amber-500 text-lg text-white">🍔</span>
            <span class="font-bold text-stone-900">{{ $storeSettings['name'] ?? 'Admin' }}</span>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto p-3">
            @foreach($nav as [$route, $label, $icon, $patterns])
                <a href="{{ route($route) }}" @class([
                    'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                    'bg-amber-50 text-amber-700' => request()->routeIs($patterns),
                    'text-stone-600 hover:bg-stone-100' => ! request()->routeIs($patterns),
                ])>
                    <span>{{ $icon }}</span> {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="border-t border-stone-200 p-3">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-stone-600 hover:bg-stone-100">🌐 Ver loja</a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-red-600 hover:bg-red-50">🚪 Sair</button>
            </form>
        </div>
    </aside>

    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-stone-200 bg-white px-4 py-3">
            <button @click="sidebar = !sidebar" class="rounded-lg p-2 text-stone-600 hover:bg-stone-100 lg:hidden">☰</button>
            <h1 class="text-lg font-bold text-stone-900">@yield('heading', 'Painel')</h1>
            <div class="text-sm text-stone-500">Olá, {{ auth('admin')->user()->name }}</div>
        </header>

        <main class="p-4 md:p-6">
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
