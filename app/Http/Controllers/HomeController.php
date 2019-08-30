<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ServiceHelper;
use App\Support\Locale;
use App\Support\Utils;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $statuses = [
            'INACTIVE' => 0,
            'DOWN' => 0,
            'UP' => 0,
        ];

        // Get user service statuses id list
        $serviceStatusIdList = ServiceHelper::serviceStatusIdList(
            $request->user()
        );

        // Get user devices services statuses
        $serviceStatuses = ServiceHelper::statusesFromIdList($serviceStatusIdList)
            ->get()
            ->sortBy(function($serviceStatus) {
                return $serviceStatus->device->label.$serviceStatus->service->label;
            })
            ->each(function ($serviceStatus) use (&$statuses) {
                $serviceStatus->label_tooltip = sprintf(
                    '%s - %s',
                    $serviceStatus->status->name,
                    Locale::humanDuration(Utils::elapsed($serviceStatus->changed_at))
                );

                switch ($serviceStatus->status->name) {
                    case 'INACTIVE':
                        ++$statuses['INACTIVE'];
                        break;

                    case 'DOWN':
                        ++$statuses['DOWN'];
                        break;

                    case 'UP':
                        ++$statuses['UP'];
                        break;
                }
            });

        // Get user past service events
        $events = ServiceHelper::eventsFromIdList($serviceStatusIdList)
            ->limit(config('app.max_past_events'))
            ->get();

        // Add formatted labels
        ServiceHelper::formatEvents($events);

        return view('home', [
            'updatedOn' => Locale::humanDatetime(),
            'statuses' => $statuses,
            'byDevices' => $serviceStatuses->groupBy('device.id'), // Grouped by devices
            'events' => $events,
        ]);
    }
}
