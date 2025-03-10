<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavouriteCrew extends Model
{
    public $timestamps = false; 
    
    protected $table = 'favourites_crew';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'crew_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    public function crew()
    {
        return $this->belongsTo('App\User', 'crew_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
