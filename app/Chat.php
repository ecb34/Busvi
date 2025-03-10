<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'event_id', 'user_id', 'role', 'text', 'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @v$servicesar array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];


    public function event()
    {
        return $this->belongsTo('App\Event', 'event_id', 'id');
    }

    public function reserva()
    {
        return $this->belongsTo('App\Reserva', 'reserva_id', 'id');
    }

}
