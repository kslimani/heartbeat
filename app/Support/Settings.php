<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use App\UserSettings;

class Settings
{
    const SESSION_KEY = '__settings';

    public static function get()
    {
        $settings = session(self::SESSION_KEY);

        if (! $settings) {
            $settings = self::mergeWithDefault(self::userSettings()->settings);
            session([self::SESSION_KEY => $settings]);
        }

        return $settings;
    }

    public static function set(array $settings)
    {
        $settings = self::mergeWithDefault($settings);

        $userSettings = self::userSettings();
        $userSettings->settings = $settings;
        $userSettings->save();

        session([self::SESSION_KEY => $settings]);

        if (isset($settings['locale'])) {
            Locale::set($settings['locale']);
        }

        return $settings;
    }

    protected static function userSettings()
    {
        $user = Auth::user();

        if (! $user) {
            throw new \LogicException('User not authenticated');
        }

        $userSettings = $user->settings;

        if (! $userSettings) {
            $userSettings = new UserSettings([
                'user_id' => $user->id,
                'settings' => self::default(),
            ]);
        }

        return $userSettings;
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
