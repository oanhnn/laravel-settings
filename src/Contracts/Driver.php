<?php

namespace Laravel\Settings\Contracts;

use Illuminate\Support\Arr;

/**
 * Class Driver
 *
 * @package     Laravel\Settings\Contracts
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
    public function get(string $key, $default = null)
    {
        $this->load();

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
     * @param string $key Key string or associative array of key => value
     * @param mixed $value
     */
    public function set(string $key, $value)
    {
        $this->load();
        $this->unsaved = true;

        Arr::set($this->data, $key, $value);
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
        if (!$this->unsaved) {
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
