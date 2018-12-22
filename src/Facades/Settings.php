<?php

namespace Laravel\Settings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Settings class
 *
 * @package     Laravel\Settings\Facades
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 *
 * @method array all()
 * @method mixed get(string $key, $default = null)
 * @method bool has(string $key)
 * @method void set(string $key, $value)
 * @method void forget(string $key)
 * @method void forgetAll()
 * @method void save()
 * @method void load(bool $force = false)
 */
class Settings extends Facade
{
    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor()
    {
        return 'settings';
    }
}
