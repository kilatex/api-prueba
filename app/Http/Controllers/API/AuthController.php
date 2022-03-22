<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\User;


class AuthController extends Controller
{
    public function register(Request $request){

        // get and validate info
        $validatedData = $request->validate([
            'email' => 'required|max:255|unique:users',
            'password' => 'required|confirmed'
        ]);

        // hash passwords
        $validatedData['password'] = Hash::make($request->password);
        $validatedData['name'] = "Administrador";
        //create user and accessToken
        $user = User::create($validatedData);
        $accessToken = $user->createToken('authToken')->accessToken;
        
        return response([
            'user' => $user,
            'access_token' => $accessToken
        ]);
         
    }

    public function login(Request $request){
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if(!auth()->attempt($loginData)){
            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Login incorrecto, Credenciales incorrectas'
            );
        }else{
            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            $data = array(
                'status' => 'success',
                'code' => '200',
                'user' => auth()->user(),
                'access_token' => $accessToken

            );
        }

        return response($data);


    }



}
