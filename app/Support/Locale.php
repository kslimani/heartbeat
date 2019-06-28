<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class Locale
{
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIME = 'time';

    protected static $app;
    protected static $time;
    protected static $aliases = [];
    protected static $isoFormats = [];

    public static function boot(array $options = [])
    {
        if (isset($options['aliases'])) {
            self::$aliases = $options['aliases'];
        }

        if (isset($options['iso_formats'])) {
            self::$isoFormats = $options['iso_formats'];
        }

        self::$app = self::get();
        self::$time = setlocale(LC_TIME, 0);
        self::setTime(self::$app);
    }

    public static function restore()
    {
        self::setApp(self::$app);
        self::setTime(self::$time);
    }

    public static function setAlias($locale, $alias)
    {
        self::$aliases[$locale] = $alias;
    }

    public static function setIsoFormat($locale, $type, $isoFormat)
    {
        self::$isoFormats[$locale][$type] = $isoFormat;
    }

    public static function locale($locale)
    {
        return isset(self::$aliases[$locale])
            ? self::$aliases[$locale]
            : $locale;
    }

    public static function setApp($locale)
    {
        App::setLocale($locale);
        Carbon::setLocale($locale);
    }

    public static function setTime($locale)
    {
        setlocale(LC_TIME, self::locale($locale));
    }

    public static function set($locale)
    {
        self::setApp($locale);
        self::setTime($locale);
    }

    public static function get()
    {
        return App::getLocale();
    }

    public static function getFallback()
    {
        return config('app.fallback_locale');
    }

    public static function is($locale)
    {
        return $locale === self::get();
    }

    public static function isoFormat($type, $locale = null)
    {
        if (is_null($locale)) {
            $locale = self::get();
        }

        if (isset(self::$isoFormats[$locale][$type])) {
            return self::$isoFormats[$locale][$type];
        }

        // Custom iso format is undefined
        switch ($type) {
            case self::TYPE_DATE :
                return 'LL';

            case self::TYPE_TIME :
                return 'LTS';

            // Defaults to DATETIME
            default:
                return 'LLLL';
        }
    }
}
