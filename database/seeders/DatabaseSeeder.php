<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@lanchonete.test'],
            ['name' => 'Administrador', 'password' => 'password', 'is_active' => true],
        );

        $settings = [
            'name' => 'Lanchonete do Shopping',
            'about' => 'A melhor lanchonete do shopping! Lanches artesanais, bebidas geladas e sobremesas irresistíveis, prontos rapidinho para você aproveitar sem enfrentar filas.',
            'phone' => '(11) 4002-8922',
            'address' => 'Praça de Alimentação, Piso L2 - Shopping Central',
            'hours' => 'Segunda a Domingo, das 10h às 22h',
            'service_fee_percent' => '0',
            'payment_methods' => 'pix,credit,debit',
        ];
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $addons = collect([
            'Bacon extra' => 4.00,
            'Queijo extra' => 3.50,
            'Ovo' => 2.50,
            'Cheddar cremoso' => 4.50,
            'Molho especial' => 2.00,
            'Cebola caramelizada' => 3.00,
        ])->map(fn ($price, $name) => Addon::updateOrCreate(['name' => $name], ['price' => $price, 'is_active' => true]));

        $data = [
            'Lanches' => [
                'sort' => 1,
                'description' => 'Hambúrgueres artesanais e sanduíches feitos na hora.',
                'products' => [
                    ['X-Salada', 'Hambúrguer 150g, queijo, alface, tomate e maionese da casa.', 24.90, true],
                    ['X-Bacon', 'Hambúrguer 150g, bacon crocante, queijo e molho especial.', 28.90, true],
                    ['X-Tudo', 'Dois hambúrgueres, bacon, ovo, queijo, presunto e salada.', 34.90, false],
                    ['Frango Crispy', 'Filé de frango empanado, cheddar e alface americana.', 26.90, false],
                ],
            ],
            'Bebidas' => [
                'sort' => 2,
                'description' => 'Refrigerantes, sucos naturais e água gelada.',
                'products' => [
                    ['Refrigerante Lata', 'Coca-Cola, Guaraná ou Fanta - 350ml.', 6.00, false],
                    ['Suco Natural', 'Laranja, limão ou maracujá - 500ml.', 9.50, false],
                    ['Água Mineral', 'Com ou sem gás - 500ml.', 4.00, false],
                ],
            ],
            'Combos' => [
                'sort' => 3,
                'description' => 'Lanche + acompanhamento + bebida com preço especial.',
                'products' => [
                    ['Combo X-Salada', 'X-Salada + batata frita + refrigerante lata.', 34.90, true],
                    ['Combo X-Bacon', 'X-Bacon + batata frita + refrigerante lata.', 38.90, true],
                ],
            ],
            'Sobremesas' => [
                'sort' => 4,
                'description' => 'Para adoçar o fim da refeição.',
                'products' => [
                    ['Milk Shake', 'Chocolate, morango ou baunilha - 400ml.', 16.90, false],
                    ['Brownie com Sorvete', 'Brownie quentinho com bola de sorvete de creme.', 14.90, false],
                ],
            ],
            'Porções' => [
                'sort' => 5,
                'description' => 'Para compartilhar (ou não).',
                'products' => [
                    ['Batata Frita', 'Porção de batata frita crocante - serve 2.', 18.90, false],
                    ['Onion Rings', 'Anéis de cebola empanados - serve 2.', 21.90, false],
                ],
            ],
        ];

        foreach ($data as $categoryName => $info) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => $info['description'],
                    'sort_order' => $info['sort'],
                    'is_active' => true,
                ],
            );

            foreach ($info['products'] as $index => [$name, $description, $price, $featured]) {
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

                if (in_array($categoryName, ['Lanches', 'Combos'], true)) {
                    $product->addons()->sync($addons->pluck('id'));
                }
            }
        }
    }
}
