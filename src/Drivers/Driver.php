<?php

namespace Laravel\Settings\Drivers;

use Illuminate\Support\Arr;

/**
 * Class Driver
 *
 * @package     Laravel\Settings\Drivers
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
abstract class Driver
{
    /**
     * The settings data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Whether the store has changed since it was last loaded.
     *
     * @var boolean
     */
    protected $unsaved = false;

    /**
     * Whether the settings data are loaded.
     *
     * @var boolean
     */
    protected $loaded = false;

    /**
     * Get a specific key from the settings data.
     *
     * @param  string|array $key
     * @param  mixed $default Optional default value.
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $this->load();

        if (is_array($key)) {
            $result = [];
            foreach ($key as $k) {
                Arr::set($result, $k, Arr::get($this->data, $k));
            }

            return $result;
        }

        return Arr::get($this->data, $key, $default);
    }

    /**
     * Determine if a key exists in the settings data.
     *
     * @param  string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $this->load();

        return Arr::has($this->data, $key);
    }

    /**
     * Set a specific key to a value in the settings data.
     *
     * @param string|array $key   Key string or associative array of key => value
     * @param mixed        $value Optional only if the first argument is an array
     */
    public function set($key, $value = null)
    {
        $this->load();
        $this->unsaved = true;

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                Arr::set($this->data, $k, $v);
            }
        } else {
            Arr::set($this->data, $key, $value);
        }
    }

    /**
     * Unset a key in the settings data.
     *
     * @param  string $key
     */
    public function forget(string $key)
    {
        $this->unsaved = true;
        if ($this->has($key)) {
            Arr::forget($this->data, $key);
        }
    }

    /**
     * Unset all keys in the settings data.
     *
     * @return void
     */
    public function forgetAll()
    {
        $this->unsaved = true;
        $this->data = [];
    }

    /**
     * Get all settings data.
     *
     * @return array
     */
    public function all(): array
    {
        $this->load();
        return $this->data;
    }

    /**
     * Save any changes done to the settings data.
     *
     * @return void
     */
    public function save()
    {
        if (!$this->unsaved || !$this->loaded) {
            // either nothing has been changed, or data has not been loaded, so
            // do nothing by returning early
            return;
        }
        $this->write($this->data);
        $this->unsaved = false;
    }

    /**
     * Make sure data is loaded.
     *
     * @param bool $force Force a reload of data. Default false.
     */
    public function load($force = false)
    {
        if (!$this->loaded || $force) {
            $this->data = $this->read();
            $this->loaded = true;
            $this->unsaved = false;
        }
    }

    /**
     * Read the data from the store.
     *
     * @return array
     */
    abstract protected function read(): array;

    /**
     * Write the data into the store.
     *
     * @param  array $data
     * @return void
     */
    abstract protected function write(array $data);
}
