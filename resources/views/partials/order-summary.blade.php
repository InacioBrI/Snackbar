<div class="rounded-2xl border border-stone-200 bg-white p-6">
    <h2 class="text-lg font-bold text-stone-900">Itens do pedido</h2>
    <div class="mt-4 divide-y divide-stone-100">
        @foreach($order->items as $item)
            <div class="flex justify-between gap-3 py-3">
                <div>
                    <p class="font-medium text-stone-800">{{ $item->quantity }}× {{ $item->product_name }}</p>
                    @if(!empty($item->addons))
                        <p class="text-xs text-stone-500">{{ collect($item->addons)->pluck('name')->join(', ') }}</p>
                    @endif
                </div>
                <span class="whitespace-nowrap font-medium text-stone-800">{{ brl($item->line_total) }}</span>
            </div>
        @endforeach
    </div>
    <div class="mt-4 space-y-1 border-t border-stone-100 pt-4 text-sm">
        <div class="flex justify-between text-stone-600"><span>Subtotal</span><span>{{ brl($order->subtotal) }}</span></div>
        <div class="flex justify-between text-stone-600"><span>Taxa de serviço</span><span>{{ brl($order->service_fee) }}</span></div>
        <div class="flex justify-between pt-2 text-lg font-bold text-stone-900"><span>Total</span><span>{{ brl($order->total) }}</span></div>
    </div>
</div>
