@php
    $map = [
        'new' => ['bg-blue-100 text-blue-700', 'Novo'],
        'preparing' => ['bg-amber-100 text-amber-700', 'Em preparo'],
        'ready' => ['bg-purple-100 text-purple-700', 'Pronto'],
        'delivered' => ['bg-green-100 text-green-700', 'Entregue'],
        'cancelled' => ['bg-red-100 text-red-700', 'Cancelado'],
    ];
    [$classes, $label] = $map[$status] ?? ['bg-stone-100 text-stone-600', $status];
@endphp
<span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $classes }}">{{ $label }}</span>
