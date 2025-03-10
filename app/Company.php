<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Company extends Model
{
    public $timestamps = false;

    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'name', 'phone', 'address', 'city', 'cp', 'cif', 'email', 'password', 'logo', 'schedule', 'blocked', 'payed', 'type', 'enable_events', 'sector_id', 'lat', 'long', 'bank_count', 'web','accept_cheque_regalo','accept_eventos'
    ];


    protected $cast =[
        'accept_cheque_regalo' => 'boolean',
        'accept_eventos' => 'boolean',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @v$servicesar array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];

    protected $appends = array('offer');

    public function getOfferAttribute(){
        return $this->hasOffer();  
    }

    public function admin()
    {
        return $this->hasOne('App\User', 'company_id', 'id')->where('role', 'admin');
    }

    public function crew()
    {
        return $this->hasMany('App\User', 'company_id', 'id')
                    ->where(function ($query) {
                        $query->where('role', 'crew')
                              ->orWhere('role', 'admin');
                    });
    }

    public function crewVisible()
    {
        return $this->hasMany('App\User', 'company_id', 'id')
                    ->where(function ($query) {
                        $query->where('role', 'crew')
                              ->orWhere('role', 'admin');
                    });
    }

    public function services()
    {
        return $this->hasMany('App\Service', 'company_id', 'id')->orderBy('order', 'asc')->orderBy('name', 'asc');
    }

    public function tags()
    {
        return $this->hasMany('App\CompanyTag', 'company_id', 'id');
    }

    public function gallery()
    {
        return $this->hasMany('App\Gallery', 'company_id', 'id')->orderBy('order', 'asc');
    }

    public function hasOffer(){
        foreach($this->gallery as $image){
            if($image->offer){
                return true;
            }
        }
        return false;
    }

    public function sector()
    {
        return $this->hasOne('App\Sector', 'id', 'sector_id');
    }

    public function events()
    {
        return $this->hasManyThrough('App\Event', 'App\User', 'company_id', 'user_id', 'id');
    }

    public function future_events()
    {
        return $this->events()->where('start_date', '>=', date('Y-m-d H:i:s'))->get();
    }

    public function reservas(){
        return $this->hasManyThrough('App\Reserva', 'App\Turno', 'company_id', 'turno_id', 'id');
    }

    public function isFavourite()
    {
        return $this->hasOne('App\Favourite', 'company_id', 'id')->where('user_id', Auth::user()->id);
    }

    public function favourite()
    {
        return $this->hasOne('App\Favourite', 'company_id', 'id');
    }

    public function count_favorites()
    {
        return $this->hasMany('App\Favourite', 'company_id', 'id')
                    ->where('company_id', $this->id)
                    ->count();
    }

    public function addVisit()
    {
        ++$this->visits;

        $this->save();
    }

    public static function payed()
    {
        return self::where('payed', 1)
                   ->where('blocked', 0);
    }

    public function add_image($filename){
        $image = Gallery::create([
            'company_id' => $this->id,
            'filename' => $filename,
            'order' => Gallery::next($this->id),
        ]);
        $image->reorder();
        return $image;
    }

    public function reordenar_galeria(){
        if(count($this->gallery) > 0){
            $this->gallery[0]->reorder();
        }
    }

    public function regenerar_galeria(){

        $path = public_path() . '/img/companies/galleries/' . $this->id . '/original/';
        if(\File::isDirectory($path))
        {
            $files = \File::allFiles($path);
            foreach ($files as $file)
            {

                $filename = $file->getFileName();
                if(is_null(Gallery::where('company_id', $this->id)->where('filename', $filename)->first())){

                    $gallery = Gallery::create([
                        'company_id' => $this->id,
                        'filename' => $filename,
                        'order' => Gallery::next($this->id),
                    ]);

                }

            }
        }

    }

    public function open_now(){
        
        $horario = json_decode($this->schedule, true);
        if(is_null($horario) || !is_array($horario)){
            return false;
        }

        $dia_semana = \App\Helpers\Calendario::letra(null);

        $inicio_1 = isset($horario['horario_ini1'][$dia_semana]) ? $horario['horario_ini1'][$dia_semana] : null;
        $fin_1 = isset($horario['horario_fin1'][$dia_semana]) ? $horario['horario_fin1'][$dia_semana] : null;

        $inicio_2 = isset($horario['horario_ini2'][$dia_semana]) ? $horario['horario_ini2'][$dia_semana] : null;
        $fin_2 = isset($horario['horario_fin2'][$dia_semana]) ? $horario['horario_fin2'][$dia_semana] : null;

        $ahora = intval(date('Hi'));

        if(!is_null($inicio_1) && !is_null($fin_1)){

            $inicio_1 = intval(str_replace(':', '', $inicio_1));
            $fin_1 = intval(str_replace(':', '', $fin_1));
                
            if($inicio_1 <= $ahora && $fin_1 >= $ahora){
                return true;
            }
            
        }

        if(!is_null($inicio_2) && !is_null($fin_2)){

            $inicio_2 = intval(str_replace(':', '', $inicio_2));
            $fin_2 = intval(str_replace(':', '', $fin_2));
                
            if($inicio_2 <= $ahora && $fin_2 >= $ahora){
                return true;
            }
            
        }

        return false;

    }

    public function turnos(){
        return $this->hasMany('App\Turno', 'company_id')->orderBy('inicio', 'asc');
    }

    public function bloqueos_turnos(){
        return $this->hasMany('App\BloqueoTurno', 'company_id');
    }

}
