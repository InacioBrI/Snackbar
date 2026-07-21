<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\Product;
use Illuminate\Contracts\Session\Session;

class Cart
{
    private const SESSION_KEY = 'cart';

    public function __construct(private Session $session) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    public function items(): array
    {
        return $this->session->get(self::SESSION_KEY, []);
    }

    public function isEmpty(): bool
    {
        return count($this->items()) === 0;
    }

    public function count(): int
    {
        return array_sum(array_column($this->items(), 'quantity'));
    }

    public function subtotal(): float
    {
        $total = 0.0;
        foreach ($this->items() as $item) {
            $total += (float) $item['line_total'];
        }

        return round($total, 2);
    }

    /**
     * @param  array<int>  $addonIds
     */
    public function add(Product $product, int $quantity = 1, array $addonIds = [], ?string $notes = null): void
    {
        $quantity = max(1, $quantity);

        $addons = Addon::query()
            ->whereIn('id', $addonIds)
            ->whereIn('id', $product->addons()->pluck('addons.id'))
            ->where('is_active', true)
            ->get()
            ->map(fn (Addon $addon) => [
                'id' => $addon->id,
                'name' => $addon->name,
                'price' => (float) $addon->price,
            ])
            ->values()
            ->all();

        $addonsTotal = array_sum(array_column($addons, 'price'));
        $unitPrice = (float) $product->price + $addonsTotal;

        $rowId = $this->rowId($product->id, array_column($addons, 'id'));
        $items = $this->items();

        if (isset($items[$rowId])) {
            $items[$rowId]['quantity'] += $quantity;
        } else {
            $items[$rowId] = [
                'row_id' => $rowId,
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image,
                'base_price' => (float) $product->price,
                'addons' => $addons,
                'addons_total' => round($addonsTotal, 2),
                'unit_price' => round($unitPrice, 2),
                'quantity' => $quantity,
                'notes' => $notes,
            ];
        }

        $items[$rowId]['line_total'] = round($items[$rowId]['unit_price'] * $items[$rowId]['quantity'], 2);

        $this->save($items);
    }

    public function update(string $rowId, int $quantity): void
    {
        $items = $this->items();

        if (! isset($items[$rowId])) {
            return;
        }

        if ($quantity <= 0) {
            $this->remove($rowId);

            return;
        }

        $items[$rowId]['quantity'] = $quantity;
        $items[$rowId]['line_total'] = round($items[$rowId]['unit_price'] * $quantity, 2);

        $this->save($items);
    }

    public function remove(string $rowId): void
    {
        $items = $this->items();
        unset($items[$rowId]);
        $this->save($items);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    /**
     * @param  array<int>  $addonIds
     */
    private function rowId(int $productId, array $addonIds): string
    {
        sort($addonIds);

        return md5($productId.'|'.implode(',', $addonIds));
    }

    /**
     * @param  array<string, array<string, mixed>>  $items
     */
    private function save(array $items): void
    {
        $this->session->put(self::SESSION_KEY, $items);
    }
}
