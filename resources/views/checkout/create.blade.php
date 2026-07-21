@extends('layouts.app')

@section('title', 'Checkout - '.$storeSettings['name'])

@section('content')
    <h1 class="text-2xl font-bold text-stone-900">Finalizar pedido</h1>
    <p class="mt-1 text-sm text-stone-500">Sem cadastro: preencha seus dados e escolha a forma de pagamento.</p>

    <form action="{{ route('checkout.store') }}" method="POST" class="mt-6 grid gap-6 lg:grid-cols-3">
        @csrf
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-stone-200 bg-white p-6">
                <h2 class="text-lg font-bold text-stone-900">Seus dados</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="text-sm font-medium text-stone-700">Nome *</label>
                        <input id="name" name="name" value="{{ old('name') }}" required
                               class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="phone" class="text-sm font-medium text-stone-700">Telefone *</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" required placeholder="(11) 99999-9999"
                               class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="location" class="text-sm font-medium text-stone-700">Mesa ou local de retirada *</label>
                        <input id="location" name="location" value="{{ old('location') }}" required placeholder="Ex: Mesa 12 / Balcão"
                               class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="notes" class="text-sm font-medium text-stone-700">Observações do pedido</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Alguma preferência ou observação?"
                                  class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-6">
                <h2 class="text-lg font-bold text-stone-900">Forma de pagamento</h2>
                <div class="mt-4 space-y-3">
                    @php($methods = ['pix' => ['PIX', 'Pague na hora com QR Code'], 'credit' => ['Cartão de Crédito', 'Aprovação imediata'], 'debit' => ['Cartão de Débito', 'Aprovação imediata']])
                    @foreach($storeSettings['payment_methods'] as $method)
                        @if(isset($methods[$method]))
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-stone-200 px-4 py-3 hover:border-amber-300 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                                <input type="radio" name="payment_method" value="{{ $method }}" required @checked(old('payment_method') === $method)
                                       class="h-4 w-4 border-stone-300 text-amber-500 focus:ring-amber-500">
                                <span>
                                    <span class="block font-semibold text-stone-800">{{ $methods[$method][0] }}</span>
                                    <span class="block text-xs text-stone-500">{{ $methods[$method][1] }}</span>
                                </span>
                            </label>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="h-fit rounded-2xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-bold text-stone-900">Resumo do pedido</h2>
            <div class="mt-4 space-y-2 text-sm">
                @foreach($cart->items() as $item)
                    <div class="flex justify-between text-stone-600">
                        <span>{{ $item['quantity'] }}× {{ $item['name'] }}</span>
                        <span>{{ brl($item['line_total']) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 space-y-1 border-t border-stone-100 pt-4 text-sm">
                <div class="flex justify-between text-stone-600"><span>Subtotal</span><span>{{ brl($cart->subtotal()) }}</span></div>
                <div class="flex justify-between text-stone-600"><span>Taxa de serviço</span><span>{{ brl($serviceFee) }}</span></div>
                <div class="flex justify-between pt-2 text-lg font-bold text-stone-900"><span>Total</span><span>{{ brl($total) }}</span></div>
            </div>
            <button type="submit" class="mt-6 block w-full rounded-full bg-amber-500 px-6 py-3 text-center font-semibold text-white transition hover:bg-amber-600">Ir para o pagamento</button>
            <a href="{{ route('cart.index') }}" class="mt-3 block text-center text-sm text-stone-500 hover:text-amber-600">Voltar ao carrinho</a>
        </div>
    </form>
@endsection
