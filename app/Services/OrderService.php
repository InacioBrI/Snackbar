<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private Cart $cart) {}

    /**
     * Build a persisted order from the current cart contents.
     *
     * @param  array{name:string, phone:string, location:string, notes:?string, payment_method:string}  $data
     */
    public function createFromCart(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
            ]);

            $subtotal = 0.0;
            $lines = [];

            foreach ($this->cart->items() as $item) {
                // Re-validate price against the database to avoid tampering.
                $product = Product::find($item['product_id']);
                $basePrice = $product ? (float) $product->price : (float) $item['base_price'];
                $addonsTotal = (float) $item['addons_total'];
                $unitPrice = round($basePrice + $addonsTotal, 2);
                $quantity = (int) $item['quantity'];
                $lineTotal = round($unitPrice * $quantity, 2);
                $subtotal += $lineTotal;

                $lines[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'addons_total' => $addonsTotal,
                    'line_total' => $lineTotal,
                    'addons' => $item['addons'],
                ];

                if ($product && $product->track_stock) {
                    $product->decrement('stock', $quantity);
                }
            }

            $subtotal = round($subtotal, 2);
            $feePercent = (float) Setting::get('service_fee_percent', 0);
            $serviceFee = round($subtotal * $feePercent / 100, 2);
            $total = round($subtotal + $serviceFee, 2);

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'status' => 'new',
                'location' => $data['location'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
            ]);

            $order->items()->createMany($lines);

            return $order;
        });
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'P'.now()->format('ymd').str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
