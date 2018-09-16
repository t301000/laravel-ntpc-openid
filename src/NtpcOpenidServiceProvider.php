<?php 

namespace T301000\LaravelNtpcOpenid;

use Illuminate\Support\ServiceProvider;

/**
* 
*/
class NtpcOpenidServiceProvider extends ServiceProvider
{
	
	/**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    	$configPath = __DIR__ . '/../config/ntpcopenid.php';
        $this->publishes([$configPath => config_path('ntpcopenid.php')], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/ntpcopenid.php';
        $this->mergeConfigFrom($configPath, 'ntpcopenid');
     
        $this->app->singleton('ntpcopenid', 'T301000\LaravelNtpcOpenid\NtpcOpenid');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ntpcopenid'];
    }

}