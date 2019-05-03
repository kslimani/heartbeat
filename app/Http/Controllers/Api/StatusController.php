<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AuthorizedKey;

class StatusController extends Controller
{
    use Authorizable;

    public function check(Request $request)
    {
        if (! $this->requestIsAuthorized($request)) {
            return $this->notAuthorized();
        }

        return response('', 200);
    }
}
