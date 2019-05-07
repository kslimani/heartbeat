<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AuthorizedKey;
use App\Support\Status\StatusException;
use App\Support\Status\StatusHandler;

class StatusController extends Controller
{
    use Authorizable;

    public function check(Request $request)
    {
        if (! $this->requestIsAuthorized($request)) {
            return $this->notAuthorized();
        }

        return response()->json([], 200);
    }

    public function index(Request $request)
    {
        if (! $user = $this->authorizedUser($request)) {
            return $this->notAuthorized();
        }

        $request->validate([
            'device' => ['required', 'string', 'max:255', 'regex:/^([a-zA-Z_\.\-\d]+)$/u'],  // Alphanumeric, dot, dash & underscore
            'service' => ['required', 'string', 'max:255', 'regex:/^([a-zA-Z_\.\-\d]+)$/u'], // Alphanumeric, dot, dash & underscore
            'status' => ['required', 'string', 'max:255', 'regex:/^([a-zA-Z_]+)$/u'],        // Only letters & underscore
        ]);

        $inputs = $request->only([
            'device',
            'service',
            'status',
        ]);

        try {
            $handler = new StatusHandler($user);
            $handler->handleByNames(
                $inputs['device'],
                $inputs['service'],
                $inputs['status']
            );
        } catch (StatusException $e) {

            return response()->json(
                ['error' => $e->getMessage()],
                $e->getCode()
            );
        } catch (\Exception $e) {

            return response()->json(
                ['error' => __('app.status_update_failed')],
                400
            );
        }

        return response()->json([], 200);
    }
}
