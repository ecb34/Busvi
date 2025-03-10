<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turnos';
    public $timestamps = false;

    protected $fillable = [
        'id', 'company_id', 'nombre', 'inicio', 'fin', 'descripcion',  'plazas',  'lunes',  'martes',  'miercoles',  'jueves',  'viernes',  'sabado',  'domingo', 'fecha_inicio', 'fecha_fin'
    ];

    protected $hidden = [];
    protected $cast = [];

    public function company(){
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function bloqueos(){
        return $this->hasMany('App\BloqueoTurno', 'turno_id');
    }

    public function reset_cache($timestamp){
        cache()->forget('bloqueos_'.$this->company_id.'_'.$timestamp);
        cache()->forget('reservas_'.$this->company_id.'_'.$timestamp);
    }

    public function fecha_disponible($timestamp, $plazas = null){

        // comprobar si el turno esta activo para ese dia

        switch(intval(date('w', $timestamp))){
            case 0: // domingo
                if(!$this->domingo) return false;
            break;
            case 1: // lunes
                if(!$this->lunes) return false;
            break;
            case 2: // martes
                if(!$this->martes) return false;
            break;
            case 3: // miercoles
                if(!$this->miercoles) return false;
            break;
            case 4: // jueves
                if(!$this->jueves) return false;
            break;
            case 5: // viernes
                if(!$this->viernes) return false;
            break;
            case 6: // sabado
                if(!$this->sabado) return false;
            break;
        }

        // comprobamos si hay limitaciones por fechas de inicio y fin

        if(!is_null($this->fecha_inicio)){
            if($timestamp < strtotime($this->fecha_inicio)){
                return false;
            }
        }

        if(!is_null($this->fecha_fin)){
            if($timestamp > strtotime($this->fecha_fin)){
                return false;
            }
        }

        // si se trata de hoy, comprobar que el turno no ha terminado
        
        $hoy = strtotime(date('Y-m-d'));
        if($timestamp == $hoy){
            $ahora = date('H:i:s');
            if($ahora >= $this->fin){
                return false;
            }
        }

        // comprobamos si hay bloqueos

        $company_id = $this->company_id;
        $bloqueos = cache()->remember('bloqueos_'.$this->company_id.'_'.$timestamp, 15, function() use ($timestamp, $company_id){
            return \App\BloqueoTurno::where('company_id', $company_id)->where('fecha', date('Y-m-d', $timestamp))->get();
        });

        if(count($bloqueos) > 0){

            // comprobar si hay algún bloqueo a nivel de empresa ese dia (null) o para ese turno en concreto

            foreach($bloqueos as $bloqueo){
                if(is_null($bloqueo->turno_id) || $bloqueo->turno_id == $this->id){
                    return false;
                }
            }

        }

        // comprobar si hay plazas disponibles
        // $plazas_reservadas = \App\Reserva::where('turno_id', $this->id)->where('fecha', date('Y-m-d', $timestamp))->where('anulado', 0)->sum('plazas');

        $plazas_reservadas = 0;

        $reservas = cache()->remember('reservas_'.$company_id.'_'.$timestamp, 15, function() use ($timestamp, $company_id){
            return \App\Reserva::whereHas('turno', function($q) use ($company_id){ return $q->where('company_id', $company_id); })->where('fecha', date('Y-m-d', $timestamp))->where('anulado', 0)->get();
        });
        
        foreach($reservas as $reserva){
            if($reserva->turno_id == $this->id){
                $plazas_reservadas += $reserva->plazas;
            }
        }

        if(!is_null($plazas)){
            if($plazas_reservadas + $plazas > $this->plazas){
                return false;
            }
        }

        $plazas_disponibles = max(0, $this->plazas - $plazas_reservadas);

        return $plazas_disponibles;
    
    }

}
