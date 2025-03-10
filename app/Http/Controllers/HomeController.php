<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use Auth;
use Validator;
use Carbon\Carbon;
use Mail;
use Illuminate\Mail\Mailable;
use App\Mail\UserMail;
use App\Mail\ContactMail;
use App\Mail\CompanyMail;
use File;

use App\Post;
use App\Company;
use App\Sector;
use App\Evento;
use App\User;
use App\Crew;
use App\Event;
use App\CompanyTag;
use App\Reserva;
use App\ChequeRegalo;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->menu = Post::where('order', '>=', 0)->where('public', 1)->orderBy('order', 'ASC')->get();
        view()->share('menu', $this->menu);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $slider_inicio = Post::where('slug', 'slider-inicio')->with(['media' => function($q){
            $q->orderBy('custom_properties->order', 'asc');
        }])->first();

        $slider_servicios = Post::where('slug', 'slider-servicios')->with(['media' => function($q){
            $q->orderBy('custom_properties->order', 'asc');
        }])->first();
     
        $companies = Company::payed()->get();
        $eventos = Evento::where('validado', 1)->where('desde', '>=', Carbon::now())->get();
        $n_eventos = Evento::where('validado', 1)->count();
        $recently = Company::payed()->orderBy('id', 'desc')->limit(5)->get();
        $sectors = Sector::all()->pluck('name', 'id');
        $crews = User::where('role', 'crew')->orWhere('role', 'admin')->count();
        $events = Event::all()->count();
        $customers = User::where('role', 'user')->count();
        $reservas = Reserva::count();

        return view('public.index', [
            'companies' => $companies,
            'eventos' => $eventos,
            'n_eventos' => $n_eventos,
            'recently' => $recently,
            'sectors' => $sectors,
            'crews' => $crews,
            'events' => $events,
            'customers' => $customers,
            'reservas' => $reservas,
            'slider_inicio' => $slider_inicio,
            'slider_servicios' => $slider_servicios,
        ]);
    }

    /**
     * Show the post.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->first();
        if(is_null($post)){
            return redirect()->to('/');
        }
        return view('public.post', ['post' => $post]);
    }

    public function contacto()
    {
        $post = Post::where('slug', 'contacto')->first();
        return view('public.contacto', ['post' => $post]);
    }

    public function sendContactForm(Request $request)
    {
    	$this->validate($request, [
  			'name' => 'required',
  			'email' => 'required|email',
  			'mensaje' => 'required',
  			'g-recaptcha-response' => 'required',
  		]);

  		$query = http_build_query([
  			'secret' => config('app.recaptcha_secret'),
  			'response' => $request->get('g-recaptcha-response')
  		]);

    	$options = [
    		'http' => [
    			'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .  "Content-Length: " . strlen($query) . "\r\n",
    			'method' => 'POST',
    			'content' => $query
    		]
    	];

    	$verify = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create($options));

    	$captcha_success = json_decode($verify);

    	if ($captcha_success->success)
    	{
    		Mail::to('info@busvi.com')->send(new ContactMail($request));
    		\Session::put('success', 'Envío realizado con éxito. Muchas gracias!');
    	}
    	else
    	{
    		\Session::put('error', 'Vaya... parece que eres un robot...');
    	}

    	return redirect()->back();
    }

    /**
     * Obtenien la geolocalización.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLocation(Request $request)
    {
        session(['address_location' => $request->address]);

        return redirect()->back(); // ->json(['data' => $data]);
    }

    /**
     * Elimina la session de geolocalización.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeAddressSession(Request $request)
    {
        $request->session()->forget('address_location');

        return session('address_location');
    }

    /**
     * Show the company.
     *
     * @return \Illuminate\Http\Response
     */
    public function company($id)
    {
        $company = Company::find($id);
        $schedule = json_decode($company->schedule);
        $gallery = $this->getGallery($id);

        $company->addVisit();

        return view('public.company', ['company' => $company, 'schedule' => $schedule, 'gallery' => $gallery]);
    }

     /**
     * Show the company.
     *
     * @return \Illuminate\Http\Response
     */
    public function evento($id){        
        return view('public.eventos.show', ['evento' => Evento::find($id)]);
    }

    /**
     * Show list of companies.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $sector = null)
    {      
        if ($sector)
        {
            $companies = Company::payed()->where('sector_id', $sector)->get();
        }
        else
        {
            $companies = Company::payed()->get();
        }

        $distance = [];

        foreach ($companies as $company)
        {
            // la del de citaplus = AIzaSyDK6_kLLK6UqgyRYITpvHP6H8ErrwOX9tg
            // https://maps.googleapis.com/maps/api/distancematrix/json?origins=Castell%C3%B3n&destinations=39.8896017584119,-0.0754226398&key=AIzaSyBzYvZptWlwHwQWwaXg0SrZz3zNzy_37-0
            $address_map = $address . ', ' . $city . ', ' . $cp;
            
            $data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . urlencode($address_map) . "&destinations=39.8896017584119,-0.0754226398&key=AIzaSyB4y_g7YyNlF_V2N5PFn4qzPa85Z0XswYw");

            $json = json_decode($data, true);

            $km = (string)str_replace(' km', '', $json['rows'][0]['elements'][0]['distance']['text']);

            $distance[$km] = $company;
        }

        $sorted_companies = $this->quicksort($distance);

        $paginated = $this->arrayPaginator($sorted_companies, $request);

        return view('public.list', ['companies' => $paginated]);
    }


    public function listEventos(Request $request)
    {
        
        $eventos = Evento::where('validado', 1)->get();
        

        $distance = [];

        foreach ($eventos as $evento){
            // la del de citaplus = AIzaSyDK6_kLLK6UqgyRYITpvHP6H8ErrwOX9tg
            // https://maps.googleapis.com/maps/api/distancematrix/json?origins=Castell%C3%B3n&destinations=39.8896017584119,-0.0754226398&key=AIzaSyBzYvZptWlwHwQWwaXg0SrZz3zNzy_37-0
            $address_map = $address . ', ' . $city . ', ' . $cp;
            
            $data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . urlencode($address_map) . "&destinations=39.8896017584119,-0.0754226398&key=AIzaSyB4y_g7YyNlF_V2N5PFn4qzPa85Z0XswYw");

            $json = json_decode($data, true);

            $km = (string)str_replace(' km', '', $json['rows'][0]['elements'][0]['distance']['text']);

            $distance[$km] = $company;
        }

        $sorted_eventos = $this->quicksort($distance);

        $paginated = $this->arrayPaginator($sorted_eventos, $request);
        return view('public.eventos.list', ['eventos' => $paginated]);
    }



    /**
     * Accedemos a la zona de elegir el tipo de registro
     *
     * @return \Illuminate\Http\Response
     */
    public function select_register_type(Request $request)
    {
        return view('public.select_register');
    }

    /**
     * Registro de usuario tipo cliente
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        return view('public.register');
    }

    /**
     * Registro de usuario tipo empresa
     *
     * @return \Illuminate\Http\Response
     */
    public function register_company(Request $request)
    {
        $sectors = Sector::all()->pluck('name', 'id')->toArray();
        $provinces = $this->provinces();

        return view('public.register_company', ['sectors' => $sectors, 'provinces' => $provinces]);
    }

    /**
     * Login especial desde Home
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $message = 'No se ha podido iniciar sesión. Compruebe que los datos son correctos.';
        $m_status = 'warning';

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            $message = 'Has iniciado sesión con éxito.';
            $m_status = 'success';
        }
        elseif (Auth::attempt(['username' => $request->email, 'password' => $request->password]))
        {
            $message = 'Has iniciado sesión con éxito.';
            $m_status = 'success';
        }

        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Registramos ususario del tipo que sea
     *
     * @return \Illuminate\Http\Response
     */
    public function make_register(Request $request)
    {

        $this->validate($request, [
            'g-recaptcha-response' => 'required',
        ]);

        $query = http_build_query([
            'secret' => config('app.recaptcha_secret'),
            'response' => $request->get('g-recaptcha-response')
        ]);

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n" . "Content-Length: " . strlen($query) . "\r\n",
                'method' => 'POST',
                'content' => $query
            ]
        ];

        $verify = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create($options));

        $captcha_success = json_decode($verify);
        if(is_null($captcha_success) || !$captcha_success->success)
        {
            \Session::put('error', 'Por favor, verifica que no eres un robot');
            return redirect()->back();
        }

        if ($request->type == 'user')
        {
            return $this->user_register($request);
        }
        else
        {
            $controller = new \App\Http\Controllers\Admin\CompaniesController();

            return $controller->store($request);
        }

    }

    private function user_register(Request $request)
    {
        $message = 'Usuario creado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|unique:users',
                'username' => 'required|unique:users',
                'password' => 'required|min:5',
                'password_confirmation' => 'required|same:password',
            ]);

            if ($validator->fails())
            {
                $request->session()->flash('status', 'validation_error');

                // $error = 'Error. ';

                // if ($validator->errors()->first('email'))
                // {
                //     $error .= $validator->errors()->first('email');
                // }
                // elseif ($validator->errors()->first('password'))
                // {
                //     $error .= $validator->errors()->first('password');
                // }
                // elseif ($validator->errors()->first('username'))
                // {
                //     $error .= $validator->errors()->first('username');
                // }

                return redirect()->back()->withErrors($validator);
            }

            $user = new User();

            $user->name     = $request->name;
            $user->surname  = $request->surname;
            $user->username = $request->username;
            $user->address  = $request->address;
            $user->birthday = Carbon::parse($request->birthday)->format('Y-m-d');
            $user->genere   = $request->genere;
            $user->email    = $request->email;
            $user->role     = $request->type;
            $user->phone    = $request->phone;
            $user->cp       = $request->cp;
            $user->password = bcrypt($request->password);

            if ($user->save())
            {
                $cheques_pendientes = ChequeRegalo::where('email', $user->email)->whereNull('to_user_id')->update(['to_user_id' => $user->id]);
               
                Auth::login($user);

                Mail::to($user->email)->send(new UserMail($user));
            }
            else
            {
                $error = true; 
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating User', ['error' => $e]);
         //   dd($e);
        }
        catch (\Exception $e)
        {
            $error = true;

            \Log::info('Updating User', ['error' => $e]);
           // dd($e);
        }

        if ($error)
        {
            $message = 'Error al crear el usuario.';
            $m_status = 'error';
        	
        	return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        return redirect()->route('users.edit', $user)->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Buscamos empresas por etiqueta
     *
     * @return \Illuminate\Http\Response
     */
    public function tags(Request $request)
    {
        $distance = [];

        $term = $request->tags;
        $companies = Company::payed()
                            ->where(function($query) use ($term) {
                                $query->where('name','like' ,  '%'.$term . '%')
                                      ->orWhere('name_comercial','like', '%' . $term . '%')
                                      ->orWhere('name_comercial','like', '%' . $term . '%')
                                      ->orWhere('description','like', '%' . $term . '%')
                                      ->orWhereHas('services' , function ($query) use ($term) {
                                            $query->where('name','like', '%' . $term . '%');
                                      })
                                      ->orWhereHas('tags' , function ($query) use ($term) {
                                            $query->where('name','like', '%' . $term . '%');
                                      })
                                      ->orWhereHas('sector'  , function ($query) use ($term) {
                                            $query->where('name','like', '%' . $term . '%');
                                      });
                            })
                            ->get();

        $address = session('address_location') ? session('address_location') : 'Valencia, España';
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false&key=AIzaSyB4y_g7YyNlF_V2N5PFn4qzPa85Z0XswYw');
        $output = json_decode($geocode);
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

        $paginated = $this->companiesByDistance($companies, $latitude, $longitude);

        return view('public.list', ['companies' => $paginated]);
    }

    public function eventtags(Request $request)
    {
        $distance = [];

        $terms= explode(' ', trim($request->eventtags));
        $eventos = Evento::where('validado',1)->where('desde', '>=', Carbon::now())->with('company','organizador','categoria')
                            ->where(function($query) use ($terms) {
                                foreach ($terms as $term) {
                                   $query->where(function ($q) use ($term){
                                            $q->where('nombre','like' ,  '%'.$term . '%')
                                            ->orWhere('descripcion', 'like', '%'.$term.'%')
                                            ->orWhereHas('company' , function ($query) use ($term) {
                                                    $query->where('name','like', '%' . $term . '%');
                                              })
                                            ->orWhereHas('organizador' , function ($query) use ($term) {
                                                    $query->where('name','like', '%' . $term . '%')
                                                            ->orWhere('surname','like', '%' . $term . '%');
                                              })
                                            ->orWhereHas('categoria' , function ($query) use ($term) {
                                                    $query->where('nombre','like', '%' . $term . '%');
                                            });
                                   });
                                    
                                }
                            })->get();    
                                
           

        $address = session('address_location') ? session('address_location') : 'Valencia, España';
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false&key=AIzaSyB4y_g7YyNlF_V2N5PFn4qzPa85Z0XswYw');
        $output = json_decode($geocode);
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

        $paginated = $this->eventsByDistance($eventos, $latitude, $longitude);

        return view('public.eventos.list', ['eventos' => $paginated]);
    }

    

    private function companiesByDistance($companies, $latitudeFrom, $longitudeFrom)
    {
        if ($companies->isEmpty())
        {
            return [];
        }
        $distance = [];
        foreach ($companies as $company)
        {
            if ($company->lat && $company->long)
            {
                $earthRadius = 6371000;

                $latFrom = deg2rad($latitudeFrom);
                $lonFrom = deg2rad($longitudeFrom);
                $latTo = deg2rad($company->lat);
                $lonTo = deg2rad($company->long);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

                $km = ($angle * $earthRadius) / 1000;
                $km = round($km, 2);
                
                $company->setAttribute('km', $km);

                $distance[] = ['km' => $km, 'company' => $company];
            }
            else{
              $distance[] = ['km' => 'Sin datos', 'company' => $company]; 
            }
        }

        $sorted_distances = $sorted_companies = [];

        if ($distance)
        {
            $sorted_distances = $this->quicksort($distance);
        }

        foreach ($sorted_distances as $element)
        {
            $sorted_companies[] = $element['company'];
        }

        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $perPage = isset($this->params['perPage']) ? $this->params['perPage'] : 100;

        return $this->arrayPaginator($sorted_companies, $page, $perPage);
    }

    private function eventsByDistance($eventos, $latitudeFrom, $longitudeFrom)
    {
        if ($eventos->isEmpty())
        {
            return [];
        }
        $distance = [];
        $sorted_eventos = [];
        foreach ($eventos as $evento)
        {
            if ($evento->lat && $evento->long)
            {
                $earthRadius = 6371000;

                $latFrom = deg2rad($latitudeFrom);
                $lonFrom = deg2rad($longitudeFrom);
                $latTo = deg2rad($evento->lat);
                $lonTo = deg2rad($evento->long);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

                $km = ($angle * $earthRadius) / 1000;
                $km = round($km, 2);
                
                $evento->setAttribute('km', $km);

                $distance[] = ['km' => $km, 'evento' => $evento];
            }
            else{
              $distance[] = ['km' => 'Sin datos', 'evento' => $evento]; 
            }
        }

        $sorted_distances = $sorted_companies = [];

        if ($distance)
        {
            $sorted_distances = $this->quicksort($distance);
        }

        foreach ($sorted_distances as $element)
        {
            $sorted_eventos[] = $element['evento'];
        }

        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $perPage = isset($this->params['perPage']) ? $this->params['perPage'] : 100;
        return $this->arrayPaginator($sorted_eventos, $page, $perPage);
    }

    /**
     * Devuelve un array ordenado por sus claves --RECURSIVO--
     *
     * @return \Illuminate\Http\Response
     */
    private function quicksort($array)
    {
    	// Creamos arrays para la para los valores mayores y menores
        $left = $right = [];

        // Si el array tiene menos de 2 elementos
        // directamente devolvemos el array
        if (count($array) < 2)
        {
            return $array;
        }

        // Ordenamos el array.
        // Para ello primero cogemos el primer elemento
        $pivot_key = key($array);
        $pivot = array_shift($array);

        // Metemos cada valor en el array que le corresponde
        for ($i = 0; $i < count($array); $i++)
        {
            if ($array[$i]['km'] <= $pivot['km'])
            {
                $left[] = $array[$i];
            }
            elseif ($array[$i]['km'] > $pivot['km'])
            {
                $right[] = $array[$i];
            }
        }

        // Creamos el array a devolver, que es la suma de los tres arrays:
        // el de menores (al que se le vuelve a aplicar esta función) +
        // el elemento que acabamos de coger como pivote +
        // el de mayore (al que también aplicamos esta función)
        $to_return = array_merge($this->quicksort($left), [$pivot_key => $pivot], $this->quicksort($right));

        return $to_return;
    }

    private function arrayPaginator($array, $page = 1, $perPage = 100)
    {
        $offset = ($page * $perPage) - $perPage;

        $paginator = new LengthAwarePaginator(
            array_slice($array, $offset, $perPage, true), // Sólo recojemos lo que queremos
            count($array), // Total de items
            $perPage, // Items por página
            $page // Página actual
        );

        return $paginator;
    }

    /**
     * Devuelve un array con los nombres de las imagenes de la galería del concurso.
     *
     * @param  int  id
     * @return array
     */
    private function getGallery($id)
    {
        $path = public_path() . '/img/companies/galleries/' . $id . '/original/';
        $toReturn = [];

        // comprobamos si lo que nos pasan es un direcotrio
        if(\File::isDirectory($path))
        {
            $files = File::allFiles($path);

            foreach ($files as $file)
            {
                $toReturn[] = $file->getFileName();
            }
        }

        return $toReturn;
    }

    private function provinces()
    {
        return [
                'Álava' => 'Álava',
                'Albacete' => 'Albacete',
                'Alicante' => 'Alicante',
                'Almería' => 'Almería',
                'Asturias' => 'Asturias',
                'Ávila' => 'Ávila',
                'Badajoz' => 'Badajoz',
                'Barcelona' => 'Barcelona',
                'Burgos' => 'Burgos',
                'Cáceres' => 'Cáceres',
                'Cádiz' => 'Cádiz',
                'Cantabria' => 'Cantabria',
                'Castellón' => 'Castellón',
                'Ciudad Real' => 'Ciudad Real',
                'Córdoba' => 'Córdoba',
                'Cuenca' => 'Cuenca',
                'Gerona' => 'Gerona',
                'Granada' => 'Granada',
                'Guadalajara' => 'Guadalajara',
                'Guipúzcoa' => 'Guipúzcoa',
                'Huelva' => 'Huelva',
                'Huesca' => 'Huesca',
                'Islas Baleares' => 'Islas Baleares',
                'Jaén' => 'Jaén',
                'La Coruña' => 'La Coruña',
                'La Rioja' => 'La Rioja',
                'Las Palmas' => 'Las Palmas',
                'León' => 'León',
                'Lérida' => 'Lérida',
                'Lugo' => 'Lugo',
                'Madrid' => 'Madrid',
                'Málaga' => 'Málaga',
                'Murcia' => 'Murcia',
                'Navarra' => 'Navarra',
                'Orense' => 'Orense',
                'Palencia' => 'Palencia',
                'Pontevedra' => 'Pontevedra',
                'Salamanca' => 'Salamanca',
                'Santa Cruz de Tenerife' => 'Santa Cruz de Tenerife',
                'Segovia' => 'Segovia',
                'Sevilla' => 'Sevilla',
                'Soria' => 'Soria',
                'Tarragona' => 'Tarragona',
                'Teruel' => 'Teruel',
                'Toledo' => 'Toledo',
                'Valencia' => 'Valencia',
                'Valladolid' => 'Valladolid',
                'Vizcaya' => 'Vizcaya',
                'Zamora' => 'Zamora',
                'Zaragoza' => 'Zaragoza'];
    }
}
