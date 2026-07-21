@extends('layouts.app')

@section('title', 'Pedido '.$order->order_number)

@section('content')
    <div class="mx-auto max-w-2xl"
         x-data="{ status: @js($order->status), payment: @js($order->payment_status) }"
         x-init="setInterval(async () => {
             try {
                 const res = await fetch('{{ route('payment.status', $order) }}');
                 const data = await res.json();
                 if (data.status !== status || data.payment_status !== payment) { location.reload(); }
             } catch (e) {}
         }, 15000)">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-stone-500">Acompanhamento do pedido</p>
                <h1 class="text-2xl font-bold text-stone-900">#{{ $order->order_number }}</h1>
            </div>
            <span class="rounded-full bg-amber-100 px-4 py-1.5 text-sm font-semibold text-amber-700">{{ $order->statusLabel() }}</span>
        </div>

        <div class="mt-6 rounded-2xl border border-stone-200 bg-white p-6">
            @include('partials.order-status', ['order' => $order])
        </div>

        <div class="mt-6 rounded-2xl border border-stone-200 bg-white p-6 text-sm text-stone-600">
            <div class="grid gap-2 sm:grid-cols-2">
                <p><span class="text-stone-400">Cliente:</span> {{ $order->customer->name }}</p>
                <p><span class="text-stone-400">Telefone:</span> {{ $order->customer->phone }}</p>
                <p><span class="text-stone-400">Local:</span> {{ $order->location }}</p>
                <p><span class="text-stone-400">Realizado em:</span> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><span class="text-stone-400">Pagamento:</span> {{ $order->paymentMethodLabel() }}
                    @if($order->isPaid())
                        <span class="font-semibold text-green-600">(pago)</span>
                    @else
                        <span class="font-semibold text-amber-600">(pendente)</span>
                    @endif
                </p>
            </div>
            @if($order->notes)<p class="mt-2"><span class="text-stone-400">Observações:</span> {{ $order->notes }}</p>@endif
        </div>

        @if(! $order->isPaid() && $order->status !== 'cancelled')
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Pagamento pendente. <a href="{{ route('payment.show', $order) }}" class="font-semibold underline">Finalizar pagamento</a>
            </div>
        @endif

        <div class="mt-6">@include('partials.order-summary', ['order' => $order])</div>

        <a href="{{ route('track.index') }}" class="mt-4 block text-center text-sm text-stone-500 hover:text-amber-600">Buscar outro pedido</a>
    </div>
@endsection
