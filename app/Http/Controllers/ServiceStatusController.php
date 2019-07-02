<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ServiceHelper;
use App\Support\Utils;

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

        return redirect()
            ->route('service-events.show', ['id' => $serviceStatusId])
            ->with('alert.info', sprintf(
                '%s %s',
                __('app.notifications'),
                $isMute ? __('app.disabled') : __('app.enabled')
            ));
    }
}
