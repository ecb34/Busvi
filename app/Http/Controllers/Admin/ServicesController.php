<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Service;
use App\Company;

use Auth;

class ServicesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->middleware('isAdmin');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = new Service();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
        {
            $services = $services->all();
        }
        else
        {
            $services = $services->where('company_id', Auth::user()->company->id)->get();
        }
        
        $array_datas = ['order', 'name', 'min', 'price', 'company_id', 'id'];
        $array_titles = ['Orden', 'Nombre', 'Minutos', 'Precio', 'Negocio', ''];
        
        $datatable = $dt->datatable(
                                    'datatable_services', $services, $array_datas, 'edit', 'services', $buscadores, 'admin.services.datatable.datatable', $array_titles
                                );
            
        $script = $dt->script('datatable_services', $buscadores);

        return view('admin.services.index', ['datatable' => $datatable, 'script' => $script, 'search' => $buscadores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::all()->pluck('name', 'id');

        return view('admin.services.create', ['companies' => $companies]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = 'Servicio creado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $service = new Service();

            $service->name = $request->name;
            $service->min = $request->min;
            $service->price = $request->price;

            if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
            {
                $service->company_id = $request->company;
            }
            else
            {
                $service->company_id = Auth::user()->company->id;
            }

            $service->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Storing Service', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Storing Service', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al crear el servicio.';
            $m_status = 'error';
        }
        
        return redirect()->route('services.index')->with(['message' => $message, 'm_status' => $m_status]);
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
        $service = Service::find($id);
        $companies = Company::all()->pluck('name', 'id');

    	if ((Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator') ||
    		(Auth::user()->role == 'admin' && $service->company_id == Auth::user()->company_id))
        {
        	return view('admin.services.edit', ['service' => $service, 'companies' => $companies]);
        }

        return redirect()->route('admin.crew');
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
        $message = 'Servicio modificado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $service = Service::find($id);

            $service->name = $request->name;
            $service->min = $request->min;
            $service->price = $request->price;
            $service->order = $request->order;

            if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
            {
                $service->company_id = $request->company;
            }
            else
            {
                $service->company_id = Auth::user()->company->id;
            }

            $service->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating Service', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Updating Service', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al modificar el servicio.';
            $m_status = 'error';
        }
        
        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = 'Servicio eliminado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $service = Service::find($id);

            $service->delete();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Deleting Service', ['error' => $e]);
            dd('hola mundo');
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Deleting Service', ['error' => $e]);
            dd('hola mundo');
        }

        if ($error)
        {
            $message = 'Error al eliminar el servicio.';
            $m_status = 'error';
        }

        return redirect()->route('services.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $service = Service::find($request->id);

                $service->delete();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting Service', ['error' => $e]);
                
                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting Service', ['error' => $e]);
                
                return 0;
            }
        }

        return 1;
    }
}
