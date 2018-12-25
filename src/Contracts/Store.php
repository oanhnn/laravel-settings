<?php

namespace Laravel\Settings\Contracts;

interface Store
{
    /**
     * Read the data from the store.
     *
     * @return array
     */
    public function read(): array;

    /**
     * Write the data into the store.
     *
     * @param  array $data
     * @return void
     */
    public function write(array $data);
}
