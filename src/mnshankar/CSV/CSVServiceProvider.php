<?php

namespace mnshankar\CSV;

use Illuminate\Support\ServiceProvider;

/**
 * Class CSVServiceProvider
 *
 * @package mnshankar\CSV
 */
class CSVServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var boolean
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['csv'] = $this->app->share(function () {
            return new CSV();
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

}
