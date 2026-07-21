@extends('layouts.app')

@section('title', 'Acompanhar pedido - '.$storeSettings['name'])

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-bold text-stone-900">Acompanhar pedido</h1>
        <p class="mt-1 text-sm text-stone-500">Digite o número do pedido ou o telefone informado na compra.</p>

        <form action="{{ route('track.search') }}" method="POST" class="mt-6 flex gap-2">
            @csrf
            <input type="text" name="identifier" value="{{ old('identifier', $identifier ?? '') }}" required placeholder="Ex: P2507210001 ou (11) 99999-9999"
                   class="w-full rounded-full border border-stone-300 px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            <button class="rounded-full bg-amber-500 px-6 py-2.5 font-semibold text-white hover:bg-amber-600">Buscar</button>
        </form>

        @isset($orders)
            <div class="mt-8 space-y-3">
                <p class="text-sm text-stone-500">Pedidos encontrados:</p>
                @foreach($orders as $order)
                    <a href="{{ route('track.show', $order) }}" class="flex items-center justify-between rounded-2xl border border-stone-200 bg-white p-4 hover:border-amber-300">
                        <div>
                            <p class="font-semibold text-stone-900">#{{ $order->order_number }}</p>
                            <p class="text-xs text-stone-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{{ $order->statusLabel() }}</span>
                            <p class="mt-1 text-sm font-bold text-stone-800">{{ brl($order->total) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endisset
    </div>
@endsection
