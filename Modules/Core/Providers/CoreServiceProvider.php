<?php

namespace Modules\Core\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Core\Entities\ActivityLog;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\Locale;
use Modules\Core\Observers\ChannelObserver;
use Modules\Core\Observers\CurrencyObserver;
use Modules\Core\Observers\LocaleObserver;
use Modules\Core\Services\ActivityLogHelper;
use Modules\Core\Facades\Audit as ActivityLoggerFacade;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'core');
        $this->loadMigrationsFrom(module_path('Core', 'Database/Migrations'));
        $this->registerActivityLogger();
        $this->registerObserver();

        include __DIR__ . '/../Helpers/helpers.php';
        Validator::extend('decimal', 'Modules\Core\Contracts\Validations\Decimal@passes');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Core', 'Config/config.php') => config_path('core.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Core', 'Config/config.php'), 'core'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/core');

        $sourcePath = module_path('Core', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/core';
        }, \Config::get('view.paths')), [$sourcePath]), 'core');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/core');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'core');
        } else {
            $this->loadTranslationsFrom(module_path('Core', 'Resources/lang'), 'core');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('Core', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }


    protected function registerActivityLogger()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Audit', ActivityLoggerFacade::class);

        $this->app->singleton('audit', function () {
            return new ActivityLogHelper(new ActivityLog());
        });
    }

    private function registerObserver()
    {
        Channel::observe(ChannelObserver::class);
        Locale::observe(LocaleObserver::class);
        Currency::observe(CurrencyObserver::class);
    }
}
