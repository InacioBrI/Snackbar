@extends('layouts.app')

@section('title', $product->name.' - '.$storeSettings['name'])

@section('content')
    <nav class="mb-4 text-sm text-stone-500">
        <a href="{{ route('menu.index') }}" class="hover:text-amber-600">Cardápio</a>
        <span class="mx-1">/</span>
        <a href="{{ route('menu.index') }}#categoria-{{ $product->category->slug }}" class="hover:text-amber-600">{{ $product->category->name }}</a>
    </nav>

    <div class="grid gap-8 md:grid-cols-2">
        <div class="overflow-hidden rounded-3xl border border-stone-200 bg-white">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
            @else
                <div class="grid aspect-square w-full place-items-center bg-stone-100 text-8xl">🍽️</div>
            @endif
        </div>

        <div x-data="{
            basePrice: {{ (float) $product->price }},
            quantity: 1,
            addons: {},
            addonTotal() {
                return Object.values(this.addons).reduce((sum, p) => sum + Number(p || 0), 0);
            },
            total() {
                return ((this.basePrice + this.addonTotal()) * this.quantity);
            },
            formatBRL(v) {
                return 'R$ ' + v.toFixed(2).replace('.', ',');
            }
        }">
            <h1 class="text-3xl font-bold text-stone-900">{{ $product->name }}</h1>
            <p class="mt-3 text-stone-600">{{ $product->description }}</p>
            <p class="mt-4 text-2xl font-bold text-amber-600">{{ brl($product->price) }}</p>

            @if(! $product->isAvailable())
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    Este produto está indisponível no momento.
                </div>
            @else
                <form action="{{ route('cart.store') }}" method="POST" class="mt-6 space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if($product->addons->isNotEmpty())
                        <div>
                            <h3 class="font-semibold text-stone-900">Adicionais</h3>
                            <div class="mt-2 space-y-2">
                                @foreach($product->addons as $addon)
                                    <label class="flex cursor-pointer items-center justify-between rounded-xl border border-stone-200 bg-white px-4 py-3 hover:border-amber-300">
                                        <span class="flex items-center gap-3">
                                            <input type="checkbox" name="addons[]" value="{{ $addon->id }}"
                                                   @change="addons[{{ $addon->id }}] = $event.target.checked ? {{ (float) $addon->price }} : 0"
                                                   class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                                            <span class="text-stone-700">{{ $addon->name }}</span>
                                        </span>
                                        <span class="text-sm font-medium text-stone-500">+ {{ brl($addon->price) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label for="notes" class="font-semibold text-stone-900">Observações</label>
                        <textarea id="notes" name="notes" rows="2" maxlength="255" placeholder="Ex: sem cebola, ponto da carne..."
                                  class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"></textarea>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center rounded-full border border-stone-300">
                            <button type="button" @click="quantity = Math.max(1, quantity - 1)" class="grid h-10 w-10 place-items-center text-lg text-stone-600 hover:text-amber-600">−</button>
                            <input type="number" name="quantity" x-model.number="quantity" min="1" max="50" class="w-12 border-none bg-transparent text-center focus:outline-none focus:ring-0">
                            <button type="button" @click="quantity = Math.min(50, quantity + 1)" class="grid h-10 w-10 place-items-center text-lg text-stone-600 hover:text-amber-600">+</button>
                        </div>
                        <button type="submit" class="flex flex-1 items-center justify-between rounded-full bg-amber-500 px-6 py-3 font-semibold text-white transition hover:bg-amber-600">
                            <span>Adicionar ao carrinho</span>
                            <span x-text="formatBRL(total())"></span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @if($related->isNotEmpty())
        <section class="mt-12">
            <h2 class="text-xl font-bold text-stone-900">Você também pode gostar</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($related as $item)
                    @include('partials.product-card', ['product' => $item])
                @endforeach
            </div>
        </section>
    @endif
@endsection
