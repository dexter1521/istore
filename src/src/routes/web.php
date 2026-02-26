<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\CarritoController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\CatalogoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Services\WhatsAppService;
use App\Models\Pedido;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

// Public Routes (Sprint 1 & 2)
Route::get('/', [CatalogoController::class, 'index'])->name('home');
Route::get('/producto/{id}', [CatalogoController::class, 'show'])->name('producto.show');

// Ruta pÃƒÆ’Ã‚Âºblica para mostrar/filtrar por categorÃƒÆ’Ã‚Â­a
Route::get('/categoria/{id}', [CatalogoController::class, 'categoria'])->name('categorias.show');

Route::get('/media/{path}', function (string $path) {
    if (str_contains($path, '..')) {
        abort(404);
    }
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('media.show');

Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::post('/carrito/actualizar', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
Route::post('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->middleware('throttle:checkout')
    ->name('checkout.store');

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
    Route::resource('productos', \App\Http\Controllers\Admin\ProductoController::class)->except(['show']);
    Route::post('/productos/import', [\App\Http\Controllers\Admin\ProductoController::class, 'import'])->name('productos.import');
    Route::get('/productos/template', function () {
        $path = resource_path('templates/import-template.csv');
        if (!file_exists($path)) {
            $path = base_path('resources/templates/import-template.csv');
        }

        if (file_exists($path)) {
            return response()->download($path, 'import-template.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // Fallback para entornos donde el archivo no esta montado en contenedor.
        $csv = implode("\n", [
            'sku,nombre,descripcion,precio,categoria,activo',
            'SKU123,Producto Ejemplo,Descripcion corta,12.50,Categoria Ejemplo,1',
        ]) . "\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="import-template.csv"',
        ]);
    })->name('productos.template');
    Route::resource('categorias', \App\Http\Controllers\Admin\CategoriaController::class);
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    // Pedidos (Kanban)
    Route::get('/pedidos', [\App\Http\Controllers\Admin\PedidoController::class, 'kanban'])->name('pedidos.kanban');
    Route::get('/pedidos/{pedido}', [\App\Http\Controllers\Admin\PedidoController::class, 'show'])->name('pedidos.show');
    Route::post('/pedidos/{pedido}/estado', [\App\Http\Controllers\Admin\PedidoController::class, 'updateEstado'])->name('pedidos.update-estado');
});

require __DIR__ . '/auth.php';

RateLimiter::for('checkout', function (Request $request) {
    $key = $request->ip();
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by($key);
});
