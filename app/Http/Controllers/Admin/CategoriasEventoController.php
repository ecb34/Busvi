<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use App\DataTables\CategoriasEventoDataTable;

use App\CategoriaEvento;

class CategoriasEventoController extends Controller
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
    public function index(CategoriasEventoDataTable $datatable)
    {     
     return $datatable->render('admin.categorias_evento.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categorias_evento.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = 'CategoriaEvento creada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $categoria_evento = new CategoriaEvento();
            $categoria_evento->nombre = $request->nombre;           
            $categoria_evento->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Storing CategoriaEvento', ['error' => $e]);
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
            $message = 'Error al crear la categoria_evento.';
            $m_status = 'error';
        }
        
        return redirect()->route('categorias_evento.index')->with(['message' => $message, 'm_status' => $m_status]);
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
        $categoria_evento = CategoriaEvento::find($id);

        return view('admin.categorias_evento.edit', ['categoria_evento' => $categoria_evento]);        
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
        $message = 'CategoriaEvento modificada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
           
            $categoria_evento = CategoriaEvento::find($id);

            $categoria_evento->nombre = $request->nombre;
            $categoria_evento->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating CategoriaEvento', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Updating CategoriaEvento', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al editar la CategoriaEvento.';
            $m_status = 'error';
        }
        
        return redirect()->route('categorias_evento.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = 'CategoriaEvento eliminada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $categoria_evento = CategoriaEvento::find($id);
            $categoria_evento->delete();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Deleting CategoriaEvento', ['error' => $e]);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Deleting CategoriaEvento', ['error' => $e]);
        }

        if ($error)
        {
            $message = 'Error al borrar la CategoriaEvento.';
            $m_status = 'error';
        }

        return redirect()->route('categorias_evento.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $categoria_evento = CategoriaEvento::find($request->id);
                $categoria_evento->delete();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting CategoriaEvento', ['error' => $e]);
                
                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting CategoriaEvento', ['error' => $e]);
                
                return 0;
            }
        }

        return 1;
    }

  
}
