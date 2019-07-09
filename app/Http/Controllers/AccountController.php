<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\Locale;
use App\Support\Settings;
use Carbon\CarbonTimeZone;
use DateTimeZone;

class AccountController extends Controller
{
    public function settings(Request $request)
    {
        return $this->edit($request, 'settings');
    }

    public function profile(Request $request)
    {
        return $this->edit($request, 'profile');
    }

    public function security(Request $request)
    {
        return $this->edit($request, 'security');
    }

    protected function edit(Request $request, $activeTab)
    {
        $locales = $this->locales();
        $timezones = $this->timezones();
        $settings = Settings::getAuth();

        return view('account/edit', [
            'tab' => $activeTab,
            'user' => $request->user(),
            'locales' => $locales,
            'timezones' => $timezones,
            'settings' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
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
        Locale::set($settings['locale']);

        return redirect()
            ->route('account-settings')
            ->with('alert.success', __('app.settings_updated'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $inputs = $request->only([
            'name',
            'email',
        ]);

        $user->update($inputs);

        return redirect()
            ->route('account-profile')
            ->with('alert.success', __('app.profile_updated'));
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $password = $request->input('password');

        $user->update([
            'password' => Hash::make($password),
        ]);

        return redirect()
            ->route('account-security')
            ->with('alert.success', __('app.password_updated'));
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

        // FIXME: find a way to build localized labels
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
