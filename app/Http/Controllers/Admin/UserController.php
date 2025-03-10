<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use Validator;
use Carbon\Carbon;
use Intervention\Image\ImageManager as InterImage;
use Auth;
use Mail;
use App\Mail\UserMail;
use Laravel\Cashier\Billable;

use App\User;
use App\Company;
use App\Event;
use App\Favourite;
use App\FavouriteCrew;

class UserController extends Controller
{
    use Billable;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	if (Auth::user()->role == 'admin')
    	{
    		return redirect()->route('crew.index');
    	}
    	elseif (Auth::user()->role == 'user')
    	{
    		return redirect()->route('users.edit', ['id' => Auth::user()->id]);
    	}

        $title = 'Administradores';
        session()->forget('type_user');

        $users = new User();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        $users = $users->where('role', 'operator');
        if(Auth::user()->role == 'superadmin'){
            $users = $users->orWhere('role', 'superadmin');
        }
        $users = $users->get();

        $array_datas = ['name', 'username', 'email', 'phone', 'role', 'empresa', 'id'];
        $array_titles = [
                            trans('app.common.name'),
                            trans('app.common.username'), 
                            trans('app.common.email'),
                            trans('app.common.phone'),
                            trans('app.common.role'),
                            trans('app.common.company'),
                            ''
                        ];

        $datatable = $dt->datatable(
                                    'datatable_users', $users, $array_datas, 'edit', 'users', $buscadores, 'admin.users.datatable.datatable', $array_titles
                                );

        $script = $dt->script('datatable_users', $buscadores);

