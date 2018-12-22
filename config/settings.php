<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Setting driver
    |--------------------------------------------------------------------------
    |
    | Select where to store the settings.
    |
    | Supported: "database", "json", "array", "memory", "cache"
    |
    */
    'driver' => 'cache',

    /*
    |--------------------------------------------------------------------------
    | Database driver
    |--------------------------------------------------------------------------
    |
    | Options for database driver. Enter which connection to use, null means
    | the default connection. Set the table and column names.
    |
    */
    'database' => [
        'connection' => null,
        'table' => 'settings',
        'key' => 'key',
        'value' => 'value',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache driver
    |--------------------------------------------------------------------------
    |
    | Options for cache driver. Enter which connection to use, null means
    | the default driver.
    |
    */
    'cache' => [
        'driver' => null,
        'cache_key' => 'app_settings',
        'failback' => 'database',
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON driver
    |--------------------------------------------------------------------------
    |
    | Options for json driver. Enter the full path to the .json file.
    |
    */
    'json' => [
        'path' => storage_path() . '/app/settings.json',
    ],

    /*
    |--------------------------------------------------------------------------
    | Override application config values
    |--------------------------------------------------------------------------
    |
    | If defined, settings package will override these config values.
    |
    | Sample:
    |   "app.fallback_locale",
    |   "app.locale" => "settings.locale",
    |
    */
    'override' => [

    ],
];
