<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthorizedKey extends Model
{
    use SoftDeletes;

    protected $table = 'authorized_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'data',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
