<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comision extends Model
{
    public $timestamps = false;

    protected $table = 'comisiones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'nombre', 'porecentaje'
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



   
}
