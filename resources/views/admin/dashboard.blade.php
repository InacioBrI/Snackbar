@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    @php($maxDay = max(1, $salesLast7Days->max('total')))
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Faturamento total</p>
            <p class="mt-1 text-2xl font-bold text-stone-900">{{ brl($totalRevenue) }}</p>
            <p class="mt-1 text-xs text-stone-400">{{ $totalSales }} vendas pagas</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Faturamento hoje</p>
            <p class="mt-1 text-2xl font-bold text-stone-900">{{ brl($todayRevenue) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Pedidos hoje</p>
            <p class="mt-1 text-2xl font-bold text-stone-900">{{ $ordersToday }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Ticket médio</p>
            <p class="mt-1 text-2xl font-bold text-stone-900">{{ brl($averageTicket) }}</p>
        </div>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 lg:col-span-2">
            <h2 class="font-bold text-stone-900">Faturamento (últimos 7 dias)</h2>
            <div class="mt-6 flex h-48 items-end justify-between gap-2">
                @foreach($salesLast7Days as $day)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="flex w-full flex-1 items-end">
                            <div class="w-full rounded-t-lg bg-amber-400" style="height: {{ max(4, round($day['total'] / $maxDay * 100)) }}%" title="{{ brl($day['total']) }}"></div>
                        </div>
                        <span class="text-[10px] text-stone-400">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <h2 class="font-bold text-stone-900">Pedidos por status</h2>
            <div class="mt-4 space-y-2">
                @foreach($statusCounts as $key => $count)
                    <a href="{{ route('admin.pedidos.index', ['status' => $key]) }}" class="flex items-center justify-between rounded-lg px-2 py-1.5 text-sm hover:bg-stone-50">
                        <span class="text-stone-600">{{ \App\Models\Order::STATUSES[$key] }}</span>
                        <span class="font-bold text-stone-900">{{ $count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <h2 class="font-bold text-stone-900">Produtos mais vendidos</h2>
            @if($topProducts->isEmpty())
                <p class="mt-4 text-sm text-stone-400">Nenhuma venda registrada ainda.</p>
            @else
                <div class="mt-4 space-y-3">
                    @foreach($topProducts as $product)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-stone-700">{{ $product->product_name }}</span>
                            <span class="text-stone-500">{{ $product->qty }} un · {{ brl($product->revenue) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-stone-900">Pedidos recentes</h2>
                <a href="{{ route('admin.pedidos.index') }}" class="text-sm font-semibold text-amber-600 hover:underline">Ver todos</a>
            </div>
            @if($recentOrders->isEmpty())
                <p class="mt-4 text-sm text-stone-400">Nenhum pedido ainda.</p>
            @else
                <div class="mt-4 divide-y divide-stone-100">
                    @foreach($recentOrders as $order)
                        <a href="{{ route('admin.pedidos.show', $order) }}" class="flex items-center justify-between py-2.5 text-sm hover:text-amber-600">
                            <span>
                                <span class="font-medium text-stone-800">#{{ $order->order_number }}</span>
                                <span class="text-stone-400"> · {{ $order->customer->name }}</span>
                            </span>
                            <span class="font-semibold text-stone-800">{{ brl($order->total) }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
