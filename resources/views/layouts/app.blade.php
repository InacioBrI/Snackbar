<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $storeSettings['name'] ?? 'Lanchonete')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-800 antialiased">
    <header class="sticky top-0 z-40 border-b border-stone-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                @if(!empty($storeSettings['logo']))
                    <img src="{{ asset('storage/'.$storeSettings['logo']) }}" alt="Logo" class="h-10 w-10 rounded-full object-cover">
                @else
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-amber-500 text-lg font-bold text-white">🍔</span>
                @endif
                <span class="text-lg font-bold text-stone-900">{{ $storeSettings['name'] ?? 'Lanchonete' }}</span>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                <a href="{{ route('home') }}" class="hover:text-amber-600">Início</a>
                <a href="{{ route('menu.index') }}" class="hover:text-amber-600">Cardápio</a>
                <a href="{{ route('track.index') }}" class="hover:text-amber-600">Acompanhar pedido</a>
            </nav>

            <div class="flex items-center gap-3">
                <form action="{{ route('menu.index') }}" method="GET" class="hidden md:block">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar produto..."
                           class="w-48 rounded-full border border-stone-300 bg-stone-50 px-4 py-1.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </form>
                <a href="{{ route('cart.index') }}" class="relative grid h-10 w-10 place-items-center rounded-full bg-stone-100 hover:bg-amber-100">
                    <span class="text-lg">🛒</span>
                    @if($cart->count() > 0)
                        <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-amber-500 px-1 text-xs font-bold text-white">{{ $cart->count() }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    @if(session('success') || session('error') || $errors->any())
        <div class="mx-auto max-w-6xl px-4 pt-4">
            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <main class="mx-auto max-w-6xl px-4 py-6">
        @yield('content')
    </main>

    <footer class="mt-12 border-t border-stone-200 bg-white">
        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 text-sm text-stone-600 md:grid-cols-3">
            <div>
                <p class="text-base font-bold text-stone-900">{{ $storeSettings['name'] ?? 'Lanchonete' }}</p>
                <p class="mt-2">{{ $storeSettings['about'] ?? '' }}</p>
            </div>
            <div>
                <p class="font-semibold text-stone-900">Contato</p>
                @if(!empty($storeSettings['phone']))<p class="mt-2">📞 {{ $storeSettings['phone'] }}</p>@endif
                @if(!empty($storeSettings['address']))<p class="mt-1">📍 {{ $storeSettings['address'] }}</p>@endif
            </div>
            <div>
                <p class="font-semibold text-stone-900">Horário</p>
                <p class="mt-2">{{ $storeSettings['hours'] ?? '' }}</p>
                <a href="{{ route('admin.login') }}" class="mt-4 inline-block text-xs text-stone-400 hover:text-amber-600">Área do administrador</a>
            </div>
        </div>
        <div class="border-t border-stone-100 py-4 text-center text-xs text-stone-400">
            © {{ date('Y') }} {{ $storeSettings['name'] ?? 'Lanchonete' }}. Todos os direitos reservados.
        </div>
    </footer>
</body>
</html>
