<?php

namespace App\Providers;

use App\Models\DesignationMaterial;
use App\Models\Specification;
use App\Observers\DesignationMaterialObserver;
use App\Observers\SpecificationObserver;
use Illuminate\Support\ServiceProvider;

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
        $this->app['view']->addNamespace('administrator', base_path() . '/resources/views/admin');
        $this->app['view']->addNamespace('public', base_path() . '/resources/views/public');
        Specification::observe(SpecificationObserver::class);
        DesignationMaterial::observe(DesignationMaterialObserver::class);

    }
}
