<?php

namespace Laravel\Settings;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package     Laravel\Settings
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $dir = dirname(__DIR__);

        $this->publishes([
            $dir . '/config/settings.php' => config_path('settings.php'),
        ], 'config');

        $this->publishes([
            $dir . '/database/migrations/create_settings_table.php'
            => database_path('migrations/' . Carbon::now()->format('Y_m_d_His') . '_create_settings_table.php'),
        ], 'migration');

        // Override config
        if (config('settings.override')) {
            foreach (config('settings.override') as $configKey => $settingKey) {
                $value = $this->app['settings']->get($settingKey);
                if (is_null($value)) {
                    continue;
                }

                config([$configKey => $value]);
            }

            unset($value);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/settings.php', 'settings');

        $this->app->singleton('settings.manager', function ($app) {
            return new Manager($app);
        });

        $this->app->singleton('settings', function ($app) {
            return $app['settings.manager']->driver();
        });
    }
}
