@extends('layouts.admin')

@section('title', 'Pedido '.$order->order_number)
@section('heading', 'Pedido #'.$order->order_number)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.pedidos.index') }}" class="text-sm text-stone-500 hover:text-amber-600">← Voltar aos pedidos</a>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-stone-200 bg-white p-6">
                <h2 class="font-bold text-stone-900">Itens</h2>
                <div class="mt-4 divide-y divide-stone-100">
                    @foreach($order->items as $item)
                        <div class="flex justify-between gap-3 py-3 text-sm">
                            <div>
                                <p class="font-medium text-stone-800">{{ $item->quantity }}× {{ $item->product_name }}</p>
                                @if(!empty($item->addons))
                                    <p class="text-xs text-stone-500">{{ collect($item->addons)->pluck('name')->join(', ') }}</p>
                                @endif
                                <p class="text-xs text-stone-400">{{ brl($item->unit_price) }} un.</p>
                            </div>
                            <span class="font-medium text-stone-800">{{ brl($item->line_total) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 space-y-1 border-t border-stone-100 pt-4 text-sm">
                    <div class="flex justify-between text-stone-600"><span>Subtotal</span><span>{{ brl($order->subtotal) }}</span></div>
                    <div class="flex justify-between text-stone-600"><span>Taxa de serviço</span><span>{{ brl($order->service_fee) }}</span></div>
                    <div class="flex justify-between pt-2 text-lg font-bold text-stone-900"><span>Total</span><span>{{ brl($order->total) }}</span></div>
                </div>
            </div>

            @if($order->notes)
                <div class="rounded-2xl border border-stone-200 bg-white p-6">
                    <h2 class="font-bold text-stone-900">Observações</h2>
                    <p class="mt-2 text-sm text-stone-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-stone-200 bg-white p-6">
                <h2 class="font-bold text-stone-900">Atualizar status</h2>
                <form action="{{ route('admin.pedidos.status', $order) }}" method="POST" class="mt-3">
                    @csrf @method('PATCH')
                    <select name="status" class="w-full rounded-xl border border-stone-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="mt-3 w-full rounded-full bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">Salvar status</button>
                </form>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-6 text-sm">
                <h2 class="font-bold text-stone-900">Cliente</h2>
                <div class="mt-3 space-y-1 text-stone-600">
                    <p><span class="text-stone-400">Nome:</span> {{ $order->customer->name }}</p>
                    <p><span class="text-stone-400">Telefone:</span> {{ $order->customer->phone }}</p>
                    <p><span class="text-stone-400">Local/Mesa:</span> {{ $order->location }}</p>
                </div>
                <h2 class="mt-4 font-bold text-stone-900">Pagamento</h2>
                <div class="mt-3 space-y-1 text-stone-600">
                    <p><span class="text-stone-400">Forma:</span> {{ $order->paymentMethodLabel() ?? '—' }}</p>
                    <p><span class="text-stone-400">Status:</span>
                        @if($order->isPaid())<span class="font-semibold text-green-600">Pago</span>
                        @elseif($order->payment_status === 'failed')<span class="font-semibold text-red-600">Falhou</span>
                        @else<span class="font-semibold text-amber-600">Pendente</span>@endif
                    </p>
                    @if($order->paid_at)<p><span class="text-stone-400">Pago em:</span> {{ $order->paid_at->format('d/m/Y H:i') }}</p>@endif
                    <p><span class="text-stone-400">Criado em:</span> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
