@extends('layouts.app')

@section('title', 'Pedido confirmado - '.$order->order_number)

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="rounded-2xl border border-green-200 bg-green-50 p-6 text-center">
            <div class="text-5xl">✅</div>
            <h1 class="mt-3 text-2xl font-bold text-stone-900">Pedido confirmado!</h1>
            <p class="mt-1 text-stone-600">Obrigado, {{ $order->customer->name }}. Recebemos o seu pedido.</p>
            <p class="mt-4 inline-block rounded-full bg-white px-5 py-2 text-lg font-bold text-stone-900">#{{ $order->order_number }}</p>
            <p class="mt-2 text-sm text-stone-500">
                Pagamento: <span class="font-semibold">{{ $order->paymentMethodLabel() }}</span> —
                @if($order->isPaid())
                    <span class="font-semibold text-green-600">pago</span>
                @else
                    <span class="font-semibold text-amber-600">aguardando confirmação</span>
                @endif
            </p>
        </div>

        <div class="mt-6 rounded-2xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-bold text-stone-900">Status</h2>
            <div class="mt-4">@include('partials.order-status', ['order' => $order])</div>
            <div class="mt-4 grid gap-1 text-sm text-stone-600">
                <p><span class="text-stone-400">Local:</span> {{ $order->location }}</p>
                @if($order->notes)<p><span class="text-stone-400">Observações:</span> {{ $order->notes }}</p>@endif
            </div>
        </div>

        <div class="mt-6">@include('partials.order-summary', ['order' => $order])</div>

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('track.show', $order) }}" class="rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">Acompanhar pedido</a>
            <a href="{{ route('menu.index') }}" class="rounded-full border border-stone-300 px-6 py-3 text-sm font-semibold text-stone-700 hover:border-amber-400 hover:text-amber-600">Fazer novo pedido</a>
        </div>
    </div>
@endsection
