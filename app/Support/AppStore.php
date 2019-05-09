<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class AppStore
{
    const APP_STORE = 'appstore';

    /**
     * Get application store
     *
     * @return \Illuminate\Contracts\Cache\Store
     */
    public static function store()
    {
        return Cache::store(self::APP_STORE);
    }

    /**
     * Get value from store
     *
     * @param  string  $key
     * @return mixed
     */
    public static function get($key)
    {
        return self::store()->get($key);
    }

    /**
     * Put value into store
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public static function put($key, $value)
    {
        return self::store()->forever($key, $value);
    }
}
