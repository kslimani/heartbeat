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
}
