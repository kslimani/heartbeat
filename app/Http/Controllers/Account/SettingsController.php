<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Support\Settings;

class SettingsController extends Controller
{
    public function edit(Request $request)
    {
        $locales = [
            'en' => __('app.english'),
            'fr' => __('app.french'),
        ];

        $settings = Settings::get();

        return view('account/settings/edit', [
            'locales' => $locales,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'locale' => ['required', 'string', 'size:2'],
        ]);

        $settings = $request->only([
            'locale',
        ]);

        Settings::set($settings);

        return back()
            ->with('alert.success', __('app.settings_updated'));
    }
}
