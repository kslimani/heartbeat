<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogSql
{
    /**
     * Enable DB SQL queries "debug" log messages.
     *
     * @return void
     */
    public static function debug()
    {
        DB::listen(function ($query) {
            Log::debug($query->sql, [
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        });
    }
}
