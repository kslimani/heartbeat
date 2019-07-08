<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Carbon\CarbonInterval;

class Utils
{
    const ALL_DEVICES_MUTED = '_all_devices_muted';

    /**
     * Get elapsed duration in seconds
     *
     * @param  \Illuminate\Support\Carbon  $date
     * @return int
     */
    public static function elapsed(Carbon $date)
    {
        $elapsed = Carbon::now()->timestamp - $date->timestamp;

        return $elapsed > 0 ? $elapsed : 0;
    }

    /**
     * Format datetime to human readable
     *
     * @param  \Illuminate\Support\Carbon  $date
     * @return string
     */
    public static function humanDatetime(Carbon $date = null, $tz = null)
    {
        if (is_null($date)) {
            $date = Carbon::now();
        }

        if (! is_null($tz)) {
            $date->tz = $tz;
        }

        // https://carbon.nesbot.com/docs/#iso-format-available-replacements
        return ucfirst($date->isoFormat(
            Locale::isoFormat(Locale::TYPE_DATETIME)
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

    /**
     * Mute all devices
     */
    public static function muteAllDevices()
    {
        AppStore::put(self::ALL_DEVICES_MUTED, true);
    }

    /**
     * Unmute all devices
     */
    public static function unmuteAllDevices()
    {
        AppStore::put(self::ALL_DEVICES_MUTED, false);
    }

    /**
     * Check if all devices are muted
     *
     * @return bool
     */
    public static function allDeviceMuted()
    {
        return AppStore::get(self::ALL_DEVICES_MUTED) === true;
    }
}
