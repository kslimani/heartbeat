<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;

class ServiceController extends Controller
{
    public function edit($id)
    {
        $service = Service::findOrFail($id);

        return view('services/edit', [
            'service' => $service,
        ]);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'label' => ['required', 'string', 'max:255'],
        ]);

        $inputs = $request->only([
            'label',
        ]);

        $service->update($inputs);

        return redirect()
            ->route('service-statuses.index')
            ->with('alert.success', __('app.services_updated', [
                'name' => $service->name,
            ]));
    }
}
