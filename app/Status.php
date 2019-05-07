<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    const INACTIVE = 'INACTIVE';

    protected $table = 'status';

    protected $fillable = [
        'name',
    ];

    public static function inactive()
    {
        return Status::where('name', self::INACTIVE)->firstOrFail();
    }
}
