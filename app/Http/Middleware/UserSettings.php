<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\Locale;
use App\Support\Settings;
use Illuminate\Support\Facades\Auth;

class UserSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (Auth::check() && $settings = Settings::getAuth()) {
                if (isset($settings['locale'])) {
                    Locale::set($settings['locale']);
                }
                if (isset($settings['tz'])) {
                    // Store user timezone in app config to ease re-use
                    config(['app.user_tz' => $settings['tz']]);
                }
            }
        } catch (\Exception $e) {
            // Silently fails
        }

        return $next($request);
    }
}
