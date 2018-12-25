<?php

if (!function_exists('settings')) {
    /**
     * Get / set the specified setting value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function settings($key = null, $default = null)
    {
        /** @var \Laravel\Settings\Manager $settings */
        $settings = app('settings');

        if (is_null($key)) {
            return $settings;
        }

        if (is_array($key)) {
            $settings->set($key);

            return $settings;
        }

        return $settings->get($key, $default);
    }
}
