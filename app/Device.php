<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';

    protected $fillable = [
        'name',
        'label',
    ];

    public function serviceStatuses()
    {
        return $this->hasMany('App\ServiceStatus');
    }
}
