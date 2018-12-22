<?php

namespace Laravel\Settings\Drivers;

use Illuminate\Contracts\Cache\Repository;
use Laravel\Settings\Contracts\Driver;

/**
 * Class Cache
 *
 * @package     Laravel\Settings\Drivers
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class Cache extends Driver
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $store;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $failback;

    /**
     * Cache constructor.
     * @param \Illuminate\Contracts\Cache\Repository    $store
     * @param string                                    $key
     * @param \Laravel\Settings\Contracts\Driver|null   $failback
     */
    public function __construct(Repository $store, string $key, Driver $failback = null)
    {
        $this->store = $store;
        $this->key = $key;
        $this->failback = $failback;
    }

    protected function read(): array
    {
        if (!$this->store->has($this->key)) {
            $data = $this->failback->all();

            $this->store->set($this->key, $data);

            return $data;
        }

        return $this->get($this->key);
    }

    protected function write(array $data)
    {
        $this->store->set($this->key, $data);
        $this->failback->write($data);
    }
}
