<?php

namespace App\Helpers;

class Intervalo {
 
    public $inicio;
    public $inicio_str;

    public $fin;
    public $fin_str;
    
    public function __construct($inicio, $fin, $calcular_fin = false){
        
        $this->inicio = $inicio;
        if(!$calcular_fin){
            $this->fin = $fin;
        } else {
            $this->fin = $this->inicio + ($fin * 60);
        }

        if($this->inicio >= $this->fin){
            throw new \Exception('Intervalo no valido');
        }

        $this->inicio_str = date('Y-m-d H:i:s', $this->inicio);
        $this->fin_str = date('Y-m-d H:i:s', $this->fin);

    }

    public function copia(){
        return new Intervalo($this->inicio, $this->fin);
    }

    public function restar($intervalo, $forzar_array_salida = false){

        // caso 1:
        // no hay solapamiento el intervalo acaba antes de que empiece el actual

        if($intervalo->fin <= $this->inicio){
            if($forzar_array_salida){
                return [$this];
            } else {
                return $this;
            }
        }

        // caso 2:
        // no hay solapamiento el intervalo empieza despues de que acabe el actual
        
        if($this->fin <= $intervalo->inicio){
            if($forzar_array_salida){
                return [$this];
            } else {
                return $this;
            }
        }

        // caso 3:
        // solapamiento total, el intervalo empieza antes y termina despues que el actual

        if($intervalo->inicio <= $this->inicio && $this->fin <= $intervalo->fin){
            return [];
        }

        // caso 4:
        // solapamiento parcial al inicio, el intervalo empieza antes que el inicio actual y termina antes que termine el actual

        if($intervalo->inicio <= $this->inicio && $intervalo->fin < $this->fin){
            $salida = new Intervalo($intervalo->fin, $this->fin);
            if($forzar_array_salida){
                return [$salida];
            } else {
                return $salida;
            }
        }

        // caso 5:
        // solapamiento parcial al final, el intervalo empieza despues que inicie el actual y termina despues del fin actual

        if($intervalo->inicio > $this->inicio && $intervalo->fin >= $this->fin){
            $salida = new Intervalo($this->inicio, $intervalo->inicio);
            if($forzar_array_salida){
                return [$salida];
            } else {
                return $salida;
            }
        }
        
        // caso 6:
        // solapamiento intermedio, el intervalo empieza despues de este y termina antes del final de este
        if($intervalo->inicio > $this->inicio && $intervalo->fin < $this->fin){
            $salida_1 = new Intervalo($this->inicio, $intervalo->inicio);
            $salida_2 = new Intervalo($intervalo->fin, $this->fin);
            return [$salida_1, $salida_2];
        }

        dd($this, $intervalo);

        // todos los casos cubiertos, pero por si acaso...
        throw new \Exception('Operación no valida');

    }

}