        return view('admin.users.index', [
                                            'title' => $title,
                                            'datatable' => $datatable,
                                            'script' => $script,
                                            'search' => $buscadores
                                         ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customers()
    {
    	if (Auth::user()->role == 'admin')
    	{
    		return redirect()->route('crew.index');
    	}
    	elseif (Auth::user()->role == 'user')
    	{
    		return redirect()->route('users.edit', ['id' => Auth::user()->id]);
    	}

        $title = trans('app.common.users');
        session(['type_user' => 1]);

        $users = new User();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        $users = $users->where('role', 'user')->get();
        $array_datas = ['name', 'username', 'email', 'phone', 'id'];
        $array_titles = [
                            trans('app.common.name'),
                            trans('app.common.username'), 
                            trans('app.common.email'),
                            trans('app.common.phone'),
                            ''
                        ];

        $datatable = $dt->datatable(
                                    'datatable_users', $users, $array_datas, 'edit', 'users', $buscadores, 'admin.users.datatable.datatable', $array_titles
                                );

        $script = $dt->script('datatable_users', $buscadores);

        return view('admin.users.index', [
                                            'title' => $title,
                                            'datatable' => $datatable,
                                            'script' => $script,
                                            'search' => $buscadores
                                         ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	if (Auth::user()->role == 'admin')
    	{
    		return redirect()->route('crew.index');
    	}
    	elseif (Auth::user()->role == 'user')
    	{
    		return redirect()->route('users.edit', ['id' => Auth::user()->id]);
    	}

        $back = url()->previous();
        return view('admin.users.create', ['url' => $back]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = trans('app.admin.users.user_create_success');
        $m_status = 'success';
        $error = false;
        $return = 'users.index';

        try
        {
            $role = ($request->type_user) ? $request->type_user : $request->roles;

            if ($role == 'user')
            {
                $return = 'users.customers';
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required|unique:users',
                'username' => 'required|unique:users',
                'password' => 'required|min:5',
                'password_confirmation' => 'required|same:password',
            ]);

            if ($validator->fails())
            {
                $message = '';

                if ($validator->errors()->get('email'))
                {
                    $message .= $validator->errors()->get('email')[0];
                }
                elseif ($validator->errors()->get('password'))
                {

                    $message .= $validator->errors()->get('password')[0];
                }
                elseif ($validator->errors()->get('password_confirmation'))
                {
                    $message .= $validator->errors()->get('password_confirmation')[0];
                }
                elseif ($validator->errors()->get('username'))
                {
                    $message .= $validator->errors()->get('username')[0];
                }

                $m_status = 'error';
                $error = true;

                $request->session()->flash('status', 'validation_error');

                return redirect()->back()->with(['message' => $message, 'm_status' => $m_status, 'back' => $return])->withInput($request->all());
            }

            $user = new User();

            $user->name     = $request->name;
            $user->surname  = $request->surname;
            $user->username = $request->username;
            $user->address  = $request->address;
            $user->city     = $request->city;
            $user->cp       = $request->cp;
            $user->genere   = $request->genere;
            $user->email    = $request->email;
            $user->phone    = $request->phone;
            $user->role     = $role;
            $user->birthday = Carbon::parse($request->birthday)->format('Y-m-d');
            $user->password = bcrypt($request->password);

            $user->save();

            // Si tiene imagen la guardamos
            if ($request->hasFile('img') && $request->file('img')->isValid())
            {
                $error = $this->storeUserImg($request, $user);
            }

            Mail::to($user->email)->send(new UserMail($user));
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating User', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Updating User', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = trans('app.admin.users.user_create_error');
            $m_status = 'error';
        }

        return redirect()->route($return)->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Almacena el logo de la empresa.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function storeUserImg(Request $request, User $user)
    {
        try
        {
            $file_name = $this->getFileName($request->file('img'), $user->id);

            $image_resize = new InterImage;
            $image_resize = $image_resize->make($request->file('img')->getRealPath());
            $image_resize->fit(400, 400);

            if ($image_resize->filesize() > 4000000)
            {
                return -1;
            }

            if ($image_resize->save(public_path() . '/img/user/' . $file_name))
            {
                \File::delete(public_path() . '/img/user/' . $user->img);

                $user->img = $file_name;
            }

            $user->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Storing User Image', ['error' => $e]);

            return 1;
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing User Image', ['error' => $e]);

            return 1;
        }

        return 0;
    }

    /**
     * Obtiene el nombre de un archivo aunque sea muy largo.
     *
     * @param  file  $file
     * @return collection
     */
    private function getFileName($file)
    {
        return uniqid() . '.png';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Para que ningún usuario de tipo "user" o "admin"
        // pueda entrar en el perfil de ningún otro usuario.
        if ((Auth::user()->role == 'user' || Auth::user()->role == 'admin') && Auth::user()->id != $id)
        {
            return redirect()->back();
        }

        $user = User::find($id);

        if($user->role == 'superadmin' && \Auth::user()->role != 'superadmin'){
            abort(404);
        }

        $role_show = true;

        if ($user->role != 'admin' && $user->role != 'superadmin')
        {
            $role_show = false;
        }

        $datatable_events = $this->datatableEvents($id);

        $datatable_companies = NULL;

        if (Auth::user()->role == 'user')
        {
            $datatable_companies = $this->datatableCompanies($id);
        }

        return view('admin.users.edit',  [
                                            'datatable_ev'  => $datatable_events['datatable'],
                                            'datatable'     => $datatable_companies['datatable'],
                                            'script_ev'     => $datatable_events['script'],
                                            'script'        => $datatable_companies['script'],
                                            'search'        => NULL,
                                            'user'          => $user,
                                            'role_show'     => $role_show,
                                            'birth'         => Carbon::parse($user->birthday)->format('d-m-Y')
                                        ]);
    }

    private function datatableEvents($id = null)
    {
        $events = new Event();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        if (!$id && (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator'))
        {
            $events = $events->all();
        }
        elseif (Auth::user()->role == 'user' || $id)
        {
            $ident = ($id) ? $id : Auth::user()->id;

            $events = $events->where('customer_id', $ident)->get();
        }
        else
        {
            $events = Auth::user()->company->events;
        }

        $array_datas = ['service_id', 'customer_id', 'user_id', 'user_id', 'start_date', 'end_date', 'id'];
        $array_titles = [
                            trans('app.common.service'),
                            trans('app.common.customer'), 
                            trans('app.common.crew'),
                            trans('app.common.company'),
                            trans('app.common.init'),
                            trans('app.common.end'),
                            ''
                        ];

        $datatable = $dt->datatable(
                                    'datatable_events', $events, $array_datas, 'edit', 'calendar', $buscadores, 'admin.calendar.datatable.datatable', $array_titles
                                );

        $script = $dt->script('datatable_events', $buscadores);

        return ['datatable' => $datatable, 'script' => $script, 'search' => $buscadores];
    }

    private function datatableCompanies($id = null)
    {
        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        if (! $id)
        {
            $companies = Company::all();
        }
        else
        {
            $companies = Favourite::where('user_id', $id)->get();
        }

        $array_datas = ['name', 'sector_id'];
        $array_titles = [
                            trans('app.common.company'),
                            trans('app.common.sector')
                        ];

        $datatable = $dt->datatable(
                                    'datatable_companies', $companies, $array_datas, 'show', 'companies', $buscadores, 'admin.users.datatable.datatable_fav', $array_titles
                                );

        $script = $dt->script('datatable_companies', $buscadores);

        return ['datatable' => $datatable, 'script' => $script];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = trans('app.admin.users.user_update_success');
        $m_status = 'success';
        $error = false;

        try
        {
            $user = User::find($id);

            $user->name     = $request->name;
            $user->surname  = $request->surname;
            $user->username = $request->username;
            $user->address  = $request->address;
            $user->city     = $request->city;
            $user->cp       = $request->cp;
            $user->birthday = Carbon::parse($request->birthday);
            $user->genere   = $request->genere;
            $user->phone    = $request->phone;
            $user->bank_count = !is_null($request->bank_count) ? $request->bank_count : '';

            if ($user->email != $request->email)
            {
                $validatedData = $request->validate([
                    'email' => 'required|unique:users'
                ]);

                $user->email = $request->email;
            }

            // Si tiene imagen la guardamos
            if ($request->hasFile('img') && $request->file('img')->isValid())
            {
                $error = $this->storeUserImg($request, $user);
            }
            else
            {
                $user->save();
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating User', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Updating User', ['error' => $e]);
            dd('hola mundo2');
        }

        if ($error)
        {
            $message = trans('app.admin.users.user_update_error');
            $m_status = 'error';
        }

        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxUpdatePass(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $user = User::find($request->id);

                // $validatedData = $request->validate([
                //     'password' => 'required|min:5',
                //     'password_confirmation' => 'required|same:password',
                // ]);

                $validator = Validator::make($request->all(), [
                    'pass' => 'required|min:5',
                    'repass' => 'required|same:pass',
                ]);

                if ($validator->fails())
                {
                    return $request->all();
                }

                $user->password = bcrypt($request->pass);

                $user->save();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Updating User Password', ['error' => $e]);

                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Updating User Password', ['error' => $e]);

                return 0;
            }
        }

        return 1;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = trans('app.admin.users.user_remove_success');
        $m_status = 'success';
        $error = false;

        try
        {
            $user = User::find($id);

            if(\Auth::user()->role == 'operator' && $user->role == 'superadmin'){
                $error = 'notIsSuperadmin';
            } 
            elseif ($user->role == 'admin')
            {
                $error = 'isAdmin';
            }
            else
            {
                dd('delete');
                \File::delete(public_path() . '/img/crew/' . $user->img);
                $user->delete();
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Deleting User', ['error' => $e]);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Deleting User', ['error' => $e]);
        }

        if ($error)
        {
            $message = trans('app.admin.users.user_remove_error');

            if ($error == 'isAdmin' ){
                $message = trans('app.admin.users.user_no_able_remove');
            } elseif($error == 'notIsSuperadmin'){
                $message = trans('app.admin.users.user_no_able_remove_superadmin');
            }

            $m_status = 'error';
        }

        return redirect()->route('users.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $user = User::find($request->id);

                if ($user->role == 'admin')
                {
                    return -1;
                }
                else
                {
                    \File::delete(public_path() . '/img/crew/' . $user->img);

                    $user->delete();
                }
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting User', ['error' => $e]);

                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting User', ['error' => $e]);

                return 0;
            }
        }

        return 1;
    }

    public function setFavoriteCrew(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $favorite = FavouriteCrew::where('crew_id', $request->id)
                                     ->where('user_id', Auth::user()->id)
                                     ->first();

                if ($favorite)
                {
                    $favorite->delete();
                }
                else
                {
                    $favorite = new FavouriteCrew();
                    $favorite->user_id = Auth::user()->id;
                    $favorite->crew_id = $request->id;

                    $favorite->save();
                }
                
                return 1;
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Favouriting Crew', ['error' => $e]);
            }
            catch (Exception $e)
            {
                \Log::info('Favouriting Crew', ['error' => $e]);
            }
        }

        return 0;
    }

    function excel(){
        
        if(\Auth::user()->role != 'superadmin'){
            abort(404);
        }
        
        return \Excel::download(new \App\Exports\ClientsExport, 'usuarios.xlsx');

    }

}
