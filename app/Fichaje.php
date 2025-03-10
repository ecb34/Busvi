<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fichaje extends Model {
    
    public $timestamps = false;
    protected $table = 'fichajes';
    protected $fillable = ['user_id', 'inicio', 'fin', 'posicion_inicio', 'posicion_fin'];

    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
