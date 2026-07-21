@extends('layouts.app')

@section('title', 'Carrinho - '.$storeSettings['name'])

@section('content')
    <h1 class="text-2xl font-bold text-stone-900">Seu carrinho</h1>

    @if($cart->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-stone-300 bg-white p-12 text-center">
            <div class="text-5xl">🛒</div>
            <p class="mt-4 text-stone-500">Seu carrinho está vazio.</p>
            <a href="{{ route('menu.index') }}" class="mt-6 inline-block rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">Ver cardápio</a>
        </div>
    @else
        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                @foreach($cart->items() as $item)
                    <div class="flex gap-4 rounded-2xl border border-stone-200 bg-white p-4">
                        <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-stone-100">
                            @if($item['image'])
                                <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full w-full place-items-center text-3xl">🍽️</div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="font-semibold text-stone-900">{{ $item['name'] }}</p>
                                    @if(!empty($item['addons']))
                                        <p class="mt-0.5 text-xs text-stone-500">
                                            {{ collect($item['addons'])->pluck('name')->join(', ') }}
                                        </p>
                                    @endif
                                    @if(!empty($item['notes']))
                                        <p class="mt-0.5 text-xs italic text-stone-400">"{{ $item['notes'] }}"</p>
                                    @endif
                                    <p class="mt-1 text-sm text-stone-500">{{ brl($item['unit_price']) }} un.</p>
                                </div>
                                <form action="{{ route('cart.destroy', $item['row_id']) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-sm text-stone-400 hover:text-red-500">Remover</button>
                                </form>
                            </div>
                            <div class="mt-auto flex items-center justify-between pt-3">
                                <form action="{{ route('cart.update', $item['row_id']) }}" method="POST" class="flex items-center gap-2">
                                    @csrf @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" max="50"
                                           class="w-16 rounded-lg border border-stone-300 px-2 py-1 text-center text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                    <button class="rounded-lg border border-stone-300 px-3 py-1 text-sm font-medium text-stone-600 hover:border-amber-400 hover:text-amber-600">Atualizar</button>
                                </form>
                                <span class="text-lg font-bold text-stone-900">{{ brl($item['line_total']) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="text-sm text-stone-400 hover:text-red-500">Esvaziar carrinho</button>
                </form>
            </div>

            <div class="h-fit rounded-2xl border border-stone-200 bg-white p-6">
                <h2 class="text-lg font-bold text-stone-900">Resumo</h2>
                <div class="mt-4 flex justify-between text-sm text-stone-600">
                    <span>Subtotal ({{ $cart->count() }} itens)</span>
                    <span class="font-semibold text-stone-900">{{ brl($cart->subtotal()) }}</span>
                </div>
                @if(($storeSettings['service_fee_percent'] ?? 0) > 0)
                    <p class="mt-2 text-xs text-stone-400">Taxa de serviço de {{ rtrim(rtrim(number_format($storeSettings['service_fee_percent'],2,',','.'),'0'),',') }}% será aplicada no checkout.</p>
                @endif
                <a href="{{ route('checkout.create') }}" class="mt-6 block rounded-full bg-amber-500 px-6 py-3 text-center font-semibold text-white transition hover:bg-amber-600">Finalizar pedido</a>
                <a href="{{ route('menu.index') }}" class="mt-3 block text-center text-sm text-stone-500 hover:text-amber-600">Continuar comprando</a>
            </div>
        </div>
    @endif
@endsection
