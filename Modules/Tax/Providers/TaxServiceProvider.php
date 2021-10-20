<?php

namespace Modules\Tax\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Tax\Entities\CustomerTaxGroup;
use Modules\Tax\Entities\ProductTaxGroup;
use Modules\Tax\Entities\TaxRate;
use Modules\Tax\Entities\TaxRule;
use Modules\Tax\Observers\CustomerTaxGroupObserver;
use Modules\Tax\Observers\ProductTaxGroupObserver;
use Modules\Tax\Observers\TaxRateObserver;
use Modules\Tax\Observers\TaxRuleObserver;
use Modules\Tax\Services\GeoIp;
use Modules\Tax\Services\TaxCache;
use Modules\Tax\Services\TaxPrice;

class TaxServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Tax';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'tax';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerActivityLogger();
        $this->registerObserver();
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
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/geoip.php'), "geoip"
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    public function registerActivityLogger()
    {
        $this->app->singleton('TaxPrice', function () {
            return new TaxPrice();
        });
        $this->app->singleton('GeoIp', function () {
            return new GeoIp();
        });
        $this->app->singleton('TaxCache', function () {
            return new TaxCache();
        });
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

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }

    public function registerObserver(): void
    {
        ProductTaxGroup::observe(ProductTaxGroupObserver::class);
        CustomerTaxGroup::observe(CustomerTaxGroupObserver::class);
        TaxRule::observe(TaxRuleObserver::class);
        TaxRate::observe(TaxRateObserver::class);
    }
}
