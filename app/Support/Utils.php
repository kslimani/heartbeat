<?php

namespace App\Support;

use App\ServiceEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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

    /**
     * Log a service event as a debug message to the logs.
     *
     * @param  \App\ServiceEvent  $event
     * @return void
     */
    public static function logServiceEvent(ServiceEvent $event)
    {
        Log::debug(sprintf(
            '[%s @ %s] %s %s %s %s %s',
            $event->serviceStatus->service->label,
            $event->serviceStatus->device->label,
            __('app.status_changed'),
            __('app.from'),
            $event->fromStatus->name,
            __('app.to'),
            $event->toStatus->name
        ));
    }
}
