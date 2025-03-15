<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

use Validator;
use Carbon\Carbon;
use Auth;
use File;
use Intervention\Image\ImageManager as InterImage;
use Mail;
use App\Mail\UserMail;
use App\Mail\CompanyMail;
use App\Mail\GetDownCompanyMail;

use App\Company;
use App\User;
use App\Rate;
use App\Service;
use App\Sector;
use App\ServicesUser;
use App\CompaniesUsers;
use App\Event;
use App\Favourite;
use App\CompanyTag;
use App\Gallery;
use Calendar;

class CompaniesController extends Controller
{
    private $erro_csv = false;

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
    	if (Auth::user()->role == 'user')
    	{
    		return redirect()->route('users.edit', ['id' => Auth::user()->id]);
    	}

        $companies = new Company();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        $companies 	  = $companies->all();
        $array_datas  = ['name', 'name_comercial', 'visits', 'phone', 'email', 'sector_id', 'payed', 'type', 'id'];
        $array_titles = ['Nombre', 'Nombre Comercial', 'Visitas', 'Teléfono', 'email', 'sector', 'Pagos', 'Tipo', ''];

        $datatable = $dt->datatable(
                                    'datatable_companies', $companies, $array_datas, 'edit',
                'companies', $buscadores, 'admin.companies.datatable.datatable', $array_titles
                                );

        $script = $dt->script('datatable_companies', $buscadores);

