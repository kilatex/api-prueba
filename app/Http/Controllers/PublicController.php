<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
   
    
    public function getAvatar($filename){
                
        $isset = Storage::disk('users')->exists($filename);

        if($isset){
            $file =  Storage::disk('users')->get($filename);
            return new Response($file,200);
        }else{
            $data = array(
                'status' => 'error',
                'code' => '404',
                'message' => 'Avatar not found',

            );
            return response()->json($data);
        }

    }
  

  


}
