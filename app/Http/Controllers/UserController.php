<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
   

    public function login(){
        // GET USER INFO
        $json = $request->input('json',null);
        $params = json_decode($json); //object
        $params_array = json_decode($json,true); //array

        if(!empty($params) && !empty($params_array)){
            $params_array = array_map('trim',$params_array); // trim fields 

            // validate info
            $validate = \Validator::make($params_array,[       
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            
            if($validate->fails()){
                $signup =  array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Login Failed, NOT VALIDATED',
                    'errors' => $validate->errors()
                );
           }else{

            $email = $params_array['email'];
            $password = hash('sha256', $params_array['password']);

            if(!empty($params->getToken)){
                $signup =  $jwtAuth->signup($email,$password);

            }else{
                $signup =  $jwtAuth->signup($email,$password,true);
            }
           }
        }
        else{
            $signup = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Login Failed, INPUTS EMPTY'

            );
        }
        return response()->json($signup,200);
    }
}
