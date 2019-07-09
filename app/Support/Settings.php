<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserSettings;

class Settings
{
    const SESSION_KEY = '__settings';

    /**
     * Get logged in user settings
     *
     * @return array
     */
    public static function getAuth()
    {
        $settings = session(self::SESSION_KEY);

        if (! $settings) {
            $settings = self::get(Auth::user());
            session([self::SESSION_KEY => $settings]);
        }

        return $settings;
    }

    /**
     * Set logged in user settings
     *
     * @param  array  $settings
     * @return array
     */
    public static function setAuth(array $settings)
    {
        $settings = self::set(Auth::User(), $settings);
        session([self::SESSION_KEY => $settings]);

        return $settings;
    }

    /**
     * Get user settings
     *
     * @param  \App\User  $user
     * @return array
     */
    public static function get(User $user)
    {
        $userSettings = $user->settings;

        if (! $userSettings) {
            return self::default();
        }

        return self::mergeWithDefault($userSettings->settings);
    }

    /**
     * Set user settings
     *
     * @param  \App\User  $user
     * @param  array  $settings
     * @return array
     */
    public static function set(User $user, array $settings)
    {
        $userSettings = $user->settings;
        $settings = self::mergeWithDefault($settings);

        if (! $user->settings) {
            $userSettings = new UserSettings([
                'user_id' => $user->id,
            ]);
        }

        $userSettings->settings = $settings;
        $userSettings->save();

        return $settings;
    }

    /**
     * Get default settings
     *
     * @return array
     */
    public static function default()
    {
        return config('app.user_settings');
    }

    /**
     * Merge settings with default settings
     *
     * @param  array  $settings
     * @return array
     */
    public static function mergeWithDefault(array $settings)
    {
        return array_merge(self::default(), $settings);
    }
}
