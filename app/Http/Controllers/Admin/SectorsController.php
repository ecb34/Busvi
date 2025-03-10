<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;

use App\Sector;

class SectorsController extends Controller
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
        $sectors = new Sector();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        $sectors = $sectors->all();
        $array_datas = ['parent', 'name', 'id'];
        $array_titles = ['Padre', 'Nombre', ''];
        
        $datatable = $dt->datatable(
                                    'datatable_sectors', $sectors, $array_datas, 'edit', 'sectors', $buscadores, 'admin.sectors.datatable.datatable', $array_titles
                                );
            
        $script = $dt->script('datatable_sectors', $buscadores);

        return view('admin.sectors.index', ['datatable' => $datatable, 'script' => $script, 'search' => $buscadores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $all_sectors = Sector::all()->pluck('name', 'id')->toArray();
        $all_sectors = array_prepend($all_sectors, 'Ninguno', 0);

        return view('admin.sectors.create', ['all_sectors' => $all_sectors]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = 'Sector creado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $sector = new Sector();

            $sector->name = $request->name;
            $sector->sector_parent_id = $request->sector_paretn_id;

            if ($request->hasFile('image') && $request->file('image')->isValid())
            {
                $file_name = $this->getFileName($request->file('image'), $sector->id);

                if ($request->file('image')->move(public_path() . '/img/sectors/', $file_name))
                {
                    $sector->img = $file_name;
                }
            }

            $sector->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Storing Sector', ['error' => $e]);
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
            $message = 'Error al crear el sector.';
            $m_status = 'error';
        }
        
        return redirect()->route('sectors.index')->with(['message' => $message, 'm_status' => $m_status]);
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
        $sector = Sector::find($id);
        $all_sectors = Sector::where('id', '!=', $id)->pluck('name', 'id')->toArray();
        $all_sectors = array_prepend($all_sectors, 'Ninguno', 0);

        return view('admin.sectors.edit', ['sector' => $sector, 'all_sectors' => $all_sectors]);        
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
        $message = 'Sector modificado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $path = public_path() . '/img/sectors/';

            $sector = Sector::find($id);

            $sector->name = $request->name;
            $sector->sector_parent_id = $request->sector_paretn_id;

            if ($request->hasFile('image') && $request->file('image')->isValid())
            {
                $file_name = $this->getFileName($request->file('image'), $sector->id);

                if ($request->file('image')->move($path, $file_name))
                {
                    \File::delete($path . $sector->img);
                    
                    $sector->img = $file_name;
                }
            }

            $sector->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating Sector', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Updating Sector', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al crear el sector.';
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
        $message = 'Sector eliminado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $sector = Sector::find($id);
            
            \File::delete(public_path() . '/img/sectors/' . $sector->img);

            $sector->delete();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Deleting Sector', ['error' => $e]);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Deleting Sector', ['error' => $e]);
        }

        if ($error)
        {
            $message = 'Error al crear el sector.';
            $m_status = 'error';
        }

        return redirect()->route('sectors.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $sector = Sector::find($request->id);
            
                \File::delete(public_path() . '/img/sectors/' . $sector->img);

                $sector->delete();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting Sector', ['error' => $e]);
                
                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting Sector', ['error' => $e]);
                
                return 0;
            }
        }

        return 1;
    }

    private function getFileName($file)
    {
        if (strlen($file->getClientOriginalName()) <= 120)
        {
            return Carbon::now()->timestamp . '_' . $file->getClientOriginalName();
        }

        return Carbon::now()->timestamp . '.' . $file->extension();
    }
}
