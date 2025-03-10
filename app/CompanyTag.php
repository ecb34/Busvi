<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyTag extends Model
{
    public $timestamps = false;

    protected $table = 'company_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'company_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    public function company()
    {
        return $this->belongsTo('App\Company', 'id', 'company_id');
    }
}
