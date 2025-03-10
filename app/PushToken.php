<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushToken extends Model
{
    public $timestamps = false;

    protected $table = 'push_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'push_token', 'date'
    ];
}
