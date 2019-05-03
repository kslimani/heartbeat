<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\AuthorizedKey;
use Validator;

trait Authorizable
{
    public function authorizedKeyExists($keyData)
    {
        return AuthorizedKey::where('data', $keyData)
            ->count() === 1;
    }

    public function authorizedKeyValidator(Request $request, $inputName)
    {
        return Validator::make($request->all(), [
            $inputName => ['required', 'string', 'size:39'],
        ]);
    }

    public function requestIsAuthorized(Request $request, $inputName = 'key')
    {
        if ($this->authorizedKeyValidator($request, $inputName)->fails()) {
            return false;
        }

        return $this->authorizedKeyExists($request->input($inputName));
    }

    public function notAuthorized()
    {
        return response('', 403);
    }
}
