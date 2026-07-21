@extends('layouts.admin')

@section('title', 'Relatórios')
@section('heading', 'Relatórios')

@section('content')
    <form action="{{ route('admin.relatorios.index') }}" method="GET" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4">
        <div>
            <label for="from" class="text-xs font-medium text-stone-500">De</label>
            <input id="from" name="from" type="date" value="{{ $from }}" class="mt-1 block rounded-xl border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <div>
            <label for="to" class="text-xs font-medium text-stone-500">Até</label>
            <input id="to" name="to" type="date" value="{{ $to }}" class="mt-1 block rounded-xl border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <button class="rounded-full bg-amber-500 px-6 py-2 text-sm font-semibold text-white hover:bg-amber-600">Filtrar</button>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Faturamento</p>
            <p class="mt-1 text-2xl font-bold text-stone-900">{{ brl($revenue) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Pedidos pagos</p>
            <p class="mt-1 text-2xl font-bold text-stone-900">{{ $ordersCount }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Ticket médio</p>
            <p class="mt-1 text-2xl font-bold text-stone-900">{{ brl($averageTicket) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Itens vendidos</p>
            <p class="mt-1 text-2xl font-bold text-stone-900">{{ $itemsSold }}</p>
        </div>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <h2 class="font-bold text-stone-900">Vendas por dia</h2>
            @if($byDay->isEmpty())
                <p class="mt-4 text-sm text-stone-400">Sem vendas no período.</p>
            @else
                <table class="mt-4 w-full text-left text-sm">
                    <thead class="text-xs uppercase text-stone-400">
                        <tr><th class="py-2">Dia</th><th class="py-2">Pedidos</th><th class="py-2 text-right">Faturamento</th></tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($byDay as $row)
                            <tr>
                                <td class="py-2 text-stone-600">{{ \Illuminate\Support\Carbon::parse($row->day)->format('d/m/Y') }}</td>
                                <td class="py-2 text-stone-600">{{ $row->orders }}</td>
                                <td class="py-2 text-right font-medium text-stone-800">{{ brl($row->revenue) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-stone-200 bg-white p-5">
                <h2 class="font-bold text-stone-900">Por forma de pagamento</h2>
                @if($byPaymentMethod->isEmpty())
                    <p class="mt-4 text-sm text-stone-400">Sem dados.</p>
                @else
                    <div class="mt-4 space-y-2 text-sm">
                        @foreach($byPaymentMethod as $row)
                            <div class="flex justify-between">
                                <span class="text-stone-600">{{ \App\Models\Order::PAYMENT_METHODS[$row->payment_method] ?? $row->payment_method }} ({{ $row->orders }})</span>
                                <span class="font-medium text-stone-800">{{ brl($row->revenue) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-5">
                <h2 class="font-bold text-stone-900">Produtos mais vendidos</h2>
                @if($topProducts->isEmpty())
                    <p class="mt-4 text-sm text-stone-400">Sem dados.</p>
                @else
                    <div class="mt-4 space-y-2 text-sm">
                        @foreach($topProducts as $product)
                            <div class="flex justify-between">
                                <span class="text-stone-600">{{ $product->product_name }}</span>
                                <span class="text-stone-500">{{ $product->qty }} un · {{ brl($product->revenue) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
