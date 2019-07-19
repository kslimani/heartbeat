<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')
            ->paginate(config('app.pagination_limit'));

        return view('users/index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return view('users/create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $inputs = $request->only([
            'name',
            'email',
            'password',
        ]);

        $user = User::create([
            'name' => $inputs['name'],
            'email' => $inputs['email'],
            'password' => Hash::make($inputs['password']),
        ]);

        return redirect()
            ->route('users.index')
            ->with('alert.success', __('app.users_created', [
                'name' => $user->name,
            ]));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users/edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $inputs = $request->only([
            'name',
            'email',
        ]);

        $user->update($inputs);

        return redirect()
            ->route('users.index')
            ->with('alert.success', __('app.users_updated', [
                'name' => $user->name,
            ]));
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevents self delete
        if ($request->user()->id === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('alert.danger', __('app.not_permitted'));
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('alert.success', __('app.users_deleted', [
                'name' => $user->name,
            ]));
    }

    public function search(Request $request, $param = 'term')
    {
        $request->validate([
            $param => ['required', 'string', 'max:50'],
        ]);

        $like = $request->input($param);

        $users = User::where('name', 'like', $like.'%')
            ->orWhere('email', 'like', '%'.$like.'%')
            ->orderBy('name')
            ->limit(config('app.search_limit'))
            ->get()
            ->transform(function($user) {
                // Typeahead javascript plugin expected format
                return [
                    'id' => $user->id,
                    'name' => sprintf(
                        '%s - (%s)',
                        $user->name,
                        $user->email
                    ),
                ];
            });

        return response()->json($users);
    }
}
