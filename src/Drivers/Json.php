<?php

namespace Laravel\Settings\Drivers;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use Laravel\Settings\Contracts\Driver;
use RuntimeException;

/**
 * Class Json
 *
 * @package     Laravel\Settings\Drivers
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class Json extends Driver
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param string                            $path
     */
    public function __construct(Filesystem $files, string $path = null)
    {
        $this->files = $files;

        $this->setPath($path ?? storage_path('app/settings.json'));
    }

    /**
     * Set the path for the JSON file.
     *
     * @param string $path
     * @throws \InvalidArgumentException
     */
    public function setPath(string $path)
    {
        // If the file does not already exist, we will attempt to create it.
        if (!$this->files->exists($path)) {
            $result = $this->files->put($path, '{}');
            if ($result === false) {
                throw new InvalidArgumentException("Could not write to $path.");
            }
        }

        if (!$this->files->isWritable($path)) {
            throw new InvalidArgumentException("$path is not writable.");
        }

        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    protected function read(): array
    {
        $contents = $this->files->get($this->path);

        $data = json_decode($contents, true);

        if ($data === null || !is_array($data)) {
            throw new RuntimeException("Invalid JSON in {$this->path}");
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $data)
    {
        if ($data) {
            $contents = json_encode($data);
        } else {
            $contents = '{}';
        }

        $this->files->put($this->path, $contents);
    }
}
