<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Rate;

class RatesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->middleware('isOperator');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rates = new Rate();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        $rates = $rates->all();
        $array_datas = ['name', 'months', 'amount', 'id'];
        $array_titles = ['Nombre', 'Meses', 'Precio',''];
        
        $datatable = $dt->datatable(
                                    'datatable_rates', $rates, $array_datas, 'edit', 'rates', $buscadores, 'admin.rates.datatable.datatable', $array_titles
                                );
            
        $script = $dt->script('datatable_rates', $buscadores);

        return view('admin.rates.index', ['datatable' => $datatable, 'script' => $script, 'search' => $buscadores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.rates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = 'Tarifa creado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $rate = new Rate();

            $rate->name = $request->name;
            $rate->months = $request->months;
            $rate->amount = $request->amount;

            $rate->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Storing Rate', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Storing Rate', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al crear la tarifa.';
            $m_status = 'error';
        }
        
        return redirect()->route('rates.index')->with(['message' => $message, 'm_status' => $m_status]);
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
        $rate = Rate::find($id);

        return view('admin.rates.edit', ['rate' => $rate]);
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
        $message = 'Tarifa modificada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $rate = Rate::find($id);

            $rate->name = $request->name;
            $rate->months = $request->months;
            $rate->amount = $request->amount;

            $rate->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating Rate', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Updating Rate', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al modificar la tarifa.';
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
        $message = 'Tarifa eliminada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $rate = Rate::find($id);

            $rate->delete();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Deleting Rate', ['error' => $e]);
            dd('hola mundo');
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Deleting Rate', ['error' => $e]);
            dd('hola mundo');
        }

        if ($error)
        {
            $message = 'Error al eliminar la tarifa.';
            $m_status = 'error';
        }

        return redirect()->route('rates.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $rate = Rate::find($request->id);

                $rate->delete();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting Rate', ['error' => $e]);
                
                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting Rate', ['error' => $e]);
                
                return 0;
            }
        }

        return 1;
    }
}
