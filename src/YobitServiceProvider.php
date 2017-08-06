<?php
namespace Pepijnolivier\Yobit;

use Illuminate\Support\ServiceProvider;

class YobitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->publishes([
            __DIR__ . '/../config/yobit.php' => config_path('yobit.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('yobit', function () {
            return new YobitManager;
        });
    }
}
