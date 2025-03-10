<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Laravel\Cashier\Billable;
use Coderity\Wallet\Billable;
// use Spatie\Permission\Traits\HasRoles;

use Auth;

class User extends Authenticatable
{
    use Billable;
    use Notifiable;
    // use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'address', 'genere', 'username','email', 'phone', 'password', 'role', 'company_id', 'birthday', 'description','bank_count', 'trial_ends_at', 'card_last_four', 'card_brand', 'stripe_id','api_token','dni','visible','cp'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'company_id', 'description','bank_count', 'trial_ends_at', 'card_last_four', 'card_brand', 'stripe_id','api_token'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'username' => 'string',
        'email' => 'string',
        'password' => 'string',
        'remember_token' => 'string',
        'birthday' => 'date'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'username' => 'required',
        'email' => 'required|unique:users',
        'password' => 'sometimes|nullable|confirmed|min:6',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];



    public function services()
    {
        return $this->hasMany('App\ServicesUser', 'user_id', 'id');
    }

    public function favourites()
    {
        return $this->hasMany('App\Favourite', 'user_id', 'id');
    }

    public function my_favourites()
    {
        return $this->hasMany('App\FavouriteCrew', 'user_id', 'id');
    }

    public function isFavourite()
    {
        return $this->hasOne('App\FavouriteCrew', 'crew_id', 'id')->where('user_id', Auth::user()->id);
    }

    public function count_favorites()
    {
        return $this->hasMany('App\FavouriteCrew', 'crew_id', 'id')
                    ->where('crew_id', $this->id)
                    ->count();
    }
    
    public function serviceUsers()
    {
        return $this->belongsToMany('App\Service','services_users');
    }

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }

    public function blockedEvents()
    {
        return $this->hasOne('App\EventUserBlocked', 'user_id', 'id');
    }

    public function eventsUser()
    {
        return $this->hasMany('App\Event', 'user_id', 'id');
    }

    public function getTokens()
    {
        return $this->hasMany('App\PushToken', 'user_id', 'id'); 
    }

    public function generateAPIToken()
    {
        do
        {
            $this->api_token = str_random(60);
        } while (User::where('api_token', $this->api_token)->first());

        $this->save();
    }

    public function fichajes()
    {
        return $this->hasMany('App\Fichaje', 'user_id', 'id');
    }

    public function chequesRegaloEmitidos()
    {
        return $this->hasMany('App\ChequeRegalo', 'from_user_id', 'id');
    }

    public function chequesRegaloRecibidos()
    {
        return $this->hasMany('App\ChequeRegalo', 'to_user_id', 'id');
    }

    public function eventos(){
        return $this->belongsToMany('App\Evento', 'cliente_evento', 'cliente_id', 'evento_id')->withPivot('pagado','precio' ,'confirmacion_asistencia', 'plazas_reservadas', 'ultimo_uso_entrada', 'uuid')->withTimestamps();
    }


}
