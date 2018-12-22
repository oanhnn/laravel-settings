<?php

namespace Laravel\Settings;

use Illuminate\Support\ServiceProvider;

/**
 * Class SettingsServiceProvider
 *
 * @package     Laravel\Settings
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class SettingsServiceProvider extends ServiceProvider
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
            => database_path('migrations/' . date('Y_m_d_His') . '_create_settings_table.php'),
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
        $this->mergeConfigFrom(dirname(__DIR__) . '/database/config/settings.php', 'settings');

        $this->app->singleton('settings.manager', function ($app) {
            return new SettingsManager($app);
        });

        $this->app->singleton('settings', function ($app) {
            return $app['settings.manager']->driver();
        });
    }
}
