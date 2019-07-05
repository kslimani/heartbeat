<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ServiceHelper;
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
            ->sortBy('service.label')
            ->sortBy('device.label')
            ->each(function ($item) use (&$statuses) {
                $item->label_tooltip = sprintf(
                    '%s - %s',
                    $item->status->name,
                    Utils::humanDuration(Utils::elapsed($item->updated_at))
                );

                switch ($item->status->name) {
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
            'updatedOn' => Utils::humanDatetime(),
            'statuses' => $statuses,
            'byDevices' => $serviceStatuses->groupBy('device.id'), // Grouped by devices
            'events' => $events,
        ]);
    }
}
