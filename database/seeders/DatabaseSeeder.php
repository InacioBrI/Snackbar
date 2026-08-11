<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds only the structure needed to operate (admin, settings, categories and addons).
 * Dishes are managed by the admin through the "Cardápio do dia" panel.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@lanchonete.test'],
            ['name' => 'Administrador', 'password' => 'password', 'is_active' => true],
        );

        $settings = [
            'name' => 'Almoço do Shopping',
            'about' => 'Almoço caseiro na praça de alimentação: pratos do dia preparados na hora, servidos rapidinho para você aproveitar sem enfrentar filas.',
            'phone' => '(11) 4002-8922',
            'address' => 'Praça de Alimentação, Piso L2 - Shopping Central',
            'hours' => 'Segunda a Sábado, das 11h às 15h',
            'service_fee_percent' => '0',
            'payment_methods' => 'pix,credit,debit',
        ];
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $addons = [
            'Arroz extra' => 4.00,
            'Feijão extra' => 4.00,
            'Farofa' => 3.00,
            'Vinagrete' => 3.00,
            'Ovo frito' => 3.50,
            'Salada extra' => 5.00,
        ];
        foreach ($addons as $name => $price) {
            Addon::updateOrCreate(['name' => $name], ['price' => $price, 'is_active' => true]);
        }

        $categories = [
            'Pratos do dia' => ['sort' => 1, 'description' => 'O almoço de hoje, definido diariamente pela cozinha.'],
            'Pratos executivos' => ['sort' => 2, 'description' => 'Opções fixas servidas durante toda a semana.'],
            'Bebidas' => ['sort' => 3, 'description' => 'Sucos, refrigerantes e água para acompanhar o almoço.'],
        ];
        foreach ($categories as $name => $info) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $info['description'],
                    'sort_order' => $info['sort'],
                    'is_active' => true,
                ],
            );
        }
    }
}
