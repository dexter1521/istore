<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\CarritoController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\CatalogoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Services\WhatsAppService;
use App\Models\Pedido;
use Illuminate\Support\Facades\Route;

// Public Routes (Sprint 1 & 2)
Route::get('/', [CatalogoController::class, 'index'])->name('home');
Route::get('/producto/{id}', [CatalogoController::class, 'show'])->name('producto.show');

// Ruta pública para mostrar/filtrar por categoría
Route::get('/categoria/{id}', [CatalogoController::class, 'categoria'])->name('categorias.show');

Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::post('/carrito/actualizar', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
Route::get('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');

Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/checkout/whatsapp/{id}', function ($id, WhatsAppService $wa) {
    $pedido = Pedido::with('items')->findOrFail($id);
    $url = $wa->generarUrl($pedido);
    $pedido->update(['enviado_whatsapp' => true]);
    return redirect()->away($url);
})->name('checkout.whatsapp');

// Auth Routes (Breeze)
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes (Sprint 3)
Route::middleware(['auth', 'role:admin|editor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('productos', \App\Http\Controllers\Admin\ProductoController::class);
    Route::post('/productos/import', [\App\Http\Controllers\Admin\ProductoController::class, 'import'])->name('productos.import');
    Route::resource('categorias', \App\Http\Controllers\Admin\CategoriaController::class);
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    // Pedidos (Kanban)
    Route::get('/pedidos/kanban', [\App\Http\Controllers\Admin\PedidoController::class, 'kanban'])->name('pedidos.kanban');
    Route::patch('/pedidos/{pedido}/estado', [\App\Http\Controllers\Admin\PedidoController::class, 'updateEstado'])->name('pedidos.updateEstado');
});

require __DIR__ . '/auth.php';
