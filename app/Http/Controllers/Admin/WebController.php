<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use \Spatie\MediaLibrary\Models\Media;

use App\Post;

class WebController extends Controller
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
        $posts = new Post();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        $posts = $posts->all();
        $array_datas = ['title', 'order', 'public_yn', 'private_yn', 'private_user_yn', 'id'];
        $array_titles = ['Título', 'Orden', 'Público', 'Privado (negocio)', 'Privado (usuario)', ''];
        
        $datatable = $dt->datatable('datatable_web', $posts, $array_datas, 'edit', 'web', $buscadores, 'admin.web.datatable.datatable', $array_titles);
            
        $script = $dt->script('datatable_web', $buscadores);

        return view('admin.web.index', ['datatable' => $datatable, 'script' => $script, 'search' => $buscadores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.web.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = 'Sección creada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $post = new Post();

            $post->title = $request->title;
            $post->body  = !is_null($request->body) ? $request->body : '';
            $post->slug  = $request->slug;
            $post->order = isset($request->order) ? $request->order : 0;
            $post->public = $request->has('public');
            $post->private = $request->has('private');
            $post->private_user = $request->has('private_user');

            $post->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Storing POST', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Storing POST', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al crear la sección.';
            $m_status = 'error';
        }
        
        return redirect()->route('web.index')->with(['message' => $message, 'm_status' => $m_status]);
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
        $post = Post::where('id', $id)->with(['media' => function($q){
            $q->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(custom_properties, '$.order')) ASC");
        }])->first();

        return view('admin.web.edit', ['post' => $post]);
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
        $message = 'Sección modificada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $post = Post::find($id);

            $post->title = $request->title;
            $post->body  = $post->body  = !is_null($request->body) ? $request->body : '';
            $post->slug  = $request->slug;
            $post->order = isset($request->order) ? $request->order : 0;
            $post->public = $request->has('public');
            $post->private = $request->has('private');
            $post->private_user = $request->has('private_user');

            $post->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Storing POST', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Storing POST', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al modificar la sección.';
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
        $message = 'Sección eliminada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $post = Post::find($id);

            $post->delete();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Deleting Post', ['error' => $e]);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Deleting Post', ['error' => $e]);
        }

        if ($error)
        {
            $message = 'Error al eliminar la sección.';
            $m_status = 'error';
        }

        return redirect()->route('web.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $post = Post::find($request->id);

                $post->delete();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting Post', ['error' => $e]);
                
                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting Post', ['error' => $e]);
                
                return 0;
            }
        }

        return 1;
    }

    public function upload_image(Request $request)
    {
            // $file_name = $this->getFileName($request->file('logo'), $company->id);

            // if ($request->file('logo')->move(public_path() . '/img/companies/', $file_name))
            // 
            // $request->hasFile('logo')

    $CKEditor = Input::get('CKEditor');
    $funcNum = Input::get('CKEditorFuncNum');
    $message = $url = '';
    if (Input::hasFile('upload')) {
        $file = Input::file('upload');
        if ($file->isValid()) {
            $filename = $file->getClientOriginalName();
            $file->move(public_path().'/img/', $filename);
            $url = public_path() .'/img/' . $filename;
        } else {
            $message = 'An error occured while uploading the file.';
        }
    } else {
        $message = 'No file uploaded.';
    }
    return '<script>window.parent.CKEDITOR.tools.callFunction('.$funcNum.', "'.$url.'", "'.$message.'")</script>';
    }

    public function ajaxUploadImage(Request $request)
    {
        return $request->all();
        if ($request->ajax())
        {
            return $request->all();
        }
    }

    public function galleryAdd(Request $request){

        $post = Post::where('id', $request->post_id)->with('media')->first();
        
        if(is_null($post)){
            \Session::flash('m_status', 'success');
            \Session::flash('message', trans('app.admin.web.entrada_desconocida'));
            return redirect()->back();
        }

        if($post->addMediaFromRequest('image')->withCustomProperties([
            'order' => $post->media->count(),
            'title' => $request->has('title') ? $request->title : '',
            'link' => $request->has('link') ? $request->link : '',
        ])->toMediaCollection()){
            \Session::flash('m_status', 'success');
            \Session::flash('message', trans('app.admin.web.imagen_añadida'));
        } else {
            \Session::flash('m_status', 'error');
            \Session::flash('message', trans('app.admin.web.error_subiendo_imagen'));
        }
        return redirect()->back();
        
    }

    public function galleryDelete(Request $request){

        $post = Post::where('id', $request->post_id)->with('media')->first();
        $media = Media::find($request->media_id);
        
        if(is_null($post) || is_null($media)){
            \Session::flash('m_status', 'success');
            \Session::flash('message', trans('app.admin.web.entrada_desconocida'));
            return redirect()->back();
        }

        $media->delete();
        \Session::flash('m_status', 'success');
        \Session::flash('message', trans('app.admin.web.imagen_eliminada'));

        $post = Post::where('id', $post->id)->with(['media' => function($q){
            $q->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(custom_properties, '$.order')) ASC");
        }])->first();

        $i = 0;
        foreach($post->media as $media){
            $custom_properties = $media->custom_properties;
            $custom_properties['order'] = $i;
            $media->custom_properties = $custom_properties;
            $media->save();
            $i++;
        }

        return redirect()->back();

    }

    public function galleryOrder(Request $request){

        $post = Post::where('id', $request->post_id)->first();
        $media = Media::find($request->media_id);
        
        if(is_null($post) || is_null($media)){
            \Session::flash('m_status', 'error');
            \Session::flash('message', trans('app.admin.web.entrada_desconocida'));
            return redirect()->back();
        }

        switch($request->accion){
            case 'subir-todo':

                $custom_properties = $media->custom_properties;
                $custom_properties['order'] = -1;
                $media->custom_properties = $custom_properties;
                $media->save();

            break;
            case 'bajar-todo':

                $custom_properties = $media->custom_properties;
                $custom_properties['order'] = $post->media->count() + 1;
                $media->custom_properties = $custom_properties;
                $media->save();

            break;
            case 'subir':

                $post = Post::where('id', $request->post_id)->with(['media' => function($q){
                    $q->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(custom_properties, '$.order')) ASC");
                }])->first();

                $media_anterior = null;
                foreach($post->media as $_media){
                    if($_media->id == $media->id && !is_null($media_anterior)){
                        
                        $custom_properties = $media->custom_properties;
                        $custom_properties['order'] = $custom_properties['order'] - 1;
                        $media->custom_properties = $custom_properties;
                        $media->save();
                        
                        $custom_properties = $media_anterior->custom_properties;
                        $custom_properties['order'] = $custom_properties['order'] + 1;
                        $media_anterior->custom_properties = $custom_properties;
                        $media_anterior->save();

                        break;
                    }
                    $media_anterior = $_media;
                }

            break;
            case 'bajar': 

                $post = Post::where('id', $request->post_id)->with(['media' => function($q){
                    $q->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(custom_properties, '$.order')) ASC");
                }])->first();

                $media_anterior = null;
                foreach($post->media as $_media){
                    if($_media->id == $media->id && !is_null($media_anterior)){
                        
                        $custom_properties = $media->custom_properties;
                        $custom_properties['order'] = $custom_properties['order'] + 1;
                        $media->custom_properties = $custom_properties;
                        $media->save();
                        
                        $custom_properties = $media_anterior->custom_properties;
                        $custom_properties['order'] = $custom_properties['order'] - 1;
                        $media_anterior->custom_properties = $custom_properties;
                        $media_anterior->save();

                        break;
                    }
                    $media_anterior = $_media;
                }

            break;
        }

        $post = Post::where('id', $post->id)->with(['media' => function($q){
            $q->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(custom_properties, '$.order')) ASC");
        }])->first();

        $i = 0;
        foreach($post->media as $media){
            $custom_properties = $media->custom_properties;
            $custom_properties['order'] = $i;
            $media->custom_properties = $custom_properties;
            $media->save();
            $i++;
        }

        return redirect()->back();

    }

    public function galleryEdit(Request $request){
        
        $post = Post::where('id', $request->post_id)->first();
        $media = Media::find($request->media_id);
        
        if(is_null($post) || is_null($media)){
            \Session::flash('m_status', 'error');
            \Session::flash('message', trans('app.admin.web.entrada_desconocida'));
            return redirect()->back();
        }

        $custom_properties = $media->custom_properties;
        $custom_properties['title'] = $request->has('title') ? $request->title : '';
        $custom_properties['link'] = $request->has('link') ? $request->link : '';
        $media->custom_properties = $custom_properties;
        $media->save();

        \Session::flash('m_status', 'success');
        \Session::flash('message', trans('app.admin.web.cambios_guardados'));

        return redirect()->back();

    }

}
