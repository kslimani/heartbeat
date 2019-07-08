<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $table = 'user_settings';

    protected $casts = [
        'settings' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'settings',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
