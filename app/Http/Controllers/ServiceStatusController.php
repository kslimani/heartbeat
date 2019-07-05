<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ServiceHelper;
use App\Support\Utils;
use App\ServiceStatus;

class ServiceStatusController extends Controller
{
    public function show(Request $request, $serviceStatusId)
    {
        // Get user service status
        $serviceStatus = $request->user()
            ->serviceStatuses()
            ->with([
                'device',
                'service',
                'status',
            ])            
            ->where('id', $serviceStatusId)
            ->firstOrFail();

        // Add "current status" formatted label
        $serviceStatus->label_status = sprintf(
            '%s - %s',
            $serviceStatus->status->name,
            Utils::humanDuration(Utils::elapsed($serviceStatus->updated_at))
        );

        // Get service events
        $events = ServiceHelper::eventsFromId($serviceStatus->id)
            ->paginate(config('app.pagination_limit'));

        // Add formatted labels
        ServiceHelper::formatEvents($events);

        return view('service-statuses/show', [
            'updatedOn' => Utils::humanDatetime(),
            'serviceStatus' => $serviceStatus,
            'isMute' => (bool) $serviceStatus->pivot->is_mute,
            'events' => $events,
        ]);
    }

    public function update(Request $request, $serviceStatusId)
    {
        $request->validate([
            'is_mute' => ['required', 'boolean'],
        ]);

        $isMute = (bool) $request->input('is_mute');

        $user = $request->user();

        $serviceStatus = $user->serviceStatuses()
            ->where('id', $serviceStatusId)
            ->firstOrFail();

        $user->serviceStatuses()
            ->updateExistingPivot($serviceStatus->id, [
                'is_mute' => $isMute,
            ]);

        return back()
            ->with('alert.info', sprintf(
                '%s %s',
                __('app.notifications'),
                $isMute ? __('app.disabled') : __('app.enabled')
            ));
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'max:30'],
        ]);

        $like = $request->input('q');

        $serviceStatuses = ServiceHelper::statuses([
                'device',
                'service',
            ])
            ->orWhere('services.label', 'like', $like.'%')
            ->orWhere('devices.label', 'like', $like.'%')
            ->orWhere('services.name', 'like', $like.'%')
            ->orWhere('devices.name', 'like', $like.'%')
            ->orderBy('devices.label')
            ->orderBy('services.label')
            ->limit(config('app.search_limit'))
            ->get()
            ->transform(function($serviceStatus) {
                // Autocomplete jQuery plugin expected format
                return [
                    'id' => $serviceStatus->id,
                    'text' => sprintf(
                        '%s @ %s',
                        $serviceStatus->service->label,
                        $serviceStatus->device->label
                    ),
                ];
            });

        return response()->json($serviceStatuses);
    }
}
