<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\CarritoController;
use App\Http\Controllers\Public\CatalogoController;
use App\Http\Controllers\Public\CheckoutController;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Public Routes
Route::get('/', function () {
    $productosDestacados = Producto::with('imagenes')
        ->where('activo', 1)
        ->latest()
        ->take(8)
        ->get();

    return view('landing', compact('productosDestacados'));
})->name('home');
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/producto/{id}', [CatalogoController::class, 'show'])->name('producto.show');

// Ruta publica para mostrar/filtrar por categoria
Route::get('/categoria/{id}', [CatalogoController::class, 'categoria'])->name('categorias.show');

Route::get('/media/{path}', function (string $path) {
    if (str_contains($path, '..')) {
        abort(404);
    }
    $disk = Storage::disk('public');
    if (!$disk->exists($path)) {
        abort(404);
    }
    $mime = $disk->mimeType($path) ?: 'application/octet-stream';
    return response($disk->get($path), 200, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
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

// Auth Routes
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin|editor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
        ->name('users.reset-password');
    Route::get('users/data', [\App\Http\Controllers\Admin\UserController::class, 'data'])->name('users.data');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::get('productos/data', [\App\Http\Controllers\Admin\ProductoController::class, 'data'])->name('productos.data');
    Route::resource('productos', \App\Http\Controllers\Admin\ProductoController::class)->except(['show']);
    Route::delete('productos/{producto}/imagenes/{imagen}', [\App\Http\Controllers\Admin\ProductoController::class, 'destroyImagen'])
        ->name('productos.imagenes.destroy');
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
    Route::get('categorias/data', [\App\Http\Controllers\Admin\CategoriaController::class, 'data'])->name('categorias.data');
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
