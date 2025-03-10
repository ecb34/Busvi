<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServicesUser extends Model
{
    public $timestamps = false;

    protected $table = 'services_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'service_id', 'user_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];


    public function service()
    {
        return $this->hasOne('App\Service', 'id', 'service_id');
    }

    public function crew()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
