<?php
/**
 * Created by PhpStorm.
 * User: 750371433
 * Date: 04/08/2017
 * Time: 17:15
 */

namespace App\Providers;


use App\Helpers\GSMArenaFetcher;
use Illuminate\Support\ServiceProvider;

class DeviceServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // TODO: Implement boot() method.
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Helpers\Contracts\DeviceFetcherContract', function () {
            return new GSMArenaFetcher();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['App\Helpers\Contracts\DeviceFetcherContract'];
    }
}