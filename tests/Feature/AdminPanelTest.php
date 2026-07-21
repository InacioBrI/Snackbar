<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
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

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_and_view_dashboard(): void
    {
        $this->admin();

        $this->post(route('admin.login.store'), [
            'email' => 'admin@test.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertStatus(200)->assertSee('Ticket médio');
    }

    public function test_inactive_admin_cannot_login(): void
    {
        Admin::create([
            'name' => 'Inactive',
            'email' => 'off@test.com',
            'password' => 'password',
            'is_active' => false,
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'off@test.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_admin_can_create_a_category(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $this->post(route('admin.categorias.store'), [
            'name' => 'Bebidas',
            'is_active' => '1',
        ])->assertRedirect(route('admin.categorias.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Bebidas', 'slug' => 'bebidas']);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'admin');

        $this->delete(route('admin.administradores.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('admins', ['id' => $admin->id]);
    }

    public function test_cannot_delete_category_with_products(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $category = Category::create(['name' => 'Lanches', 'slug' => 'lanches', 'is_active' => true]);
        $category->products()->create([
            'name' => 'X', 'slug' => 'x', 'price' => 10, 'is_active' => true,
        ]);

        $this->delete(route('admin.categorias.destroy', $category))->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
