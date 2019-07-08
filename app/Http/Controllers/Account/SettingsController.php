<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Support\Settings;
use Carbon\CarbonTimeZone;
use DateTimeZone;

class SettingsController extends Controller
{
    public function edit(Request $request)
    {
        $locales = $this->locales();
        $timezones = $this->timezones();
        $settings = Settings::getAuth();

        return view('account/settings/edit', [
            'locales' => $locales,
            'timezones' => $timezones,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'locale' => ['required', 'string', 'size:2'],
            'tz' => ['required', 'timezone'],
        ]);

        $settings = $request->only([
            'locale',
            'tz'
        ]);

        Settings::setAuth($settings);

        return back()
            ->with('alert.success', __('app.settings_updated'));
    }

    protected function locales()
    {
        return [
            'en' => __('app.english'),
            'fr' => __('app.french'),
        ];
    }

    protected function timezones()
    {
        $timezones = [];
        $names = DateTimeZone::listIdentifiers();

        foreach ($names as $name) {
            $timezone = CarbonTimeZone::create($name);
            $timezones[$name] = sprintf(
                '%s (%s)',
                $name,
                $timezone->toOffsetName()
            );
        }

        return $timezones;
    }
}
