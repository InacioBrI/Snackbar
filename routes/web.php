<?php

use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderTrackController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer (public) routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/cardapio', [MenuController::class, 'index'])->name('menu.index');
Route::get('/produto/{product}', [MenuController::class, 'show'])->name('menu.show');

Route::controller(CartController::class)->prefix('carrinho')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/adicionar', 'store')->name('store');
    Route::patch('/{rowId}', 'update')->name('update');
    Route::delete('/{rowId}', 'destroy')->name('destroy');
    Route::delete('/', 'clear')->name('clear');
});

Route::controller(CheckoutController::class)->group(function () {
    Route::get('/checkout', 'create')->name('checkout.create');
    Route::post('/checkout', 'store')->name('checkout.store');
});

Route::controller(PaymentController::class)->prefix('pedido/{order}')->name('payment.')->group(function () {
    Route::get('/pagamento', 'show')->name('show');
    Route::post('/pagamento', 'process')->name('process');
    Route::post('/confirmar-pix', 'confirmPix')->name('confirm-pix');
    Route::get('/status', 'status')->name('status');
});

Route::get('/pedido/{order}/confirmacao', [PaymentController::class, 'confirmation'])->name('order.confirmation');

Route::controller(OrderTrackController::class)->group(function () {
    Route::get('/acompanhar', 'index')->name('track.index');
    Route::post('/acompanhar', 'search')->name('track.search');
    Route::get('/acompanhar/{order}', 'show')->name('track.show');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::middleware('admin.auth')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categorias', CategoryController::class)->except('show')
            ->parameters(['categorias' => 'category']);
        Route::resource('produtos', ProductController::class)->except('show')
            ->parameters(['produtos' => 'product']);
        Route::resource('adicionais', AddonController::class)->except('show')
            ->parameters(['adicionais' => 'addon']);

        Route::get('pedidos', [AdminOrderController::class, 'index'])->name('pedidos.index');
        Route::get('pedidos/{order}', [AdminOrderController::class, 'show'])->name('pedidos.show');
        Route::patch('pedidos/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('pedidos.status');

        Route::get('relatorios', [ReportController::class, 'index'])->name('relatorios.index');

        Route::resource('administradores', AdminController::class)->except('show')
            ->parameters(['administradores' => 'admin']);

        Route::get('configuracoes', [SettingController::class, 'edit'])->name('configuracoes.edit');
        Route::put('configuracoes', [SettingController::class, 'update'])->name('configuracoes.update');
    });
});
