<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserSettings;

class Settings
{
    const SESSION_KEY = '__settings';

    public static function getAuth()
    {
        $settings = session(self::SESSION_KEY);

        if (! $settings) {
            $settings = self::get(Auth::user());
            session([self::SESSION_KEY => $settings]);
        }

        return $settings;
    }

    public static function setAuth(array $settings)
    {
        $settings = self::set(Auth::User(), $settings);
        session([self::SESSION_KEY => $settings]);

        if (isset($settings['locale'])) {
            Locale::set($settings['locale']);
        }

        return $settings;
    }

    public static function get(User $user)
    {
        $userSettings = $user->settings;

        if (! $userSettings) {
            return self::default();
        }

        return self::mergeWithDefault($userSettings->settings);
    }

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

    public static function default()
    {
        return config('app.user_settings');
    }

    public static function mergeWithDefault(array $settings)
    {
        return array_merge(self::default(), $settings);
    }
}
