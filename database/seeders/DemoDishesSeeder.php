<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Category;
use App\Models\DailyMenu;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Optional demo data: sample dishes plus a published menu for today.
 * Run with: php artisan db:seed --class=DemoDishesSeeder
 */
class DemoDishesSeeder extends Seeder
{
    public function run(): void
    {
        $dishes = Category::firstOrCreate(
            ['slug' => 'pratos-do-dia'],
            ['name' => 'Pratos do dia', 'sort_order' => 1, 'is_active' => true],
        );

        $drinks = Category::firstOrCreate(
            ['slug' => 'bebidas'],
            ['name' => 'Bebidas', 'sort_order' => 3, 'is_active' => true],
        );

        $addonIds = Addon::where('is_active', true)->pluck('id');

        $data = [
            [$dishes, 'Feijoada completa', 'Feijoada com arroz, couve, farofa e laranja.', 32.90, true],
            [$dishes, 'Filé de frango grelhado', 'Filé grelhado com arroz, feijão, purê e salada.', 28.90, false],
            [$dishes, 'Bife acebolado', 'Bife acebolado com arroz, feijão e batata frita.', 34.90, false],
            [$dishes, 'Strogonoff de frango', 'Strogonoff com arroz branco e batata palha.', 30.90, false],
            [$drinks, 'Suco natural', 'Laranja, limão ou maracujá - 500ml.', 9.50, false],
            [$drinks, 'Refrigerante lata', 'Coca-Cola, Guaraná ou Fanta - 350ml.', 6.00, false],
        ];

        $products = collect($data)->map(function (array $row, int $index) use ($addonIds) {
            [$category, $name, $description, $price, $featured] = $row;

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $category->id,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'is_active' => true,
                    'is_featured' => $featured,
                    'sort_order' => $index,
                ],
            );

            if ($category->slug === 'pratos-do-dia') {
                $product->addons()->sync($addonIds);
            }

            return $product;
        });

        $menu = DailyMenu::updateOrCreate(
            ['menu_date' => today()],
            ['title' => 'Almoço de hoje', 'is_published' => true],
        );

        $menu->products()->sync(
            $products->values()
                ->mapWithKeys(fn (Product $product, int $index) => [
                    $product->id => ['is_sold_out' => false, 'sort_order' => $index],
                ])
                ->all()
        );
    }
}
