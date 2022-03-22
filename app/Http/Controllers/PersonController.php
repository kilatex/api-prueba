<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
   
    
    public function store(Request $request){
        
        if(!empty($request)){
             // Validate Info
             $validator = \Validator::make($request->all(),[
                 'first_name' => 'required|string|min:2|max:256',
                 'last_name' => 'required|string|min:2|max:256',
                 'email' => ['required','max:255',Rule::unique('people','email')->where(function ($query){
                    return $query->whereNull('deleted_at');
                 })],
                 'document' =>  ['required','max:255',Rule::unique('people','document')->where(function ($query){
                    return $query->whereNull('deleted_at');
                 })],
                 'type_person' => 'required|integer',
                 'img' => ' nullable|image'
             ]);
            
             if($validator->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => '400',
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
                }else{
                    $person->img = '';
                }

                $validarName =  preg_match('/[A-Za-z]/',$request->input('first_name'));
                $validarLastName =  preg_match('/[A-Za-z]/',$request->input('last_name'));
                if($validarName && $validarLastName){
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
                }else{
                    $errors = array();
                    if(!$validarName){
                        $errors['first_name'] = 'Nombre No Valido';
                    }
                    if(!$validarLastName){
                        $errors['last_name'] = 'Apellido no valido';

                    }
                    $data = array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'Persona NO REGISTRADA',
                        'error' => $errors
                    );
                }
               
             }
               
    
 
             
        }else{
 
             $data = array(
                 'status' => 'error',
                 'code' => '400',
                 'message' => 'Persona no registrada'
             );
 
        }
 
        return response()->json($data);
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
                "message" => $message
            );
        }
        return response()->json($data);
    }

    public function show($id_person){
        $person =  Person::where('id',$id_person)->first();
       return $person;
    }
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
    public function update(Request $request, $id_person ){

        if(!empty($request)){
            // Validate Info

            $person = Person::find($id_person);
            $person_json = json_decode('');
           
            $validator = \Validator::make($request->all(),[
                'first_name' => 'required|string|min:3|max:256',
                'last_name' => 'required|string|min:3|max:256',
                'email' => ['required','max:255','unique:people,email,'.$person->id],
                'document' => 'required|string|unique:people,document,'.$person->id,
                'type_person' => 'required|integer',
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

                $validarName =  preg_match('/[A-Za-z]/',$request->input('first_name'));
                $validarLastName =  preg_match('/[A-Za-z]/',$request->input('last_name'));
                if($validarName && $validarLastName){
                    $person_json['first_name'] = $request->input('first_name');
                    $person_json['last_name'] = $request->input('last_name');
                    $person_json['email'] = $request->input('email');
                    $person_json['document'] = $request->input('document');
                    $person_json['type_person'] = $request->input('type_person');
                    $person->update($person_json);
                    // Imagen del usuario
                    $data = array(
                        'status' => 'success',
                        'code' => '200',
                        'message' => 'Persona Actualizada',
                        'persona' => $person
                    );
                }else{
                    $errors = array();
                    if(!$validarName){
                        $errors['first_name'] = 'Nombre No Valido';
                    }
                    if(!$validarLastName){
                        $errors['last_name'] = 'Apellido no valido';

                    }
                    $data = array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'Persona NO REGISTRADA',
                        'error' => $errors
                    );
                }
            }
                
            
            
            

            
       }else{

            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Persona no Actualizada'
            );

       }

       return response()->json($data);
    }

    public function search(Request $request){
      
        $tipo_busqueda = $request->input('select');
        $texto = $request->input('text');
        $validarTexto = preg_match('/[A-Za-z]/',$texto);
        if($validarTexto && ($tipo_busqueda == "first_name" || $tipo_busqueda == "last_name")){
            $users = Person::where($tipo_busqueda,'LIKE', "%{$texto}%")->get();
            if(count($users)>0){
                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'USUARIOS ENCONTRADOS',
                    'users' => $users
                );;
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Ningún Usuario Encontrado'
                );  
            }
            
        }else{
            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Ningún Usuario Encontrado'
            );
        }
        return response()->json($data);
    }

   
    public function destroy($id_user){
        $person = Person::find($id_user);
        
        
        if($person){
            $delete_info = array(
                'email' => "D_".$person['email'],
                'document' => "D_".$person['document']
            );
            $person->update($delete_info);
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
