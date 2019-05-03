<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\KeyGenerator;
use App\User;

class UserKeyController extends Controller
{
    public function index($id)
    {
        $user = User::findOrFail($id);

        $keys = $user->authorizedKeys()
            ->paginate(config('app.pagination_limit'));

        return view('user-keys/index', [
            'user' => $user,
            'keys' => $keys,
        ]);
    }

    public function generate($userId)
    {
        $user = User::findOrFail($userId);

        KeyGenerator::make($user);

        return redirect()->route('user-keys.index', ['user' => $user->id]);
    }

    public function destroy($userId, $keyId)
    {
        $user = User::findOrFail($userId);

        $key = $user->authorizedKeys()
            ->where('id', $keyId)
            ->firstOrFail();

        $key->delete();

        return redirect()->route('user-keys.index', ['user' => $user->id]);
    }
}
