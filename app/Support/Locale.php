<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Carbon\CarbonInterval;

class Locale
{
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATETIME_SHORT = 'datetime_short';
    const TYPE_TIME = 'time';

    protected static $app;
    protected static $time;
    protected static $aliases = [];
    protected static $isoFormats = [];
    protected static $appTz;
    protected static $prevTz;
    protected static $tz;

    /**
     * Boot locale
     *
     * @param  array  $options
     * @return void
     */
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
        self::$appTz = App::get('config')['app.timezone'];
    }

    /**
     * Restore previous locale
     *
     * @return void
     */
    public static function restore()
    {
        self::setApp(self::$app);
        self::setTime(self::$time);

        if (! is_null(self::$prevTz)) {
            self::$tz = self::$prevTz;
        }
    }

    /**
     * Set datetime locale alias
     *
     * @param  string  $locale
     * @param  string  $alias
     * @return void
     */
    public static function setAlias($locale, $alias)
    {
        self::$aliases[$locale] = $alias;
    }

    /**
     * Set datetime iso format
     *
     * @param  string  $locale
     * @param  string  $type
     * @param  string  $isoFormat
     * @return void
     */
    public static function setIsoFormat($locale, $type, $isoFormat)
    {
        self::$isoFormats[$locale][$type] = $isoFormat;
    }

    /**
     * Get datetime locale alias
     *
     * @param  string  $locale
     * @return string
     */
    public static function locale($locale)
    {
        return isset(self::$aliases[$locale])
            ? self::$aliases[$locale]
            : $locale;
    }

    /**
     * Set application locale
     *
     * @param  string  $locale
     * @return void
     */
    public static function setApp($locale)
    {
        App::setLocale($locale);
        Carbon::setLocale($locale);
    }

    /**
     * Set datetime locale
     *
     * @param  string  $locale
     * @return void
     */
    public static function setTime($locale)
    {
        setlocale(LC_TIME, self::locale($locale));
    }

    /**
     * Set current locale
     *
     * @param  string  $locale
     * @return void
     */
    public static function set($locale)
    {
        self::setApp($locale);
        self::setTime($locale);
    }

    /**
     * Get current locale
     *
     * @return string
     */
    public static function get()
    {
        return App::getLocale();
    }

    /**
     * Get application fallback locale
     *
     * @return string
     */
    public static function getFallback()
    {
        return config('app.fallback_locale');
    }

    /**
     * Determine if current locale is the given locale
     *
     * @param  string  $locale
     * @return bool
     */
    public static function is($locale)
    {
        return $locale === self::get();
    }

    /**
     * Get datetime iso format for the given type
     *
     * @param  string  $type
     * @param  string  $locale
     * @return string
     */
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
            case self::TYPE_DATE:
                return 'LL';

            case self::TYPE_TIME:
                return 'LTS';

            case self::TYPE_DATETIME_SHORT:
                return 'll LT';

            // Defaults to DATETIME
            default:
                return 'LLLL';
        }
    }

    /**
     * Get current display time zone
     *
     * @return string
     */
    public static function tz()
    {
        return is_null(self::$tz)
            ? self::$appTz
            : self::$tz;
    }

    /**
     * Set current display time zone
     *
     * @return string
     */
    public static function setTz($timezone)
    {
        if (! is_null(self::$tz)) {
            self::$prevTz = self::$tz;
        }

        // Does NOT change application time zone
        // and is only used by date formatters
        self::$tz = $timezone;
    }

    /**
     * Format datetime to human readable
     *
     * @param  \Illuminate\Support\Carbon  $date
     * @param  string  $type
     * @param  string  $tz
     * @return string
     */
    public static function humanDatetime(Carbon $date = null, $type = self::TYPE_DATETIME, $tz = null)
    {
        $fmtDate = is_null($date) ? Carbon::now() : $date->copy();
        $fmtDate->tz = is_null($tz) ? self::tz() : $tz;

        // https://carbon.nesbot.com/docs/#iso-format-available-replacements
        return ucfirst($fmtDate->isoFormat(
            self::isoFormat($type)
        ));
    }

    /**
     * Format duration in seconds to human readable
     *
     * @param  int  $seconds
     * @return string
     */
    public static function humanDuration($seconds)
    {
        return CarbonInterval::seconds($seconds)->cascade()->forHumans();
    }
}
