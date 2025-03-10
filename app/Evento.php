<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp;
use \Exception\GuzzleException;
use GuzzleHttp\Client;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use App\Comision;
use Ramsey\Uuid\Uuid;



class Evento extends Model implements HasMedia
{
    use HasMediaTrait;
    
    protected $table = 'eventos';

    protected $fillable = [
        'precio','precio_final' ,'categoria_evento_id', 'organizador_id', 'company_id', 'direccion', 'long','lat', 'desde', 'hasta', 'validado','pagado_a_comercio', 'nombre', 'poblacion', 'descripcion','aforo_maximo', 'uuid'
    ];

    protected $hidden = [];

    protected $casts = [
        'precio' => 'float',
        'precio_final' => 'float',
        'desde' => 'datetime',
        'hasta' => 'datetime',
        'validado' => 'boolean',
        'pagado_a_comercio' => 'boolean',
        'descripcion' => 'text',
        'aforo_maximo' => 'integer',
    ];

    protected $appends = [
        'plazas_libres','es_editable', 'imagen', 'n_asistentes'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if(!$model->direccion && $model->company_id){ // SI no trae direccion y trae local cojo la del local
                $model->direccion = $model->company->address;
                $model->poblacion = $model->company->city;
                $model->lat = $model->company->lat;
                $model->long = $model->company->long;
                $model->uuid = (string) Uuid::uuid4();
            }
            $model->precio_final = $model->precio + (($model->precio * optional(Comision::where('nombre', 'eventos')->first())->porcentaje)/100);
            

        });
        self::updating(function ($model) {
            if(!$model->direccion && $model->company_id){ // SI no trae direccion y trae local cojo la del local
                $model->direccion = $model->company->address;
                $model->poblacion = $model->company->city;
                $model->lat = $model->company->lat;
                $model->long = $model->company->long;
            }
            $model->precio_final = $model->precio + (($model->precio * optional(Comision::where('nombre', 'eventos')->first())->porcentaje)/100);
            
        });
    }

    public function company(){
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function organizador(){
        return $this->belongsTo('App\User', 'organizador_id');
    }


    public function categoria(){
        return $this->belongsTo('App\CategoriaEvento', 'categoria_evento_id');
    }

    public  static function AcceptedCompanies(){
        return \App\Company::where('accept_eventos', 1);
    }

    public function asistentes(){
        return $this->belongsToMany('App\User', 'cliente_evento', 'evento_id', 'cliente_id')->withPivot('pagado','precio' ,'confirmacion_asistencia', 'plazas_reservadas', 'ultimo_uso_entrada', 'uuid')->withTimestamps();
    }

    public function getPlazasLibresAttribute(){
        return $this->aforo_maximo ? $this->aforo_maximo - $this->asistentes()->where('pagado', 1)->sum('plazas_reservadas') : 999999;
    }

    public function getEsEditableAttribute(){
        return $this->asistentes()->count() <= 0;
    }

    public function getImagenAttribute(){
        $url = $this->getFirstMediaUrl();
        return $url ? asset($url) : asset('img/busvi_logo.jpg');
    }
     public function getNAsistentesAttribute(){
        return $this->asistentes()->where('pagado', 1)->sum('plazas_reservadas').' / '.$this->aforo_maximo ;
    }

}
