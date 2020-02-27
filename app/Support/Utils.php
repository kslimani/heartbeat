<?php

namespace App\Support;

use Illuminate\Support\Carbon;

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

    /**
     * Check if two values does not equals as integer.
     * (some DB engine may return integer value as string)
     *
     * @param  mixed  $intA
     * @param  mixed  $intB
     * @return bool
     */
    public static function intNotEquals($intA, $intB)
    {
        return (int) $intA !== (int) $intB;
    }

    /**
     * Check if two values equals as integer.
     *
     * @param  mixed  $intA
     * @param  mixed  $intB
     * @return bool
     */
    public static function intEquals($intA, $intB)
    {
        return !self::intNotEquals($intA, $intB);
    }
}
