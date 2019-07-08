<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ServiceHelper;
use App\Support\Utils;

class ServiceEventController extends Controller
{
    public function index(Request $request)
    {
        // Get user service statuses id list
        $serviceStatusIdList = ServiceHelper::serviceStatusIdList(
            $request->user()
        );

        // Get user service events
        $events = ServiceHelper::eventsFromIdList($serviceStatusIdList)
            ->paginate(config('app.pagination_limit'));

        // Add formatted labels
        ServiceHelper::formatEvents($events);

        return view('service-events/index', [
            'updatedOn' => Utils::humanDatetime(null, config('app.user_tz')),
            'events' => $events,
        ]);
    }
}
