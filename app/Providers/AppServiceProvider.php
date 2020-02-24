<?php

namespace App\Providers;

use App\Core\BaseModule\BaseRepository;
use App\Core\BaseModule\BaseRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        $this->app->singleton(BaseRepositoryInterface::class, BaseRepository::class);
    }
}
