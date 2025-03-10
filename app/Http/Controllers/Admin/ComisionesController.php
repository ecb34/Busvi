<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use App\DataTables\ComisionesDataTable;

use App\Comision;

class ComisionesController extends Controller
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
    public function index(ComisionesDataTable $datatable)
    {     
     return $datatable->render('admin.comisiones.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.comisiones.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = 'Comision creada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $comision = new Comision();
            $comision->nombre = $request->nombre;
            $comision->porcentaje = $request->porcentaje;
            $comision->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Storing Comision', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Storing Sector', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al crear la comision.';
            $m_status = 'error';
        }
        
        return redirect()->route('comisiones.index')->with(['message' => $message, 'm_status' => $m_status]);
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
        $comision = Comision::find($id);

        return view('admin.comisiones.edit', ['comision' => $comision]);        
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
        $message = 'Comision modificada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
           
            $comision = Comision::find($id);

            $comision->nombre = $request->nombre;
            $comision->porcentaje = $request->porcentaje;
            $comision->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating Comision', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Updating Comision', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al editar la Comision.';
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
        $message = 'Comision eliminada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $comision = Comision::find($id);
            $comision->delete();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Deleting Comision', ['error' => $e]);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Deleting Comision', ['error' => $e]);
        }

        if ($error)
        {
            $message = 'Error al borrar la Comision.';
            $m_status = 'error';
        }

        return redirect()->route('comisiones.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $comision = Comision::find($request->id);
                $comision->delete();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting Comision', ['error' => $e]);
                
                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting Comision', ['error' => $e]);
                
                return 0;
            }
        }

        return 1;
    }

  
}
