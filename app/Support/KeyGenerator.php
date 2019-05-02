<?php

namespace App\Support;

use Illuminate\Support\Str;
use App\User;
use App\AuthorizedKey;

class KeyGenerator
{
    /**
     * Generates a string of 39 characters.
     *
     * @return string
     */
    public static function generate()
    {
        return mb_strtoupper(
            implode(
                '-',
                str_split(
                    str_replace('-', '', Str::uuid()),
                    4
                )
            )
        );
    }

    /**
     * Make an authorized key for user.
     *
     * @param  \App\User  $user
     * @return \App\AuthorizedKey
     */
    public static function make(User $user)
    {
        return $user->authorizedKeys()->create([
            'data' => self::generate(),
        ]);
    }
}
