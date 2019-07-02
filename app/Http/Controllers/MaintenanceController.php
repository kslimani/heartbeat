<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\Utils;

class MaintenanceController extends Controller
{
    public function show(Request $request)
    {
        $allMuted = Utils::allDeviceMuted();

        return view('maintenance/show', [
            'allMuted' => $allMuted,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'mute_all' => ['required', 'boolean'],
        ]);

        $muteAll = (bool) $request->input('mute_all');

        if ($muteAll) {
            Utils::muteAllDevices();
        } else {
            Utils::unmuteAllDevices();
        }

        return redirect()
            ->route('maintenance.show')
            ->with($muteAll ? 'alert.warning' : 'alert.info', sprintf(
                '%s %s',
                __('app.maintenance'),
                $muteAll ? __('app.enabled') : __('app.disabled')
            ));
    }
}
