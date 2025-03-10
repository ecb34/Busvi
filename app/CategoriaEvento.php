<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaEvento extends Model
{
    public $timestamps = false;

    protected $table = 'categorias_evento';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'nombre'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    public function eventos()
    {
        return $this->hasMany('App\Evento', 'id', 'categoria_evento_id');
    }
}
