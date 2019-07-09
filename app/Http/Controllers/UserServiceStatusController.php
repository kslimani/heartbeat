<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ServiceHelper;
use App\User;
use App\ServiceStatus;

class UserServiceStatusController extends Controller
{
    public function index($userId)
    {
        $user = User::findOrFail($userId);

        $serviceStatuses = ServiceHelper::userStatuses($user)
            ->orderBy('devices.label')
            ->orderBy('services.label')
            ->paginate(config('app.pagination_limit'));

        return view('user-service-statuses/index', [
            'user' => $user,
            'serviceStatuses' => $serviceStatuses,
        ]);
    }

    public function update(Request $request, $userId, $serviceStatusId)
    {
        $user = User::findOrFail($userId);

        $serviceStatus = $user->serviceStatuses()
            ->where('id', $serviceStatusId)
            ->firstOrFail();

        $request->validate([
            'is_mute' => ['sometimes', 'required', 'boolean'],
            'is_updatable' => ['sometimes', 'required', 'boolean'],
        ]);

        $pivots = $request->only([
            'is_mute',
            'is_updatable',
        ]);

        if (! empty($pivots)) {
            $user->serviceStatuses()
                ->updateExistingPivot($serviceStatus->id, $pivots);
        }

        return back();
    }

    public function attach(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'service_status_id' => ['required', 'integer'],
            'is_updatable' => ['required', 'boolean'],
            'is_mute' => ['required', 'boolean'],
        ]);

        $serviceStatusId = (int) $request->input('service_status_id');

        $pivots = $request->only([
            'is_updatable',
            'is_mute',
        ]);

        $serviceStatus = ServiceStatus::findOrFail($serviceStatusId);

        $serviceStatus->users()->syncWithoutDetaching([
            $user->id => $pivots,
        ]);

        return redirect()
            ->route('user-service-statuses.index', ['user' => $user->id])
            ->with('alert.success', __('app.service_status_attached', [
                'label' => sprintf(
                    '%s @ %s',
                    $serviceStatus->service->label,
                    $serviceStatus->device->label
                ),
            ]));
    }

    public function attachAll($userId)
    {
        $user = User::with(['roles'])->findOrFail($userId);

        // Note: all pivots will be reset to default
        $pivots = [
            'is_updatable' => $user->isAdmin(),
            'is_mute' => false,
        ];

        $relations = [];

        ServiceStatus::pluck('id')
            ->each(function($serviceStatusId) use (&$relations, $pivots) {
                $relations[$serviceStatusId] = $pivots;
            });

        $user->serviceStatuses()
            ->syncWithoutDetaching($relations);

        return redirect()
            ->route('user-service-statuses.index', ['user' => $user->id])
            ->with('alert.success', __('app.all_services_attached'));
    }

    public function detach($userId, $serviceStatusId)
    {
        $user = User::findOrFail($userId);

        $serviceStatus = $user->serviceStatuses()
            ->with(['device', 'service'])
            ->where('id', $serviceStatusId)
            ->firstOrFail();

        $user->serviceStatuses()->detach($serviceStatus->id);

        return back()
            ->with('alert.info', __('app.service_status_detached', [
                'label' => sprintf(
                    '%s @ %s',
                    $serviceStatus->service->label,
                    $serviceStatus->device->label
                ),
            ]));
    }
}
