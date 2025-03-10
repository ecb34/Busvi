<?php

namespace App\Helpers;

class Calendario {
 
    public $horarios;
    public $bloqueos;
    public $eventos;

    public $disponibles;
    public $posibles;

    private $service;
    private $crew;
    private $day;
    
    public function __construct($service, $crew, $day, $editando = null){

        $this->horarios = [];
        $this->bloqueos = [];
        $this->eventos = [];

        $this->disponibles = [];
        $this->posibles = [];

        if(
            is_null($service) 
            || is_null($service->company) 
            || $service->min <= 0
            || is_null($crew) 
            || is_null($crew->company) 
            || $service->company->id != $crew->company->id
            || $day < strtotime('2018-01-01')
        ){
            return;
        }

        $this->service = $service;
        $this->crew = $crew;
        $this->day = $day;
        
        // calculo de horarios iniciales

        $horarios = json_decode($service->company->schedule, true);
        $d = $this->letra(date('w', $day));

        if(!is_null($horarios['horario_ini1'][$d]) && !is_null($horarios['horario_fin1'][$d])){
            $inicio = strtotime(date('Y-m-d '.$horarios['horario_ini1'][$d].':00', $day));
            $fin = strtotime(date('Y-m-d '.$horarios['horario_fin1'][$d].':00', $day));
            $this->horarios[] = new Intervalo($inicio, $fin);
        }
        
        if(!is_null($horarios['horario_ini2'][$d]) && !is_null($horarios['horario_fin2'][$d])){
            $inicio = strtotime(date('Y-m-d '.$horarios['horario_ini2'][$d].':00', $day));
            $fin = strtotime(date('Y-m-d '.$horarios['horario_fin2'][$d].':00', $day));
            $this->horarios[] = new Intervalo($inicio, $fin);
        }

        // calculo de bloqueos

        $day_blocks = \App\EventUserBlocked::where('user_id', $crew->id)
            ->where(function ($query) use ($day) {
                $query->where(function ($q) use ($day) {
                    $q->where('all_day', 0)
                        ->where('start_date', '>=', date('Y-m-d 00:00:00', $day))
                        ->where('end_date', '<=', date('Y-m-d 23:59:59', $day));
                })
                ->orWhere(function ($q) use ($day) {
                    $q->where('all_day', 1)
                        ->where('start_date', 'LIKE', date('Y-m-d', $day) . ' %');
                });
            })
            ->get();

        foreach($day_blocks as $bloqueo){
            if($bloqueo->all_day){
                $this->bloqueos[] = new Intervalo(strtotime(date('Y-m-d 00:00:00', strtotime($bloqueo->start_date))), strtotime(date('Y-m-d 23:59:59', strtotime($bloqueo->start_date))));
            } else {
                $this->bloqueos[] = new Intervalo(strtotime(date('Y-m-d H:i:00', strtotime($bloqueo->start_date))), strtotime(date('Y-m-d H:i:00', strtotime($bloqueo->end_date))));
            }
        }

        // calculo de eventos actuales

        $day_events = \App\Event::where('user_id', $crew->id)
            ->whereBetween('start_date', [date('Y-m-d 00:00:00', $day), date('Y-m-d 23:59:59', $day)])
            ->get();

        foreach($day_events as $evento){
            if(is_null($editando) || $editando->id != $evento->id){
                $this->eventos[] = new Intervalo(strtotime($evento->start_date), strtotime($evento->end_date));
            }
        }

        // calculo de huecos disponibles

        foreach($this->horarios as $horario){
            $this->disponibles[] = $horario->copia();
        }

        foreach($this->bloqueos as $bloqueo){

            $_intervalos = [];

            foreach($this->disponibles as $intervalo){
                $resultado_resta = $intervalo->restar($bloqueo, true);
                foreach($resultado_resta as $_intervalo){
                    $_intervalos[] = $_intervalo;
                }
            }

            $this->disponibles = [];
            foreach($_intervalos as $intervalo){
                $this->disponibles[] = $intervalo->copia();
            }

        }

        foreach($this->eventos as $evento){

            $_intervalos = [];

            foreach($this->disponibles as $intervalo){
                $resultado_resta = $intervalo->restar($evento, true);
                foreach($resultado_resta as $_intervalo){
                    $_intervalos[] = $_intervalo;
                }
            }

            $this->disponibles = [];
            foreach($_intervalos as $intervalo){
                $this->disponibles[] = $intervalo->copia();
            }

        }

        // calculo de horarios posibles para el servicio

        foreach($this->disponibles as $intervalo){

            $inicio = $intervalo->inicio;

            do {
                $fin = $inicio + ($service->min * 60);
                if($fin <= $intervalo->fin){
                    $this->posibles[] = new Intervalo($inicio, $fin);
                }
                $inicio += (5 * 60);
            } while($fin < $intervalo->fin);

        }

    }

    public function getMoments(){

        $moments = [];
            
        foreach($this->posibles as $intervalo){
            $moments[] = [
                'time' => date('Y-m-d H:i:s', $intervalo->inicio),
                'disabled' => false,
            ];
        }

        return $moments;

    }

    public static function letra($dia_semana = null){

        if(is_null($dia_semana)){
            $dia_semana = intval(date('w'));
        }

        switch ($dia_semana){
            case 0: return 'd';
            case 1: return 'l';
            case 2: return 'm';
            case 3: return 'x';
            case 4: return 'j';
            case 5: return 'v';
            case 6: return 's';
        }

        return new \Exception('Dia inexistente');
        
    }


}