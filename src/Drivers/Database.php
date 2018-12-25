<?php

namespace Laravel\Settings\Drivers;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Support\Arr;
use UnexpectedValueException;

/**
 * Class Database
 *
 * @package     Laravel\Settings\Drivers
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class Database extends Driver
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * The table to query from.
     *
     * @var string
     */
    protected $table;

    /**
     * The key column name to query from.
     *
     * @var string
     */
    protected $key;

    /**
     * The value column name to query from.
     *
     * @var string
     */
    protected $value;

    /**
     * Any query constraints that should be applied.
     *
     * @var Closure|null
     */
    protected $queryConstraint;

    /**
     * Any extra columns that should be added to the rows.
     *
     * @var array
     */
    protected $extraColumns = [];

    /**
     * @param \Illuminate\Database\Connection $connection
     * @param string $table
     * @param string $key
     * @param string $value
     */
    public function __construct(Connection $connection, string $table = null, string $key = null, string $value = null)
    {
        $this->connection = $connection;
        $this->table = $table ?? 'settings';
        $this->key = $key ?? 'key';
        $this->value = $value ?? 'value';
    }

    /**
     * Set the table to query from.
     *
     * @param string $table
     */
    public function setTable(string $table)
    {
        $this->table = $table;
    }

    /**
     * Set the key column name to query from.
     *
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * Set the value column name to query from.
     *
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * Set the query constraint.
     *
     * @param Closure $callback
     */
    public function setConstraint(Closure $callback)
    {
        $this->data = [];
        $this->loaded = false;
        $this->queryConstraint = $callback;
    }

    /**
     * Set extra columns to be added to the rows.
     *
     * @param array $columns
     */
    public function setExtraColumns(array $columns)
    {
        $this->extraColumns = $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function forget(string $key)
    {
        parent::forget($key);

        // because the database store cannot store empty arrays, remove empty
        // arrays to keep data consistent before and after saving
        $segments = explode('.', $key);
        array_pop($segments);

        while ($segments) {
            $segment = implode('.', $segments);

            // non-empty array - exit out of the loop
            if ($this->get($segment)) {
                break;
            }

            // remove the empty array and move on to the next segment
            $this->forget($segment);
            array_pop($segments);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $data)
    {
        $keysQuery = $this->newQuery();
        $keys = $keysQuery->pluck($this->key);

        $insertData = Arr::dot($data);
        $updateData = [];
        $deleteKeys = [];

        foreach ($keys as $key) {
            if (isset($insertData[$key])) {
                $updateData[$key] = $insertData[$key];
            } else {
                $deleteKeys[] = $key;
            }
            unset($insertData[$key]);
        }

        foreach ($updateData as $key => $value) {
            $this->newQuery()
                ->where($this->key, '=', $key)
                ->update([$this->value => $value]);
        }

        if ($insertData) {
            $this->newQuery(true)
                ->insert($this->prepareInsertData($insertData));
        }

        if ($deleteKeys) {
            $this->newQuery()
                ->whereIn($this->key, $deleteKeys)
                ->delete();
        }
    }

    /**
     * Transforms settings data into an array ready to be insterted into the
     * database. Call array_dot on a multidimensional array before passing it
     * into this method!
     *
     * @param  array $data Call array_dot on a multidimensional array before passing it into this method!
     * @return array
     */
    protected function prepareInsertData(array $data)
    {
        $dbData = [];

        if ($this->extraColumns) {
            foreach ($data as $key => $value) {
                $dbData[] = array_merge(
                    $this->extraColumns,
                    [$this->key => $key, $this->value => $value]
                );
            }
        } else {
            foreach ($data as $key => $value) {
                $dbData[] = [$this->key => $key, $this->value => $value];
            }
        }

        return $dbData;
    }

    /**
     * {@inheritdoc}
     */
    protected function read(): array
    {
        return $this->parseReadData($this->newQuery()->get());
    }

    /**
     * Parse data coming from the database.
     *
     * @param  array|\Illuminate\Support\Collection $data
     * @return array
     * @throws \UnexpectedValueException
     */
    public function parseReadData($data): array
    {
        $results = [];

        foreach ($data as $row) {
            if (is_array($row)) {
                $key = $row[$this->key];
                $value = $row[$this->value];
            } elseif (is_object($row)) {
                $key = $row->{$this->key};
                $value = $row->{$this->value};
            } else {
                throw new UnexpectedValueException('Expected array or object, got ' . gettype($row));
            }

            Arr::set($results, $key, $value);
        }

        return $results;
    }

    /**
     * Create a new query builder instance.
     *
     * @param  bool $insert Whether the query is an insert or not.
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newQuery(bool $insert = false)
    {
        $query = $this->connection->table($this->table);

        if (!$insert) {
            foreach ($this->extraColumns as $key => $value) {
                $query->where($key, '=', $value);
            }
        }

        if ($this->queryConstraint !== null) {
            $callback = $this->queryConstraint;
            $callback($query, $insert);
        }

        return $query;
    }
}
