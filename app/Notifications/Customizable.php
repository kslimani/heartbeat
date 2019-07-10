<?php

namespace App\Notifications;

use App\Support\Locale;
use App\Support\Settings;
use App\User;

trait Customizable
{
    /**
     * Apply user customizations
     *
     * @param  \App\User  $user
     * @return $this
     */
    public function customize(User $user)
    {
        $settings = Settings::get($user);
        Locale::set($settings['locale']);
        Locale::setTz($settings['tz']);

        return $this;
    }
}
