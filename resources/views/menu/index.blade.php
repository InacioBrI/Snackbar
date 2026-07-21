@extends('layouts.app')

@section('title', 'Cardápio - '.$storeSettings['name'])

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl font-bold text-stone-900">Cardápio</h1>
        <form action="{{ route('menu.index') }}" method="GET" class="flex gap-2">
            <input type="search" name="q" value="{{ $search }}" placeholder="Buscar produto..."
                   class="w-full rounded-full border border-stone-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 md:w-72">
            <button class="rounded-full bg-amber-500 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-600">Buscar</button>
        </form>
    </div>

    @if($search !== '')
        <p class="mt-3 text-sm text-stone-500">Resultados para <span class="font-semibold text-stone-700">"{{ $search }}"</span>
            — <a href="{{ route('menu.index') }}" class="text-amber-600 hover:underline">limpar busca</a></p>
    @endif

    @if($categories->isEmpty())
        <div class="mt-10 rounded-2xl border border-dashed border-stone-300 bg-white p-10 text-center text-stone-500">
            Nenhum produto encontrado.
        </div>
    @else
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach($categories as $category)
                <a href="#categoria-{{ $category->slug }}" class="rounded-full border border-stone-200 bg-white px-4 py-1.5 text-sm font-medium text-stone-700 hover:border-amber-300 hover:text-amber-600">{{ $category->name }}</a>
            @endforeach
        </div>

        @foreach($categories as $category)
            <section id="categoria-{{ $category->slug }}" class="mt-10 scroll-mt-24">
                <h2 class="text-xl font-bold text-stone-900">{{ $category->name }}</h2>
                @if($category->description)<p class="text-sm text-stone-500">{{ $category->description }}</p>@endif
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($category->activeProducts as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
@endsection
