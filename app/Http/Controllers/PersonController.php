<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersonController extends Controller
{
 
    
    public function store(Request $request){

        if(!empty($request)){
             // Validate Info
             $validator = \Validator::make($request->all(),[
                 'first_name' => 'required|string|max:256',
                 'last_name' => 'required|string',
                 'email' => 'required|email|unique:people',
                 'document' => 'required|string|unique:people',
                 'type_person' => 'required|integer',
                 'img' => 'image'
             ]);
            
             if($validator->fails()){
                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'Persona No Registrada',
                    'error' => $validator->errors()
                );
             }else{
                $person = new Person();
                // Create User
                if($request->file('img')){
                    $image = $request->file('img');
                    // SAVE IMAGE
                    $image_path_name = time().$image->getClientOriginalName();
                    Storage::disk('users')->put($image_path_name, \File::get($image));
                    $person->img = $image_path_name;
                }
                
                $person->first_name = $request->input('first_name');
                $person->last_name = $request->input('last_name');
                $person->email = $request->input('email');
                $person->document = $request->input('document');
                $person->type_person = $request->input('type_person');

                 $person->save();
 
                 $data = array(
                     'status' => 'success',
                     'code' => '200',
                     'message' => 'Persona Registrada',
                     'persona' => $person
                 );
             }
               
    
 
             
        }else{
 
             $data = array(
                 'status' => 'error',
                 'code' => '400',
                 'message' => 'Persona no registrada'
             );
 
        }
 
        return response()->json($data,$data['code']);
    }
    
    public function list($number = ''){
        
        
        $message = '';
        switch ($number) {
            case '1':
                
                $people = Person::where('type_person',1)->get();
                if(count($people) <= 0) $message = "No hay personas de tipo 1 Registradas";
                break;
            
            case '2':
                $people = Person::where('type_person',2)->get();
                if(count($people) <= 0){ $message = "No hay personas de tipo 2 registradas"; }
                break;
                
            case '':
                $people = Person::get();
                if(count($people) <= 0) $message = "No hay personas registradas";
                break;

            default:
                $people = array();
                $message = "Caso inexistente";
                break;
        }

        if(count($people) >=1){
            $data = array(
                "status" => 'success',
                "code" => "200",
                "people" => $people
            );
        }else{
            $data = array(
                "status" => 'error',
                "code" => "400",
                "messsage" => $message
            );
        }
        return response()->json($data,$data['code']);
    }

    public function show($id_person){
        $person =  Person::where('id',$id_person)->first();
       return $person;
    }

    public function update(Request $request, $id_person ){

        if(!empty($request)){
            // Validate Info

            $person = Person::find($id_person);
            $person_json = json_decode($person,true);
           
            $validator = \Validator::make($request->all(),[
                'first_name' => 'nullable|string|max:256',
                'last_name' => 'required|string',
                'email' => 'nullable|email|unique:people,email,'.$person->id,
                'document' => 'nullable|string|unique:people,document,'.$person->id,
                'type_person' => 'nullable|integer',
                'img' => 'nullable|image'
            ]);
            
            if($validator->fails()){
               $data = array(
                   'status' => 'error',
                   'code' => '400',
                   'message' => 'Persona No Actualizada',
                   'error' => $validator->errors()
               );
            }else{

                $image = $request->file('img');
                if($image){
                    
                    // SAVE IMAGE
                    $image_path_name = time().$image->getClientOriginalName();
                    Storage::disk('users')->put($image_path_name, \File::get($image));

                    if($person->img){
                        Storage::disk('users')->delete($person->img);
                    }
                    $person_json['img'] = $image_path_name;
                }
                
                $person->update($person_json);
                // Profile image
                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'Persona Actualizada',
                    'persona' => $person
                );
            }
                
            
            
            

            
       }else{

            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Persona no Actualizada'
            );

       }

       return response()->json($data,$data['code']);
    }

    public function destroy($id_user){
        $person = Person::find($id_user);
        
        if($person){
            $person->delete();
            $data = array(
                "status" => "success",
                "code" => "200",
                "message" => "Usuario Eliminado"
            );
        }else{
            $data = array(
                "status" => "error",
                "code" => "400",
                "message" => "No se pudo eliminar el usuario"
            );
        }

        return response()->json($data);
    }
}
