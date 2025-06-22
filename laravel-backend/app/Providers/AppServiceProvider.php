<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bindeamos la interfaz del repositorio a su implementación concreta
        $this->app->bind(
            \App\Contracts\ProductRepositoryInterface::class,
            \App\Repositories\ProductRepository::class
        );

        // Bindeamos la interfaz del servicio a su implementación concreta
        $this->app->bind(
            \App\Contracts\ProductServiceInterface::class,
            \App\Services\ProductService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}