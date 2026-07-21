<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Painel Administrativo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-stone-100 font-sans text-stone-800">
    <div class="w-full max-w-sm px-4">
        <div class="mb-6 text-center">
            <span class="inline-grid h-14 w-14 place-items-center rounded-2xl bg-amber-500 text-2xl text-white">🍔</span>
            <h1 class="mt-3 text-xl font-bold text-stone-900">Painel Administrativo</h1>
            <p class="text-sm text-stone-500">{{ $storeSettings['name'] ?? 'Lanchonete' }}</p>
        </div>

        <form action="{{ route('admin.login.store') }}" method="POST" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            @csrf
            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
            <div>
                <label for="email" class="text-sm font-medium text-stone-700">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div class="mt-4">
                <label for="password" class="text-sm font-medium text-stone-700">Senha</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <label class="mt-4 flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                Manter conectado
            </label>
            <button class="mt-6 w-full rounded-full bg-amber-500 px-6 py-2.5 font-semibold text-white transition hover:bg-amber-600">Entrar</button>
        </form>

        <a href="{{ route('home') }}" class="mt-4 block text-center text-sm text-stone-500 hover:text-amber-600">← Voltar para a loja</a>
    </div>
</body>
</html>
