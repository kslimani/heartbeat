<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ServiceHelper;
use App\Support\Locale;
use App\Support\Utils;
use App\ServiceStatus;

class ServiceStatusController extends Controller
{
    const REGEX_RULE = 'regex:/^([a-zA-Z_\.\-\d]+)$/u'; // Alphanumeric, dot, dash & underscore

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
            Locale::humanDuration(Utils::elapsed($serviceStatus->changed_at))
        );

        // Get service events
        $events = ServiceHelper::eventsFromId($serviceStatus->id)
            ->paginate(config('app.pagination_limit'));

        // Add formatted labels
        ServiceHelper::formatEvents($events);

        return view('service-statuses/show', [
            'updatedOn' => Locale::humanDatetime(),
            'serviceStatus' => $serviceStatus,
            'isMute' => (bool) $serviceStatus->pivot->is_mute,
            'events' => $events,
        ]);
    }

    public function editSettings(Request $request, $serviceStatusId)
    {
        $serviceStatus = $request->user()
            ->serviceStatuses()
            ->with([
                'device',
                'service',
            ])
            ->where('id', $serviceStatusId)
            ->firstOrFail();

        return view('service-statuses/settings', [
            'serviceStatus' => $serviceStatus,
            'defaultRtd' => config('app.report_tolerance_delay'),
        ]);
    }

    public function updateSettings(Request $request, $serviceStatusId)
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

        return redirect()->route('service-statuses.show', [
            'id' => $serviceStatus->id
        ]);
    }

    public function index(Request $request, $searchParam = 'q')
    {
        $request->validate([
            $searchParam => ['nullable', 'string', 'max:50', self::REGEX_RULE],
        ]);

        $query = ServiceHelper::statuses([
            'device',
            'service',
        ]);

        $search = $request->input($searchParam);

        if ($search) {
            $like = '%'.$search.'%';
            $query = $query->orWhere('services.label', 'like', $like)
                ->orWhere('devices.label', 'like', $like)
                ->orWhere('services.name', 'like', $like)
                ->orWhere('devices.name', 'like', $like);
        }

        $serviceStatuses = $query->orderBy('devices.label')
            ->orderBy('services.label')
            ->paginate(config('app.pagination_limit'));

        return view('service-statuses/index', [
            'search' => $search,
            'serviceStatuses' => $serviceStatuses,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $serviceStatus = ServiceStatus::with(['device', 'service'])
            ->findOrFail($id);

        // Delete service status and events
        $serviceStatus->delete();
        $serviceStatus->events()->delete();

        return redirect()
            ->route('service-statuses.index')
            ->with('alert.success', __('app.service_status_deleted', [
                'label' => sprintf(
                    '%s @ %s',
                    $serviceStatus->device->name,
                    $serviceStatus->service->name
                ),
            ]));
    }

    public function search(Request $request, $searchParam = 'term')
    {
        $request->validate([
            $searchParam => ['required', 'string', 'max:50', self::REGEX_RULE],
        ]);

        $like = $request->input($searchParam);
        $like = '%'.$like.'%';

        $serviceStatuses = ServiceHelper::statuses([
                'device',
                'service',
            ])
            ->orWhere('services.label', 'like', $like)
            ->orWhere('devices.label', 'like', $like)
            ->orWhere('services.name', 'like', $like)
            ->orWhere('devices.name', 'like', $like)
            ->orderBy('devices.label')
            ->orderBy('services.label')
            ->limit(config('app.search_limit'))
            ->get()
            ->transform(function($serviceStatus) {
                // Typeahead javascript plugin expected format
                return [
                    'id' => $serviceStatus->id,
                    'name' => sprintf(
                        '%s @ %s',
                        $serviceStatus->service->label,
                        $serviceStatus->device->label
                    ),
                ];
            });

        return response()->json($serviceStatuses);
    }
}
