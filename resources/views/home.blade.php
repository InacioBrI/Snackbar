@extends('layouts.app')

@section('title', $storeSettings['name'])

@section('content')
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 px-6 py-14 text-white shadow-lg md:px-12 md:py-20">
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-3xl font-extrabold leading-tight md:text-5xl">{{ $storeSettings['name'] }}</h1>
            <p class="mt-4 text-base text-white/90 md:text-lg">{{ $storeSettings['about'] }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('menu.index') }}" class="rounded-full bg-white px-6 py-3 text-sm font-bold text-orange-600 shadow transition hover:bg-stone-100">Ver cardápio</a>
                <a href="{{ route('track.index') }}" class="rounded-full border border-white/70 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10">Acompanhar pedido</a>
            </div>
            <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-white/90">
                @if($storeSettings['hours'])<span>🕒 {{ $storeSettings['hours'] }}</span>@endif
                @if($storeSettings['address'])<span>📍 {{ $storeSettings['address'] }}</span>@endif
            </div>
        </div>
        <div class="pointer-events-none absolute -right-10 -top-10 text-[12rem] opacity-20 md:text-[16rem]">🍔</div>
    </section>

    <section class="mt-10">
        <h2 class="text-xl font-bold text-stone-900">Categorias</h2>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
            @foreach($categories as $category)
                <a href="{{ route('menu.index') }}#categoria-{{ $category->slug }}"
                   class="rounded-2xl border border-stone-200 bg-white p-4 text-center shadow-sm transition hover:border-amber-300 hover:shadow-md">
                    <div class="text-3xl">🍴</div>
                    <div class="mt-2 font-semibold text-stone-800">{{ $category->name }}</div>
                    <div class="text-xs text-stone-400">{{ $category->products_count }} itens</div>
                </a>
            @endforeach
        </div>
    </section>

    @if($featured->isNotEmpty())
        <section class="mt-10">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-stone-900">Destaques</h2>
                <a href="{{ route('menu.index') }}" class="text-sm font-semibold text-amber-600 hover:underline">Ver tudo →</a>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($featured as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif
@endsection
