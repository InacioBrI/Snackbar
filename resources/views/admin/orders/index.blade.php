@extends('layouts.admin')

@section('title', 'Pedidos')
@section('heading', 'Pedidos')

@section('content')
    <form action="{{ route('admin.pedidos.index') }}" method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Nº do pedido, cliente ou telefone"
               class="rounded-full border border-stone-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        <select name="status" class="rounded-full border border-stone-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            <option value="">Todos status</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="rounded-full border border-stone-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            <option value="">Pagamento (todos)</option>
            <option value="pending" @selected(request('payment_status') === 'pending')>Pendente</option>
            <option value="paid" @selected(request('payment_status') === 'paid')>Pago</option>
            <option value="failed" @selected(request('payment_status') === 'failed')>Falhou</option>
        </select>
        <button class="rounded-full border border-stone-300 px-5 py-2 text-sm font-medium text-stone-600 hover:bg-stone-50">Filtrar</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr>
                    <th class="px-4 py-3">Pedido</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Local</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Pagamento</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 font-medium text-stone-800">#{{ $order->order_number }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $order->customer->name }}<br><span class="text-xs text-stone-400">{{ $order->customer->phone }}</span></td>
                        <td class="px-4 py-3 text-stone-500">{{ $order->location }}</td>
                        <td class="px-4 py-3 font-medium text-stone-800">{{ brl($order->total) }}</td>
                        <td class="px-4 py-3">
                            @if($order->isPaid())
                                <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Pago</span>
                            @elseif($order->payment_status === 'failed')
                                <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Falhou</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">Pendente</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @include('admin.orders.partials.status-badge', ['status' => $order->status])
                        </td>
                        <td class="px-4 py-3 text-stone-500">{{ $order->created_at->format('d/m H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.pedidos.show', $order) }}" class="rounded-lg border border-stone-200 px-3 py-1 text-xs font-medium text-stone-600 hover:border-amber-400 hover:text-amber-600">Detalhes</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-stone-400">Nenhum pedido encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection
