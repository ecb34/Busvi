<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public $timestamps = false;

    protected $table = 'services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name'
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


    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function all_companies()
    {
        return $this->belongsToMany('App\Company', 'id', 'company_id')->distinct();
    }

    public function users()
    {
        return $this->belongsToMany('App\User','services_users');
    }
}
