<?php

namespace App\Support;

use App\Role;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AppInstaller
{
    public function install($email, $password)
    {
        $this
            ->createRoles()
            ->createAdminUser('Admin', $email, $password)
        ;
    }

    public function createRoles()
    {
        collect([
            Role::ADMIN,
        ])->each(function($name) {
            Role::firstOrCreate(['name' => $name]);
        });

        return $this;
    }

    public function validateUser($name, $email, $password)
    {
        // Based on \App\Http\Controllers\Auth\RegisterController validator
        return Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ])->passes();
    }

    public function createUser($name, $email, $password, $verified = false)
    {
        if (! $this->validateUser($name, $email, $password)) {
            throw new \Exception('Invalid user data');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
        }

        if ($verified) {
            $user->markEmailAsVerified();
        }

        return $user;
    }

    public function createAdminUser($name, $email, $password, $verified = true)
    {
        $adminRole = Role::where('name', Role::ADMIN)->firstOrFail();

        $this->createUser($name, $email, $password, $verified)
            ->roles()
            ->attach($adminRole->id);

        return $this;
    }
}
