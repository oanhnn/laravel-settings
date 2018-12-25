<?php

namespace Laravel\Settings;

use Illuminate\Support\Manager as BaseManage;
use Laravel\Settings\Drivers\Cache;
use Laravel\Settings\Drivers\Database;
use Laravel\Settings\Drivers\Json;
use Laravel\Settings\Drivers\Memory;

/**
 * Class Manager
 *
 * @package     Laravel\Settings
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class Manager extends BaseManage
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
    protected function createJsonDriver()
    {
        $path = config('settings.json.path');

        return new Json($this->app['files'], $path);
    }

    /**
     * @return Database
     */
    protected function createDatabaseDriver()
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
    protected function createMemoryDriver()
    {
        return new Memory();
    }

    /**
     * @return Memory
     */
    protected function createArrayDriver()
    {
        return $this->createMemoryDriver();
    }

    /**
     * @return Cache
     */
    protected function createCacheDriver()
    {
        // TODO
    }
}
