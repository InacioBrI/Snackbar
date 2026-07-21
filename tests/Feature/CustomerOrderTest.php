<?php

namespace Tests\Feature;

use App\Models\Addon;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(float $price = 20, bool $trackStock = false, int $stock = 0): Product
    {
        $category = Category::create(['name' => 'Lanches', 'slug' => 'lanches', 'is_active' => true]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'X-Burger',
            'slug' => 'x-burger',
            'price' => $price,
            'is_active' => true,
            'track_stock' => $trackStock,
            'stock' => $stock,
        ]);
    }

    public function test_customer_can_add_product_with_addon_to_cart(): void
    {
        $product = $this->makeProduct(20);
        $addon = Addon::create(['name' => 'Bacon', 'price' => 5, 'is_active' => true]);
        $product->addons()->attach($addon->id);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'addons' => [$addon->id],
        ])->assertRedirect(route('cart.index'));

        $this->get(route('cart.index'))
            ->assertStatus(200)
            ->assertSee('X-Burger')
            ->assertSee('Bacon');
    }

    public function test_customer_can_checkout_without_login_and_pay_with_pix(): void
    {
        Setting::set('service_fee_percent', '10');
        Setting::set('payment_methods', 'pix,credit,debit');
        $product = $this->makeProduct(20);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2]);

        $response = $this->post(route('checkout.store'), [
            'name' => 'Maria',
            'phone' => '11999990000',
            'location' => 'Mesa 3',
            'payment_method' => 'pix',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals(40.0, (float) $order->subtotal);
        $this->assertEquals(4.0, (float) $order->service_fee);
        $this->assertEquals(44.0, (float) $order->total);
        $this->assertEquals('pending', $order->payment_status);
        $response->assertRedirect(route('payment.show', $order));

        $this->get(route('payment.show', $order))->assertStatus(200)->assertSee('Pague com PIX');

        $this->post(route('payment.confirm-pix', $order))
            ->assertRedirect(route('order.confirmation', $order));

        $this->assertEquals('paid', $order->fresh()->payment_status);
    }

    public function test_declined_card_keeps_order_pending(): void
    {
        $product = $this->makeProduct(30);
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->post(route('checkout.store'), [
            'name' => 'Ana',
            'phone' => '11888887777',
            'location' => 'Balcão',
            'payment_method' => 'credit',
        ]);
        $order = Order::first();

        // A card number ending in 0 is declined by the mock gateway.
        $this->post(route('payment.process', $order), [
            'card_number' => '4111111111111110',
            'card_holder' => 'ANA',
            'card_expiry' => '12/30',
            'card_cvv' => '123',
        ])->assertRedirect();

        $this->assertEquals('failed', $order->fresh()->payment_status);
    }

    public function test_customer_can_track_order_by_phone(): void
    {
        $product = $this->makeProduct(15);
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->post(route('checkout.store'), [
            'name' => 'Carlos',
            'phone' => '11777776666',
            'location' => 'Mesa 9',
            'payment_method' => 'pix',
        ]);
        $order = Order::first();

        $this->post(route('track.search'), ['identifier' => '11777776666'])
            ->assertRedirect(route('track.show', $order));

        $this->post(route('track.search'), ['identifier' => $order->order_number])
            ->assertRedirect(route('track.show', $order));
    }
}
