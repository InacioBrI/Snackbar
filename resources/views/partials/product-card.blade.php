@php($available = $product->isAvailable())
<div class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition hover:shadow-md">
    <a href="{{ route('menu.show', $product) }}" class="block aspect-[4/3] overflow-hidden bg-stone-100">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
        @else
            <div class="grid h-full w-full place-items-center text-5xl">🍽️</div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-4">
        <a href="{{ route('menu.show', $product) }}" class="font-semibold text-stone-900 hover:text-amber-600">{{ $product->name }}</a>
        <p class="mt-1 line-clamp-2 flex-1 text-sm text-stone-500">{{ $product->description }}</p>
        <div class="mt-3 flex items-center justify-between">
            <span class="text-lg font-bold text-amber-600">{{ brl($product->price) }}</span>
            @if($available)
                <a href="{{ route('menu.show', $product) }}" class="rounded-full bg-amber-500 px-4 py-1.5 text-sm font-semibold text-white transition hover:bg-amber-600">Pedir</a>
            @else
                <span class="rounded-full bg-stone-100 px-4 py-1.5 text-sm font-medium text-stone-400">Indisponível</span>
            @endif
        </div>
    </div>
</div>
