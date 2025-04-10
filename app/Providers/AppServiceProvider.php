<?php

namespace App\Providers;

use App\Models\DesignationMaterial;
use App\Models\Specification;
use App\Observers\DesignationMaterialObserver;
use App\Observers\SpecificationObserver;
use App\Repositories\DepartmentRepository;
use App\Repositories\DetailSpecificationRepository;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\DetailSpecificationRepositoryInterface;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Repositories\Interfaces\PlanTaskRepositoryInterface;
use App\Repositories\Interfaces\ReportApplicationStatementRepositoryInterface;
use App\Repositories\OrderNameRepository;
use App\Repositories\PlanTaskRepository;
use App\Repositories\ReportApplicationStatementRepository;
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
        $this->app->bind(ReportApplicationStatementRepositoryInterface::class, ReportApplicationStatementRepository::class);
        $this->app->bind(PlanTaskRepositoryInterface::class, PlanTaskRepository::class);
        $this->app->bind(OrderNameRepositoryInterface::class, OrderNameRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(DetailSpecificationRepositoryInterface::class, DetailSpecificationRepository::class);

    }
}
