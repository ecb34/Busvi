<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    public $timestamps = false;

    protected $fillable = [
        'id', 'turno_id', 'user_id', 'fecha', 'plazas', 'nombre', 'telefono', 'confirmado', 'anulado'
    ];

    protected $hidden = [];
    protected $cast = [];

    public function turno(){
        return $this->belongsTo('App\Turno', 'turno_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function chat(){
        return $this->hasMany('App\Chat', 'reserva_id');
    }

}
