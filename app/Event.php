<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'start_date', 'end_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $cast = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];



    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\User', 'customer_id');
    }

    public function service()
    {
        return $this->belongsTo('App\Service', 'service_id');
    }

    public function chat()
    {
        return $this->hasMany('App\Chat', 'event_id', 'id');
    }
}
