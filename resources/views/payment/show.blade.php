@extends('layouts.app')

@section('title', 'Pagamento - Pedido '.$order->order_number)

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="rounded-2xl border border-stone-200 bg-white p-6 text-center">
            <p class="text-sm text-stone-500">Pedido</p>
            <p class="text-2xl font-bold text-stone-900">#{{ $order->order_number }}</p>
            <p class="mt-1 text-lg font-semibold text-amber-600">Total: {{ brl($order->total) }}</p>
        </div>

        @if($order->payment_method === 'pix' && $pix)
            <div class="mt-6 rounded-2xl border border-stone-200 bg-white p-6 text-center"
                 x-data="{ copied: false, code: @js($pix->pixPayload) }">
                <h2 class="text-lg font-bold text-stone-900">Pague com PIX</h2>
                <p class="mt-1 text-sm text-stone-500">{{ $pix->message }}</p>
                <img src="{{ $pix->pixQrCode }}" alt="QR Code PIX" class="mx-auto mt-4 h-64 w-64 rounded-xl border border-stone-100">
                <div class="mt-4">
                    <p class="break-all rounded-xl bg-stone-50 p-3 text-xs text-stone-600">{{ $pix->pixPayload }}</p>
                    <button type="button"
                            @click="navigator.clipboard.writeText(code); copied = true; setTimeout(() => copied = false, 2000)"
                            class="mt-3 w-full rounded-full border border-amber-500 px-6 py-2.5 text-sm font-semibold text-amber-600 hover:bg-amber-50">
                        <span x-show="!copied">Copiar código PIX</span>
                        <span x-show="copied" x-cloak>Copiado! ✓</span>
                    </button>
                </div>

                <form action="{{ route('payment.confirm-pix', $order) }}" method="POST" class="mt-4">
                    @csrf
                    <button class="w-full rounded-full bg-amber-500 px-6 py-3 font-semibold text-white transition hover:bg-amber-600">Já efetuei o pagamento</button>
                </form>
                <p class="mt-3 text-xs text-stone-400">Ambiente de demonstração: a confirmação é simulada. Em produção, o status é atualizado automaticamente pelo provedor.</p>
            </div>
        @elseif(in_array($order->payment_method, ['credit', 'debit']))
            <div class="mt-6 rounded-2xl border border-stone-200 bg-white p-6"
                 x-data="{ number: '', expiry: '', formatNumber() { this.number = this.number.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim().slice(0,19) }, formatExpiry() { this.expiry = this.expiry.replace(/\D/g,'').replace(/(.{2})(.+)/, '$1/$2').slice(0,7) } }">
                <h2 class="text-lg font-bold text-stone-900">{{ $order->paymentMethodLabel() }}</h2>
                <form action="{{ route('payment.process', $order) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="card_number" class="text-sm font-medium text-stone-700">Número do cartão</label>
                        <input id="card_number" name="card_number" x-model="number" @input="formatNumber" inputmode="numeric" placeholder="0000 0000 0000 0000" required
                               class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="card_holder" class="text-sm font-medium text-stone-700">Nome impresso no cartão</label>
                        <input id="card_holder" name="card_holder" placeholder="NOME SOBRENOME" required
                               class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 uppercase focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="card_expiry" class="text-sm font-medium text-stone-700">Validade</label>
                            <input id="card_expiry" name="card_expiry" x-model="expiry" @input="formatExpiry" placeholder="MM/AA" required
                                   class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </div>
                        <div>
                            <label for="card_cvv" class="text-sm font-medium text-stone-700">CVV</label>
                            <input id="card_cvv" name="card_cvv" inputmode="numeric" maxlength="4" placeholder="123" required
                                   class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </div>
                    </div>
                    <button class="w-full rounded-full bg-amber-500 px-6 py-3 font-semibold text-white transition hover:bg-amber-600">Pagar {{ brl($order->total) }}</button>
                    <p class="text-center text-xs text-stone-400">Ambiente de demonstração: nenhum dado de cartão real é processado ou armazenado.</p>
                </form>
            </div>
        @endif

        <a href="{{ route('track.show', $order) }}" class="mt-4 block text-center text-sm text-stone-500 hover:text-amber-600">Acompanhar este pedido</a>
    </div>
@endsection
