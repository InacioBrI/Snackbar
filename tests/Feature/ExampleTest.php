<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_loads(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_the_menu_lists_active_products(): void
    {
        $category = Category::create(['name' => 'Lanches', 'slug' => 'lanches', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'X-Burger',
            'slug' => 'x-burger',
            'price' => 20,
            'is_active' => true,
        ]);

        $this->get(route('menu.index'))
            ->assertStatus(200)
            ->assertSee('X-Burger');

        $this->get(route('menu.show', $product))
            ->assertStatus(200)
            ->assertSee('Adicionar ao carrinho');
    }
}
