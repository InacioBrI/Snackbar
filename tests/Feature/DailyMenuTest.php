<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\DailyMenu;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyMenuTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'is_active' => true,
        ]);
    }

    private function dish(string $name): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'pratos-do-dia'],
            ['name' => 'Pratos do dia', 'is_active' => true],
        );

        return Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'slug' => str($name)->slug()->value(),
            'price' => 29.90,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_daily_menu_with_dishes(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $feijoada = $this->dish('Feijoada');
        $frango = $this->dish('Frango grelhado');

        $this->post(route('admin.cardapio-do-dia.store'), [
            'menu_date' => today()->toDateString(),
            'title' => 'Almoço de hoje',
            'is_published' => 1,
            'products' => [$feijoada->id, $frango->id],
            'sold_out' => [$frango->id],
        ])->assertRedirect(route('admin.cardapio-do-dia.index'));

        $menu = DailyMenu::first();
        $this->assertNotNull($menu);
        $this->assertTrue($menu->is_published);
        $this->assertEquals(2, $menu->products()->count());
        $this->assertTrue((bool) $menu->products()->find($frango->id)->pivot->is_sold_out);
    }

    public function test_daily_menu_requires_at_least_one_dish(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $this->post(route('admin.cardapio-do-dia.store'), [
            'menu_date' => today()->toDateString(),
        ])->assertSessionHasErrors('products');
    }

    public function test_published_menu_limits_what_customers_can_order(): void
    {
        $today = $this->dish('Feijoada');
        $offMenu = $this->dish('Frango grelhado');

        $menu = DailyMenu::create(['menu_date' => today(), 'is_published' => true]);
        $menu->products()->sync([$today->id => ['is_sold_out' => false, 'sort_order' => 0]]);

        $this->get(route('menu.index'))
            ->assertStatus(200)
            ->assertSee('Feijoada')
            ->assertDontSee('Frango grelhado');

        $this->post(route('cart.store'), ['product_id' => $offMenu->id, 'quantity' => 1])
            ->assertSessionHas('error');

        $this->post(route('cart.store'), ['product_id' => $today->id, 'quantity' => 1])
            ->assertRedirect(route('cart.index'));
    }

    public function test_sold_out_dish_cannot_be_ordered(): void
    {
        $dish = $this->dish('Feijoada');

        $menu = DailyMenu::create(['menu_date' => today(), 'is_published' => true]);
        $menu->products()->sync([$dish->id => ['is_sold_out' => true, 'sort_order' => 0]]);

        $this->post(route('cart.store'), ['product_id' => $dish->id, 'quantity' => 1])
            ->assertSessionHas('error');
    }

    public function test_unpublished_menu_does_not_restrict_the_catalog(): void
    {
        $dish = $this->dish('Feijoada');
        $other = $this->dish('Frango grelhado');

        $menu = DailyMenu::create(['menu_date' => today(), 'is_published' => false]);
        $menu->products()->sync([$dish->id => ['is_sold_out' => false, 'sort_order' => 0]]);

        $this->get(route('menu.index'))
            ->assertStatus(200)
            ->assertSee('Feijoada')
            ->assertSee('Frango grelhado');

        $this->post(route('cart.store'), ['product_id' => $other->id, 'quantity' => 1])
            ->assertRedirect(route('cart.index'));
    }
}
