<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventUserBlocked extends Model
{
    public $timestamps = false; 
    
    protected $table = 'event_user_blocked';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'all_day', 'text', 'start_date', 'end_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
