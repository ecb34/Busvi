<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Post;
use Illuminate\Support\Facades\Auth;

class InfoController extends Controller
{

    public function getPrivatePost($slug){

        $post = Post::where('slug', $slug)->first();
        
        if(is_null($post)){
            abort(404);
        }

        if(Auth::user()->role == 'user' && !$post->private_user){
            abort(404);
        }

        if((Auth::user()->role == 'admin' || Auth::user()->role == 'crew') && !$post->private){
            abort(404);
        }
        
        return view('admin/info/post', compact('post'));

    }

}
