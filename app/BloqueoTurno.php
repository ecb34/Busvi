<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BloqueoTurno extends Model
{
    protected $table = 'bloqueos_turnos';
    public $timestamps = false;

    protected $fillable = [
        'id', 'company_id', 'turno_id', 'fecha'
    ];

    protected $hidden = [];
    protected $cast = [];

    public function company(){
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function turno(){
        return $this->belongsTo('App\Turno', 'turno_id');
    }

}
