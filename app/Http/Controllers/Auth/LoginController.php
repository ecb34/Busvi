<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

     /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL) ? $this->username() : 'username';

        return [
            $field => $request->get($this->username()),
            'password' => $request->password,
        ];
    }

    // PARA SABER DE DÓNDE SALE QUE LO DE ABAJO FUNCIONE
    // 
    // El siguiente método hace que, dependiendo del rol de usuario,
    // éste, al loggearse, sea redirigido hacía una parte o a otra.
    // 
    // Existe un método llamado authenticated en el trait AuthenticatesUsers
    // Este método está vacío (para que cada uno ponga lo que quiera o sepa
    // que existe).
    // 
    // Es llamado desde la función sendLoginResponse del trait AuthenticatesUsers.
    // Y se indica que si lo que devuelve "authenticated" es vacío, redirija a
    // dónde tiene marcado (en nuestro caso la variable de $redirectTo de más
    // arriba).
    // 
    //          Fran.
    //          
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     *
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->role == 'user')
        {
            return redirect()->route('home.index');
        }

        return redirect()->route('home'); 
    }
}
