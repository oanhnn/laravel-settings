<?php

namespace Laravel\Settings\Drivers;

/**
 * Class Memory
 *
 * @package     Laravel\Settings\Drivers
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class Memory extends Driver
{
    /**
     * @var bool
     */
    protected $readonly;

    /**
     * @param array $data
     * @param bool  $readonly
     */
    public function __construct(array $data = null, bool $readonly = false)
    {
        if ($data) {
            $this->data = $data;
        }

        $this->readonly = $readonly;
    }

    /**
     * {@inheritdoc}
     */
    protected function read(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $data)
    {
        if (!$this->readonly) {
            $this->data = $data;
        }
    }
}
