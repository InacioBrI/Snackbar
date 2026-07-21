@php
    $flow = ['new', 'preparing', 'ready', 'delivered'];
    $labels = \App\Models\Order::STATUSES;
    $currentIndex = array_search($order->status, $flow, true);
@endphp

@if($order->status === 'cancelled')
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
        Pedido cancelado.
    </div>
@else
    <div class="flex items-center justify-between">
        @foreach($flow as $i => $step)
            <div class="flex flex-1 flex-col items-center text-center">
                <div @class([
                    'grid h-9 w-9 place-items-center rounded-full text-sm font-bold',
                    'bg-amber-500 text-white' => $currentIndex !== false && $i <= $currentIndex,
                    'bg-stone-200 text-stone-500' => $currentIndex === false || $i > $currentIndex,
                ])>{{ $i + 1 }}</div>
                <span class="mt-1 text-xs {{ ($currentIndex !== false && $i <= $currentIndex) ? 'font-semibold text-amber-600' : 'text-stone-400' }}">{{ $labels[$step] }}</span>
            </div>
            @if(! $loop->last)
                <div class="mx-1 h-0.5 flex-1 {{ ($currentIndex !== false && $i < $currentIndex) ? 'bg-amber-500' : 'bg-stone-200' }}"></div>
            @endif
        @endforeach
    </div>
@endif
