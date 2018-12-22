<?php

namespace Laravel\Settings;

use Illuminate\Support\Manager;
use Laravel\Settings\Drivers\Database;
use Laravel\Settings\Drivers\Json;
use Laravel\Settings\Drivers\Memory;

/**
 * Class SettingsManager
 *
 * @package     Laravel\Settings
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class SettingsManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return config('settings.driver');
    }

    /**
     * @return Json
     */
    public function createJsonDriver()
    {
        $path = config('settings.json.path');

        return new Json($this->app['files'], $path);
    }

    /**
     * @return Database
     */
    public function createDatabaseDriver()
    {
        $connection = $this->app['db']->connection(config('settings.database.connection'));
        $table = config('settings.database.table');
        $key = config('settings.database.key');
        $value = config('settings.database.value');

        return new Database($connection, $table, $key, $value);
    }

    /**
     * @return Memory
     */
    public function createMemoryDriver()
    {
        return new Memory();
    }

    /**
     * @return Memory
     */
    public function createArrayDriver()
    {
        return $this->createMemoryDriver();
    }

    public function createCacheDriver()
    {
    }
}
