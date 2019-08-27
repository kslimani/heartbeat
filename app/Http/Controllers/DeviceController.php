<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Device;

class DeviceController extends Controller
{
    public function edit($id)
    {
        $device = Device::findOrFail($id);

        return view('devices/edit', [
            'device' => $device,
        ]);
    }

    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $request->validate([
            'label' => ['required', 'string', 'max:255'],
        ]);

        $inputs = $request->only([
            'label',
        ]);

        $device->update($inputs);

        return redirect()
            ->route('service-statuses.index')
            ->with('alert.success', __('app.devices_updated', [
                'name' => $device->name,
            ]));
    }
}