        return view('admin.companies.index', ['datatable' => $datatable, 'script' => $script, 'search' => $buscadores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	if (Auth::user()->role == 'user')
    	{
    		return redirect()->route('users.edit', ['id' => Auth::user()->id]);
    	}

    	$sectors = Sector::all()->pluck('name', 'id')->toArray();
        $disabled = NULL;
        $provinces = $this->provinces();
        $types_company = ['No Pagado', 'Basic', 'Premium - Citas No Activas', 'Premium - Citas Activas'];

        if (! $sectors)
        {
            $disabled = 'disabled';
        }

        return view('admin.companies.create', [
                                                'sectors'       => $sectors,
                                                'disabled'      => $disabled,
                                                'provinces'     => $provinces,
                                                'types_company' => $types_company
                                              ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message  = 'Negocio creado con éxito.';
        $m_status = 'success';
        $error    = $this->createCompany($request);

        if ($error != 1)
        {
            $message = 'Error al crear el negocio.';
            $m_status = 'error';

            if ($error == -1)
            {
                $message = 'Negocio creado. Error al subir la imagen. Puede que sea demasiado grande.';
                $m_status = 'warning';
            }

            return redirect()->back()
                             ->with(['message' => $message, 'm_status' => $m_status])->withInput($request->all());;
        }

        if ($request->type)
        {
            if ($error == 0)
            {
                $message = 'Error al crear el negocio. Puede que el mail o el username esté en uso o que la contraseña no sea de más de 5 carácteres e iguales en la original y la repetición.';

                return redirect()->back()
                                 ->with(['message' => $message, 'm_status' => $m_status])->withInput($request->all());;
            }
        }

        return redirect()->route('companies.index')
                         ->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Crea la empresa.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function createCompany(Request $request)
    {
        try
        {
            $types = $this->getTypeCompany($request->type);

            // Creamos la empresa
            $company = new Company();

            $company->name              = $request->name;
            $company->name_comercial    = $request->name_comercial;
            $company->phone             = $request->phone;
            $company->phone2            = $request->phone2;
            $company->address           = $request->address;
            $company->city              = $request->city;
            $company->province          = $request->province;
            $company->cp                = $request->cp;
            $company->cif               = $request->cif;
            $company->bank_count        = ($request->bank_count) ? $request->bank_count : null;
            $company->schedule          = $this->getSchedule($request);
            $company->sector_id         = $request->sector_id;
            $company->description       = $request->description;
            $company->type              = $types['type'];
            $company->enable_events     = $types['enable_events'];
            $company->payed             = $types['payed'];
            $company->web               = $request->web;

            $latLong = $this->getLatLong($request->address, $request->city, $request->cp);

            $company->lat   = $latLong['lat'];
            $company->long  = $latLong['long'];

            $company->save();

            // Guardamos el admin de la empresa
            if (! $this->createAdmin($request, $company))
            {
                // Si no podemos crear el administrador
                // eliminamos la empresa creada
                $aux = Company::find($company->id);
                $aux->forceDelete();

                return 0;
            }

            Mail::to($company->admin->email)->bcc('info@busvi.com')->send(new CompanyMail($company));

            // Si tiene imagen la guardamos
            if ($request->hasFile('logo') && $request->file('logo')->isValid())
            {
                return $this->storeCompanyLogo($request, $company);
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Storing Company', ['error' => $e]);

            return 0;
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing Company', ['error' => $e]);

            return 0;
        }

        return 1;
    }

    private function getTypeCompany($type)
    {
        switch ($type)
        {
            case 0:
                $type = 0;
                $enable_events = 0;
                $payed = 0;

                break;

            case 1:
                $type = 0;
                $enable_events = 0;
                $payed = 1;
                
                break;
            
            case 2:
                $type = 1;
                $enable_events = 0;
                $payed = 1;
                
                break;
            
            case 3:
                $type = 1;
                $enable_events = 1;
                $payed = 1;
                
                break;
        }

        return ['type' => $type, 'enable_events' => $enable_events, 'payed' => $payed];
    }

    /**
     * Obtenemos los horarios de toda la semana.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function getSchedule(Request $request)
    {
        $toReturn = [
                        'horario_ini1' => $request->horario_ini1,
                        'horario_fin1' => $request->horario_fin1,
                        'horario_ini2' => $request->horario_ini2,
                        'horario_fin2' => $request->horario_fin2,
                    ];

        return json_encode($toReturn);
    }

    /**
     * Obtenemos la latitud y la longitud a partir de la dirección.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function getLatLong($address, $city, $cp)
    {
        // dd('https://maps.google.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false&key=AIzaSyBzYvZptWlwHwQWwaXg0SrZz3zNzy_37-0');
        $address_map = $address . ',
                ' . $city . ',
                ' . $cp;
        
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . urlencode($address_map) . '&sensor=false&key=AIzaSyB4y_g7YyNlF_V2N5PFn4qzPa85Z0XswYw');

        $lat = 0;
        $long = 0;

        $output = json_decode($geocode);

        if (count($output->results) != 0)
        {
            $lat = $output->results[0]->geometry->location->lat;
            $long = $output->results[0]->geometry->location->lng;
        }

        return ['lat' => $lat, 'long' => $long];
    }

    /**
     * Crea el administrador de la empresa.
     *
     * @param  Request  $request
     * @return
     */
    private function createAdmin(Request $request, $company)
    {
    	try
    	{
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:5',
                'password_confirmation' => 'required|same:password',
            ]);

            if ($validator->fails())
            {
            	$request->session()->flash('status', 'validation_error');

                return 0;
            }

    		$admin = new User();

    		$admin->role 		= 'admin';
            $admin->name        = $request->user_name;
            $admin->surname     = $request->user_surname;
			$admin->username 	= $request->email;
            $admin->email       = $request->email;
            $admin->phone       = $request->phone;
			$admin->password 	= bcrypt($request->password);
            $admin->company_id  = $company->id;

			$admin->save();

            if (! $this->saveUserServices($request, $admin))
            {
                return 0;
            }

            // Si el registro se hace desde la parte pública lleva la variable "type"
            // Sino, no la lleva. Así que si la lleva hacemos el login del usuario.
            if (! Auth::check() && $request->type)
            {
                Auth::login($admin);
            }

            Mail::to($admin->email)->send(new UserMail($admin));

			return $admin;
    	}
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Storing Admin Company', ['error' => $e]);

            return 0;
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing Admin Company', ['error' => $e]);

            return 0;
        }
    }

    public function subscribe($id, Request $request)
    {
        $plan = $request->plan;

        $stripe_token = $request->token;

        $user->newSubscription('main', $plan)
             // ->trialDays(60)
             ->create($stripe_token);
    }

    /**
     * Almacena el logo de la empresa.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function storeCompanyLogo(Request $request, Company $company)
    {
    	try
    	{
    		$file_name = $this->getFileName($request->file('logo'), $company->id);

            $image_resize = new InterImage;

            $image_resize = $image_resize->make($request->file('logo')->getRealPath());

            if ($image_resize->filesize() > 4000000)
            {
                return -1;
            }

            $image_resize->fit(400, 400);

            if ($image_resize->save(public_path() . '/img/companies/' . $file_name))
            {
                \File::delete(public_path() . '/img/companies/' . $company->logo);

                $company->logo = $file_name;
            }

            $company->save();
    	}
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Storing Company Logo', ['error' => $e]);

            return -1;
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing Company Logo', ['error' => $e]);

            return -1;
        }

        return 1;
    }

    /**
     * Almacena los servicios del usuario.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function saveUserServices($request, $user)
    {
        try
        {
            $new_services_users = $request->services;
            $actual_services_users = ServicesUser::where('user_id', $user->id)->get();

            foreach ($actual_services_users as $services_user)
            {
                $services_user->forceDelete();
            }

            if ($new_services_users)
            {
                foreach ($new_services_users as $services_user)
                {
                    $new_services_user = new ServicesUser();
                    $new_services_user->user_id = $user->id;
                    $new_services_user->service_id = $services_user;

                    $new_services_user->save();
                }
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Storing User Services', ['error' => $e]);

            return 0;
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing User Services', ['error' => $e]);

            return 0;
        }

        return 1;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::find($id);
        $schedule = json_decode($company->schedule);
        $images = $this->getGallery($id);

        return view('admin.companies.show', [
                                                'company'  => $company,
                                                'schedule' => $schedule,
                                                'images'   => $images
                                            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	if (Auth::user()->role == 'user')
    	{
    		return redirect()->route('users.edit', ['id' => Auth::user()->id]);
    	}

    	$auth_company = NULL;

    	if (Auth::user()->company)
    	{
    		$auth_company = Auth::user()->company->id;
    	}
        
        $company = Company::find($id);

        if (Auth::user()->role == 'admin' && $company->id != $auth_company)
        {
        	return redirect()->route('companies.edit', Auth::user()->company->id);
        }

        $services = $company->services;
        $sectors  = Sector::all()->pluck('name',
                'id')->toArray();
        $disabled = NULL;
        $schedule = json_decode($company->schedule);

        $company_tags = $company->tags;
        $tags = [];

        if ($company_tags)
        {
            foreach ($company_tags as $tag)
            {
                $tags[] = $tag->name;
            }
        }

        $tags = implode(',', $tags);

        if (($services->isEmpty()) || (! $sectors))
        {
            $disabled = 'disabled';
        }

        $images = $this->getGallery($id, true);
    
        $switch_enabled = (! $company->payed || ! $company->type) ? 'disabled' : '';
        $checked_events = $company->enable_events ? 'checked' : '';
        $checked_fichajes = $company->enable_fichajes ? 'checked' : '';
        $checked_reservas = $company->enable_reservas ? 'checked' : '';
        $checked_accept_cheque_regalo = $company->accept_cheque_regalo ? 'checked' : '';
        $checked_accept_eventos = $company->accept_eventos ? 'checked' : '';
        $val_types_company = $this->getTypesCompany($company);

        $types_company = [-1 => 'No Pagado', 0 => 'Basic', 1 => 'Premium']; // 'Premium - Citas No Activas', 'Premium - Citas Activas'];

        return view('admin.companies.edit', [
                                                'company'           => $company,
                                                'services'          => $services,
                                                'sectors'           => $sectors,
                                                'disabled'          => $disabled,
                                                'schedule'          => $schedule,
                                                'tags'              => $tags,
                                                'switch_enabled'    => $switch_enabled,
                                                'images'            => $images,
                                                'checked_events'    => $checked_events,
                                                'checked_fichajes'  => $checked_fichajes,
                                                'checked_reservas'  => $checked_reservas,
                                                'checked_accept_cheque_regalo'  => $checked_accept_cheque_regalo,
                                                'checked_accept_eventos'  => $checked_accept_eventos,
                                                'val_types_company' => $val_types_company,
                                                'types_company'     => $types_company
                                                // 'calendar' => $calendar
                                            ]);
    }

    private function getTypesCompany($company)
    {
        if(!$company->payed){
            return -1;
        } else {
            return $company->type;
        }
    }

    /**
     * Show the form for company's payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payment($id)
    {
    	if (Auth::user()->role == 'user')
    	{
    		return redirect()->route('users.edit', ['id' => Auth::user()->id]);
    	}

        $company = Company::find($id);

        if ((Auth::user()->role != 'operator' || Auth::user()->role != 'superadmin') &&
            ($company->payed && ! $company->blocked))
        {
            return redirect()->route('home');
        }

        // modificación para que por defecto todas las empresas sean basic
        if(!$company->blocked){

            $company->payed = 1;
			$company->type = 0; // basic
			$company->enable_events = 0; // sin eventos

            $company->save();
            
            return redirect()->route('home');
            
        }

        $premium = Rate::where('name', 'Premium')->first();
        $basic   = Rate::where('name', 'Basic')->first();

        return view('admin.companies.payment', [
                                                    'company' => $company, 
                                                    'premium' => $premium, 
                                                    'basic' => $basic
                                               ]);
    }

    /**
     * Show the form for company's payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paymentPremium($id)
    {
    	if (Auth::user()->role == 'user')
    	{
    		return redirect()->route('users.edit', ['id' => Auth::user()->id]);
    	}
    	
        $company = Company::find($id);

        $premium = Rate::where('name', 'Premium')->first();

        session(['type' => 'premium']);

        return view('admin.companies.payment_premium', ['company' => $company, 'premium' => $premium]);
    }

    /**
     * Set Company Blocked.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function block(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $company = Company::find($request->id);

                $company->blocked = $request->val;

                $company->save();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Blocking/DeBlocking Company', ['error' => $e]);

                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('DB ERROR - Blocking/DeBlocking Company', ['error' => $e]);

                return 0;
            }

            return 1;
        }

        return 0;
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
        $message  = 'Negocio modificado con éxito.';
        $m_status = 'success';
        $error    = $this->editCompany($request, $id);

        if ($error != 1)
        {
            $message = 'Error al modificar el negocio.';
            $m_status = 'error';

            if ($error == -1)
            {
                $message = 'Negocio actualizado. Error al subir la imagen. Puede que sea demasiado grande.';
                $m_status = 'warning';
            }
        }

        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Edita la empresa.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function editCompany(Request $request, $id)
    {

        try
    	{
            $company = Company::find($id);

            $company->name              = $request->name;
            $company->name_comercial    = $request->name_comercial;
            $company->phone             = $request->phone;
            $company->phone2            = $request->phone2;
            $company->address           = $request->address;
            $company->city              = $request->city;
            $company->province          = $request->province;
            $company->cp                = $request->cp;
            $company->cif               = $request->cif;
            $company->schedule          = $this->getSchedule($request);
            $company->sector_id         = $request->sector_id;
            $company->bank_count        = $request->bank_count;
            $company->description       = $request->description;
            $company->web               = $request->web;

            if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operador')
            {

                $company->enable_events     = $request->enable_events ? 1 : 0;
                $company->enable_fichajes   = $request->enable_fichajes ? 1 : 0;
                $company->enable_reservas   = $request->enable_reservas ? 1 : 0;
                $company->accept_cheque_regalo   = $request->accept_cheque_regalo ? 1 : 0;
                $company->accept_eventos   = $request->accept_eventos ? 1 : 0;

                //$types = $this->getTypeCompany($request->type);
                if(intval($request->type) == -1){
                    $company->payed = 0;    
                    $company->enable_events = 0;
                    $company->enable_fichajes = 0;
                    $company->enable_reservas = 0;
                } else {
                    $company->payed      = 1;    
                    $company->type       = $request->type;
                }
                
            }

            $latLong = $this->getLatLong($request->address, $request->city, $request->cp);

            $company->lat   = $latLong['lat'];
            $company->long  = $latLong['long'];

            $company->save();

            if ($request->hasFile('logo') && $request->file('logo')->isValid())
            {
                $img_error = $this->storeCompanyLogo($request, $company);
				
                if (! $img_error || $img_error == -1)
				{
					return $img_error;
				}
			}
            if ($request->hasFile('logo') && ! $request->file('logo')->isValid())
            {
                return -1;
            }

            if (! $this->saveTags($request->tags, $company->id))
            {
                return 0;
            }

			return $this->editAdmin($request, $company);
    	}
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Storing Company', ['error' => $e]);

            return 0;
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing Company', ['error' => $e]);

            return 0;
        }
    }

    /**
     * Salva las etiquetas en la tabla correspondiente.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function saveTags($request_tags, $id)
    {
        $request_tags = explode(',', $request_tags);
        $tags = [];

        try
        {
            $toDelete = CompanyTag::where('company_id', $id)->get();

            foreach ($toDelete as $tag)
            {
                $tag->delete();
            }

            for ($i = 0; $i < count($request_tags); $i++)
            {
                $tags[] = $this->transliterateString(strtolower($request_tags[$i]));
            }

            for ($i = 0; $i < count($tags); $i++)
            {
                $tag = new CompanyTag;

                $tag->name = $tags[$i];
                $tag->company_id = $id;

                $tag->save();
            }
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing Tag', ['error' => $e]);
            return 0;
        }

        return 1;
    }

    /**
     * Edita el administrador de la empresa.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function editAdmin(Request $request, Company $company)
    {
    	try
    	{
            $admin = User::find($company->admin->id);

    		$admin->name 		= $request->user_name;
			$admin->username 	= $request->username;
			$admin->email 		= $request->email;

			$admin->save();

            if (! $this->saveUserServices($request, $admin))
            {
                return 0;
            }

			return 1;
    	}
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Storing Admin Company User', ['error' => $e]);

            return 0;
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing Admin Company User', ['error' => $e]);

            return 0;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = 'Negocio eliminado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $company = Company::find($id);

            if ($company->admin->delete())
            {
            	$img = $company->logo;

            	if ($company->forceDelete())
            	{
                	\File::delete(public_path() . '/img/companies/' . $img);

                	$company->admin->forceDelete();
            	}
            	else
            	{
            		$company->admin->restore();
            	}
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Deleting Company', ['error' => $e]);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Deleting Company', ['error' => $e]);
        }

        if ($error)
        {
            $message = 'Error al eliminar la empresa.';
            $m_status = 'error';
        }

        return redirect()->route('companies.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
	            $company = Company::find($request->id);

	            if ($company->admin && $company->admin->delete())
	            {
	            	$img = $company->logo;

	            	if ($company->forceDelete())
	            	{
	                	\File::delete(public_path() . '/img/companies/' . $img);

                		$company->admin->forceDelete();
	            	}
	            	else
	            	{
	            		$company->admin->restore();
	            	}
	            }
                else
                {
                    return 0;
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

    public function getDown(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $company = Company::find($request->id);
                
                $admins = User::where('role', 'operator')
                              ->orWhere('role', 'superadmin')
                              ->get();

                foreach ($admins as $admin)
                {
                    Mail::to($admin->email)->send(new GetDownCompanyMail($company));
                }

                return 1;
            }
            catch (Exception $e)
            {
                return 0;
            }
        }

        return 0;
    }

    /**
     * Obtiene el nombre de un archivo aunque sea muy largo.
     *
     * @param  file  $file
     * @return string 'nombre'
     */
    private function getFileName($file)
    {
        return uniqid() . '.png';
    }

    public function adminFavorites()
    {
        if (Auth::user()->role == 'user')
        {
            return view('admin.companies.find_companies');
        }
        else
        {
            dd('Sólo los usuarios clientes pueden hacer favoritos.');
        }
    }

    public function tags(Request $request)
    {
        $distance = [];
        $term = $request->tags;

        $companies = Company::payed()
                            ->where(function($query) use ($term) {
                                $query->where('name','like' ,  '%' . $term . '%')
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

        return view('admin.companies.list', ['companies' => $paginated]);
    }

    private function companiesByDistance($companies, $latitudeFrom, $longitudeFrom)
    {
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
        $pivot = $array[0];

        // Metemos cada valor en el array que le corresponde
        for ($i = 0; $i < count($array); $i++)
        {
            if ($array[$i]['km'] < $pivot['km'])
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
        $to_return = array_merge($this->quicksort($left), [$pivot], $this->quicksort($right));

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
     * Indica como favorita una empresa o no.
     *
     * @return \Illuminate\Http\Response
     */
    public function setFavourite(Request $request)
    {
        try
        {
            $favorite = Favourite::where('company_id', $request->id)
                                 ->where('user_id', Auth::user()->id)
                                 ->first();

            if ($favorite)
            {
                $favorite->delete();
            }
            else
            {
                $favorite = new Favourite();
                $favorite->user_id = Auth::user()->id;
                $favorite->company_id = $request->id;

                $favorite->save();
            }
            
            return redirect()->back();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Favouriting Company', ['error' => $e]);

            dd($e);
        }
        catch (Exception $e)
        {
            \Log::info('Favouriting Company', ['error' => $e]);

            dd($e);
        }
    }

    /**
     * Indica como favorita una empresa o no.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxFavourite(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $favorite = Favourite::where('company_id', $request->id)
                                     ->where('user_id', Auth::user()->id)
                                     ->first();

                if ($favorite)
                {
                    $favorite->delete();

                    return 0;
                }
                else
                {
                    $favorite = new Favourite();
                    $favorite->user_id = Auth::user()->id;
                    $favorite->company_id = $request->id;

                    $favorite->save();

                    return 1;
                }
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Favouriting Company', ['error' => $e]);

                return -1;
            }
            catch (Exception $e)
            {
                \Log::info('Favouriting Company', ['error' => $e]);

                return -1;
            }
        }
    }

    /**
     * Para importar los tags.
     *
     * @param  Request  $request
     * @return array
     */
    public function importTags($id, Request $request)
    {
        $message = 'Etiquetas importadas.';
        $m_status = 'success';
        $error = false;

        try
        {
            // Comprobamos si cargamos el archivo
            if ($request->hasFile('csv'))
            {
                // Obtenemos la ruta
                $path = $request->file('csv')->getRealPath();
                if(file_exists($path)){

                    $cabecera = true;

                    $file = fopen($path, 'r');
                    while (($line = fgetcsv($file)) !== FALSE) {

                        if($cabecera && (count($line) != 1 || $line[0] != 'name')){
                            
                            \Log::info('CompanyTag importing', ['error' => 'wrong header']);
                            $this->erro_csv = true;
                            $error = true;

                        }

                        if(!$cabecera){

                            $etiqueta = $line[0];
                            if($etiqueta != ''){

                                $tag = CompanyTag::where('name', $etiqueta)
                                             ->where('company_id', $id)
                                             ->get();

                                if ($tag->isEmpty())
                                {
                                    $tag = new CompanyTag;
                                    
                                    $tag->name = $etiqueta;
                                    $tag->company_id = $id;
    
                                    $tag->save();
                                }

                            }

                        }

                        $cabecera = false;

                    }
                    fclose($file);


                } else {

                    \Log::info('CompanyTag importing', ['error' => 'missing file']);
                    $error = true;

                }

                // \Excel::load($path, function($reader) use ($id, $error) {
                //     // Getting all results
                //     $results = $reader->all();

                //     // Leemos cada línea
                //     foreach ($reader->toArray() as $row)
                //     {
                //         if (isset($row['name']))
                //         {
                //             $tag = CompanyTag::where('name', $row['name'])
                //                              ->where('company_id', $id)
                //                              ->get();

                //             if ($tag->isEmpty())
                //             {
                //                 $tag = new CompanyTag;
                                
                //                 $tag->name = $row['name'];
                //                 $tag->company_id = $id;

                //                 $tag->save();
                //             }
                //         }
                //         else
                //         {
                //             $this->erro_csv = true;
                //         }
                //     }
                // });
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - CompanyTag importing', ['error' => $e]);

            $error = true;
        }
        catch (Exception $e)
        {
            \Log::info('CompanyTag importing', ['error' => $e]);

            $error = true;
        }

        if ($error)
        {
            $message = 'Error al importar la etiqueta.';
            $m_status = 'error';
        }
        elseif ($this->erro_csv)
        {
            $message = 'Error al importar la etiqueta. Formato no compatible';
            $m_status = 'error';
        }

        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Gestiona la galería de imágenes de un negocio.
     *
     * @param  int  id
     * @return array
     */
    public function gallery(Request $request, $id)
    {
        $file_name = $this->getFileName($request->file('image'), $id);

        $base_path = public_path() . '/img/companies/galleries/' . $id . '/';

        if( ! \File::isDirectory($base_path) )
        {
            \File::makeDirectory($base_path, 493, true);
        }

        $image_resize = new InterImage;
        $image_resize = $image_resize->make($request->file('image')->getRealPath());

        if ($image_resize->filesize() > 4000000)
        {
            $message = 'La imágen es demasiado grande';
            $m_status = 'error';
            
            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        // Guardamos la imagen en tamaño original
        $path = $base_path . '/original/';

        if( ! \File::isDirectory($path) )
        {
            \File::makeDirectory($path, 493, true);
        }

        $image_resize->save($path . $file_name);

        // Guardamos la imagen en tamaño thumbnail
        $image_resize->fit(300, 300);

        $path = $base_path . '/thumb/';

        if( ! \File::isDirectory($path) )
        {
            \File::makeDirectory($path, 493, true);
        }

        $image_resize->save($path . $file_name);

        $company = Company::find($id);
        if(!is_null($company)){
            $image = $company->add_image($file_name);
        }

        return redirect()->back();
    }

    /**
     * Devuelve un array con los nombres de las imagenes de la galería.
     *
     * @param  int  id
     * @return array
     */
    private function getGallery($id, $return_object = false)
    {

        $company = Company::find($id);
        if(is_null($company)) return [];

        $path = public_path() . '/img/companies/galleries/' . $id . '/original/';
        $toReturn = [];

        // comprobamos si lo que nos pasan es un direcotrio
        if(\File::isDirectory($path))
        {

            foreach($company->gallery as $image){
                if(file_exists($path.$image->filename)){
                    if(!$return_object){
                        $toReturn[$image->id] = $image->filename;
                    } else {
                        $toReturn[$image->id] = $image;
                    }
                }
            }

        }

        return $toReturn;
    }

    public function removeImageGallery(Request $request)
    {

        $company = Company::find($request->company_id);
        if(!is_null($company)){
            foreach($company->gallery as $image){
                if($image->filename == $request->image_name){
                    $image->delete();
                    $image->reorder();
                }
            }
        }

        $base_path = public_path() . '/img/companies/galleries/' . $request->company_id . '/';

        $path = $base_path . '/thumb/' . $request->image_name;
        $this->destroyImageGallery($path);

        $path = $base_path . '/original/' . $request->image_name;
        $this->destroyImageGallery($path);

        return redirect()->back();
    }
    
    private function destroyImageGallery($path)
    {
        if(\File::isFile($path))
        {
            unlink($path);
        }
    }

    public function orderGallery(Request $request){
        
        $gallery = Gallery::find($request->id);
        $company = Company::find($request->company_id);

        if(!is_null($company) && !is_null($gallery)){
            
            switch($request->accion){
                case 'subir-todo':
                    $gallery->order_start();
                break;
                case 'subir':
                    $gallery->order_up();
                break;
                case 'bajar':
                    $gallery->order_down();
                break;
                case 'bajar-todo':
                    $gallery->order_end();
                break;
            }

            return response()->json($company->gallery->toArray(), 200);

        }
        return response()->json([], 500);
    }

    public function offerGallery(Request $request){

        $gallery = Gallery::find($request->id);
        $company = Company::find($request->company_id);

        if(!is_null($company) && !is_null($gallery)){

            $gallery->offer = !$gallery->offer;
            $gallery->save();

            return response()->json($gallery->toArray(), 200);

        }
        return response()->json([], 500);

    }

    public function editGallery(Request $request){

        $gallery = Gallery::find($request->id);
        $company = Company::find($request->company_id);

        if(!is_null($company) && !is_null($gallery)){

            $gallery->description = !is_null($request->description) ? $request->description : '' ;
            if($gallery->save()){
                return redirect()->back();
            }

        }

        return redirect()->back();

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

    private function transliterateString($txt)
    {
        $transliterationTable = ['á' => 'a',
                'Á' => 'A',
                'à' => 'a',
                'À' => 'A',
                'ă' => 'a',
                'Ă' => 'A',
                'â' => 'a',
                'Â' => 'A',
                'å' => 'a',
                'Å' => 'A',
                'ã' => 'a',
                'Ã' => 'A',
                'ą' => 'a',
                'Ą' => 'A',
                'ā' => 'a',
                'Ā' => 'A',
                'ä' => 'ae',
                'Ä' => 'AE',
                'æ' => 'ae',
                'Æ' => 'AE',
                'ḃ' => 'b',
                'Ḃ' => 'B',
                'ć' => 'c',
                'Ć' => 'C',
                'ĉ' => 'c',
                'Ĉ' => 'C',
                'č' => 'c',
                'Č' => 'C',
                'ċ' => 'c',
                'Ċ' => 'C',
                'ç' => 'c',
                'Ç' => 'C',
                'ď' => 'd',
                'Ď' => 'D',
                'ḋ' => 'd',
                'Ḋ' => 'D',
                'đ' => 'd',
                'Đ' => 'D',
                'ð' => 'dh',
                'Ð' => 'Dh',
                'é' => 'e',
                'É' => 'E',
                'è' => 'e',
                'È' => 'E',
                'ĕ' => 'e',
                'Ĕ' => 'E',
                'ê' => 'e',
                'Ê' => 'E',
                'ě' => 'e',
                'Ě' => 'E',
                'ë' => 'e',
                'Ë' => 'E',
                'ė' => 'e',
                'Ė' => 'E',
                'ę' => 'e',
                'Ę' => 'E',
                'ē' => 'e',
                'Ē' => 'E',
                'ḟ' => 'f',
                'Ḟ' => 'F',
                'ƒ' => 'f',
                'Ƒ' => 'F',
                'ğ' => 'g',
                'Ğ' => 'G',
                'ĝ' => 'g',
                'Ĝ' => 'G',
                'ġ' => 'g',
                'Ġ' => 'G',
                'ģ' => 'g',
                'Ģ' => 'G',
                'ĥ' => 'h',
                'Ĥ' => 'H',
                'ħ' => 'h',
                'Ħ' => 'H',
                'í' => 'i',
                'Í' => 'I',
                'ì' => 'i',
                'Ì' => 'I',
                'î' => 'i',
                'Î' => 'I',
                'ï' => 'i',
                'Ï' => 'I',
                'ĩ' => 'i',
                'Ĩ' => 'I',
                'į' => 'i',
                'Į' => 'I',
                'ī' => 'i',
                'Ī' => 'I',
                'ĵ' => 'j',
                'Ĵ' => 'J',
                'ķ' => 'k',
                'Ķ' => 'K',
                'ĺ' => 'l',
                'Ĺ' => 'L',
                'ľ' => 'l',
                'Ľ' => 'L',
                'ļ' => 'l',
                'Ļ' => 'L',
                'ł' => 'l',
                'Ł' => 'L',
                'ṁ' => 'm',
                'Ṁ' => 'M',
                'ń' => 'n',
                'Ń' => 'N',
                'ň' => 'n',
                'Ň' => 'N',
                'ñ' => 'n',
                'Ñ' => 'N',
                'ņ' => 'n',
                'Ņ' => 'N',
                'ó' => 'o',
                'Ó' => 'O',
                'ò' => 'o',
                'Ò' => 'O',
                'ô' => 'o',
                'Ô' => 'O',
                'ő' => 'o',
                'Ő' => 'O',
                'õ' => 'o',
                'Õ' => 'O',
                'ø' => 'oe',
                'Ø' => 'OE',
                'ō' => 'o',
                'Ō' => 'O',
                'ơ' => 'o',
                'Ơ' => 'O',
                'ö' => 'oe',
                'Ö' => 'OE',
                'ṗ' => 'p',
                'Ṗ' => 'P',
                'ŕ' => 'r',
                'Ŕ' => 'R',
                'ř' => 'r',
                'Ř' => 'R',
                'ŗ' => 'r',
                'Ŗ' => 'R',
                'ś' => 's',
                'Ś' => 'S',
                'ŝ' => 's',
                'Ŝ' => 'S',
                'š' => 's',
                'Š' => 'S',
                'ṡ' => 's',
                'Ṡ' => 'S',
                'ş' => 's',
                'Ş' => 'S',
                'ș' => 's',
                'Ș' => 'S',
                'ß' => 'SS',
                'ť' => 't',
                'Ť' => 'T',
                'ṫ' => 't',
                'Ṫ' => 'T',
                'ţ' => 't',
                'Ţ' => 'T',
                'ț' => 't',
                'Ț' => 'T',
                'ŧ' => 't',
                'Ŧ' => 'T',
                'ú' => 'u',
                'Ú' => 'U',
                'ù' => 'u',
                'Ù' => 'U',
                'ŭ' => 'u',
                'Ŭ' => 'U',
                'û' => 'u',
                'Û' => 'U',
                'ů' => 'u',
                'Ů' => 'U',
                'ű' => 'u',
                'Ű' => 'U',
                'ũ' => 'u',
                'Ũ' => 'U',
                'ų' => 'u',
                'Ų' => 'U',
                'ū' => 'u',
                'Ū' => 'U',
                'ư' => 'u',
                'Ư' => 'U',
                'ü' => 'ue',
                'Ü' => 'UE',
                'ẃ' => 'w',
                'Ẃ' => 'W',
                'ẁ' => 'w',
                'Ẁ' => 'W',
                'ŵ' => 'w',
                'Ŵ' => 'W',
                'ẅ' => 'w',
                'Ẅ' => 'W',
                'ý' => 'y',
                'Ý' => 'Y',
                'ỳ' => 'y',
                'Ỳ' => 'Y',
                'ŷ' => 'y',
                'Ŷ' => 'Y',
                'ÿ' => 'y',
                'Ÿ' => 'Y',
                'ź' => 'z',
                'Ź' => 'Z',
                'ž' => 'z',
                'Ž' => 'Z',
                'ż' => 'z',
                'Ż' => 'Z',
                'þ' => 'th',
                'Þ' => 'Th',
                'µ' => 'u',
                'а' => 'a',
                'А' => 'a',
                'б' => 'b',
                'Б' => 'b',
                'в' => 'v',
                'В' => 'v',
                'г' => 'g',
                'Г' => 'g',
                'д' => 'd',
                'Д' => 'd',
                'е' => 'e',
                'Е' => 'E',
                'ё' => 'e',
                'Ё' => 'E',
                'ж' => 'zh',
                'Ж' => 'zh',
                'з' => 'z',
                'З' => 'z',
                'и' => 'i',
                'И' => 'i',
                'й' => 'j',
                'Й' => 'j',
                'к' => 'k',
                'К' => 'k',
                'л' => 'l',
                'Л' => 'l',
                'м' => 'm',
                'М' => 'm',
                'н' => 'n',
                'Н' => 'n',
                'о' => 'o',
                'О' => 'o',
                'п' => 'p',
                'П' => 'p',
                'р' => 'r',
                'Р' => 'r',
                'с' => 's',
                'С' => 's',
                'т' => 't',
                'Т' => 't',
                'у' => 'u',
                'У' => 'u',
                'ф' => 'f',
                'Ф' => 'f',
                'х' => 'h',
                'Х' => 'h',
                'ц' => 'c',
                'Ц' => 'c',
                'ч' => 'ch',
                'Ч' => 'ch',
                'ш' => 'sh',
                'Ш' => 'sh',
                'щ' => 'sch',
                'Щ' => 'sch',
                'ъ' => '',
                'Ъ' => '',
                'ы' => 'y',
                'Ы' => 'y',
                'ь' => '',
                'Ь' => '',
                'э' => 'e',
                'Э' => 'e',
                'ю' => 'ju',
                'Ю' => 'ju',
                'я' => 'ja',
                'Я' => 'ja'];

        return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
    }

    function excel(){
        
        if(\Auth::user()->role != 'superadmin'){
            abort(404);
        }

        return \Excel::download(new \App\Exports\CompaniesExport, 'negocios.xlsx');
        
    }

    public function ajaxShow(Request $request){
        $company = Company::find($request->company_id);
        if(!$company){
            return response()->json(['msg' => 'error_negocio_no_encontrado'], 500);  
        }
        return response()->json($company, 200);  

    }

}
