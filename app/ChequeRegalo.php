<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ChequeRegalo extends Model
{
    protected $table = 'cheques_regalo';

    protected $fillable = [
        'uuid', 'company_id', 'from_user_id', 'to_user_id', 'importe', 'status','used_at', 'email', 'nombre', 'pagado_a_comercio', 'a_pagar_al_negocio'
    ];

    protected $hidden = [];

    protected $casts = [
        'importe' => 'float',
        'used_at' => 'datetime',
    ];

    
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
            $model->a_pagar_al_negocio = $model->importe - (($model->importe * optional(Comision::where('nombre', 'cheques_regalo')->first())->porcentaje)/100);
        });
        self::updating(function ($model) {
            $model->a_pagar_al_negocio = $model->importe - (($model->importe * optional(Comision::where('nombre', 'cheques_regalo')->first())->porcentaje)/100);
        });    

    }

    public function company(){
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function emisor(){
        return $this->belongsTo('App\User', 'from_user_id');
    }

    public function destinatario(){
        return $this->belongsTo('App\User', 'to_user_id');
    }

    public  static function AcceptedCompanies(){
        return \App\Company::where('accept_cheque_regalo', 1);
    }

}
