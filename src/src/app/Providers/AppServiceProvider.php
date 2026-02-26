<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Categoria;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartir las categorías activas con todas las vistas
        View::composer('*', function ($view) {
            $categorias = Categoria::where('activo', 1)->orderBy('nombre')->get();
            $view->with('categoriasMenu', $categorias);
        });
    }
}
