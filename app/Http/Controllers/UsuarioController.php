<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use Carbon\Carbon;

class UsuarioController extends Controller
{
    /////////////////////
    /////////////////////
    /////ADMINISTRADOR///
    /////////////////////
    /////////////////////

    public function userUnreadNotification(Request $request)
    {
        //comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  //TRUE O FALSE

        if($checkToken)
        {
            //sacar usuario identificado
            $userLog = $jwtAuth->checkToken($token, true);
            $user = User::where('id', $userLog->sub)->first();
            $hoy = Carbon::now();

            //contador de notificaciones no leidas
            //$unread = intval(count($user->unreadNotifications));
            $notification = $user->Notifications
                            ->where('read_at','=',null)
                            ->where('created_at', '>', $hoy->toDateString() );

        }

        return response()->json( ['notification'=>$notification] );
    }



    public function userReadNotification(Request $request)
    {
        //comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  //TRUE O FALSE

        if($checkToken)
        {
            //sacar usuario identificado
            $userLog = $jwtAuth->checkToken($token, true);
            $user = User::where('id', $userLog->sub)->first();
            try {
              $user->unreadNotifications->markAsRead();
              
              $data = [ 'code'  =>200,
                        'status'=>'success'];

            } catch (Exception $e) {
                $data = [ 'code'  =>400,
                          'status'=>'error'];
              
            }
        }

        return response()->json([$data]);
    }


    public function buscarusuarionombre(Request $request)
    {
        $buscar   = $request->buscar;

        if($buscar == ''){
            $usuario = User::get();
        }else{
            $usuario = User::where('users.name', 'like', '%'.$buscar.'%')
                           ->orWhere('users.surname', 'like', '%'.$buscar.'%')
                           ->orWhere('users.email', 'like', '%'.$buscar.'%')
                           ->get();
        }
        return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Usuario' => $usuario], 200);
    } 



    public function index()
    {
        $user = User::get();
 
        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'Usuario' => $user], 200);
    }


    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
       
       //limpiar datos
   //    $params_array = array_map('trim', $params_array);


        if(!empty($params_array)){
                $validate = \Validator::make($params_array,[
                  'name'      => 'required',
                  'surname'   => 'required',
                  'email'     => 'required|email|unique:users',
                  'role'      =>     'required',
                  'identificador' => 'required|unique:users'
                ]);

                if($validate->fails()){
                $data = ['code' => 400,
                         'status'=> 'error',
                         'message' => 'Datos incorrectos, error',
                         'errors'    =>  $validate->errors()];
                }else{

                    //cifrar contraseña
                    $pwd = hash('sha256', $params_array['identificador']);


                    $user = new User();
                    $user->name         = $params_array['name'];
                    $user->surname      = $params_array['surname'];
                    $user->description  = $params_array['description'];
                    $user->email        = $params_array['email'];
                    $user->role         = $params_array['role'];
                    $user->image        = $params_array['image'];
                    $user->identificador  = $params_array['identificador'];
                    $user->password     = $pwd;
                    $user->condicion    = 1;
                    $user->save();
            
                    $data = [ 'code'  =>200,
                              'status'=>'success',
                              'User'=>$user];
                }
        }else{
                $data = [ 'code'  =>400,
                          'status'=>'error',
                          'message'=>'No hay datos'];
        }
        return response()->json($data, $data['code']);
    }


    
    public function show($id)
    {
        $user = User::where('users.id', '=', $id)->first();
    
        if(is_object($user)){
            $data = ['code' => 200,
                     'status' => 'success',
                     'User' => $user];
        }else{
            $data = ['code'=>404,
                     'status'=> 'error',
                     'message' => 'Error usuario no existe'];
        }
        return response()->json($data, $data['code']);
    }


    
    public function update(Request $request, $id)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
   
        unset($params_array['remember_token']);
        unset($params_array['condicion']);

      
        if(!empty($params_array)){
                $validate = \Validator::make($params_array,[
                  'id'        => 'required',
                  'name'      => 'required',
                  'surname'   => 'required',
                  'email'     => 'required|email|unique:users,email,'.$id,
                  'role'      => 'required',
                  'identificador' => 'required|unique:users,identificador,'.$id
              
                ]);


                if($validate->fails()){

                $data = ['code' => 400,
                         'status'=> 'error',
                         'message' => 'No se actualizo usuario, verifique datos',
                         'errors'    =>  $validate->errors()];
        }else{
                $ant =   User::where('id', $id)->first();
                $usuario = User::where('id', $id)
                                  ->update($params_array);

                $act = User::where('id', $id)->first(); 
                $data = [ 'code'  =>200,
                          'status'=>'success',
                          'ant'=>$ant,
                          'act' => $act];
                }

        }else{
            $data = ['code'   =>404,
                     'status' => 'error',
                     'message' => 'No hay datos'];
        }
        return response()->json($data, $data['code']);
    }



    public function destroy(Request $request, $id)
    {
          $usuario = User::find($id);
          if(!empty($usuario)){

              if($usuario->condicion == 1){
                $usuario->condicion = 0;
              }else{
                $usuario->condicion = 1;
              }

              $usuario->update();
              $data = [ 'code'  => 200,
                        'status'=>'success',
                      ];
          }else{
              $data = [ 'code'  => 400,
                        'status'=>'error'
                      ];              
          }
           return response()->json($data, $data['code']);
    }


    public function subirImagen(Request $request)
    {
      $image = $request->file('file0');
      $validate = \Validator::make($request->all(), [
          'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
      if(!$image || $validate->fails()){
            $data = [ 'code'=> 400,
                  'status' => 'error',
                  'message'=> 'Error al subir imagen'];
      }else{
        $image_name = time().$image->getClientOriginalName();
        \Storage::disk('users')->put($image_name, \File::get($image));
        $data = ['code'   =>200,
                 'status' => 'success',
                 'image' => $image_name];
      }
      return response()->json($data, $data['code']);
    }



    public function getImage($filename)
    {
      $isset = \Storage::disk('users')->exists($filename); 
      if($isset){
        $file = \Storage::disk('users')->get($filename);
        return new Response($file, 200);
      }else{
        $data = ['code'   =>400,
                 'status' => 'error',
                 'message' => 'Imagen no existe'];
      }
     return response()->json($data, $data['code']); 
    }




    /////////////////////
    /////////////////////
    ///////USER LOGUIN/
    /////////////////////
    ///////JWTAUTH///////
    /////////////////////
    /////////////////////

    public function login(Request $request)
    {
      //Recibir datos por post
      $json = $request->input('json', null);
      $params = json_decode($json);
      $params_array = json_decode($json, true);


      // validar datos
      $validate = \Validator::make($params_array,[
              'email'     => 'required|email',
              'password'  => 'required'           
            ]);

      if($validate->fails()){
         $signup = ['code' => 400,
                    'status'=> 'error',
                    'message' => 'Usuario no se ha logueado, error informacion incorrecta',
                    'errors'    =>  $validate->errors()];
      }else{
          //cifrar contraseña
          $pwd = hash('sha256', $params_array['password']);

          $jwtAuth = new \JwtAuth();
          //devolver token
          $signup = $jwtAuth->signup($params_array['email'], $pwd);
      
                  if(!empty($params->getToken))
                  { 
                    //devolver datos de usuario co 3er parametro
                    $signup = $jwtAuth->signup($params_array['email'], $pwd, true);
                  }
                  
        }    
        //LOGIN DEVUELVE TOKEN   (O DATOS DEPENDE DE 3ER PARAMETRO)
        return response()->json($signup, 200);
    }


    
    public function updateUserLog(Request $request)
    {
        //recoger datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);     //objet
        $params_array = json_decode($json, true);    //array
        //comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  //TRUE O FALSE

        if($checkToken && !empty($params_array))
        {
            //sacar usuario identificado
            $userLog = $jwtAuth->checkToken($token, true);
            //validar datos
            $validate = \Validator::make($params_array,[
                      'name'    =>  'required',
                      'surname' =>  'required',
                      'email'     => 'required|email|unique:users,email,'.$userLog->sub,
                      'identificador' => 'unique:users,identificador,'.$userLog->sub
            ]);

            if(!$validate->fails()){

                //quitar campos que no se actualizan
                unset($params_array['id']);
                unset($params_array['role']);
               // unset($params_array['password']);
                unset($params_array['identificador']);
                unset($params_array['created_at']);
                unset($params_array['remember_token']);
               
                //actualizar en db
                $ant    =   User::where('id', $userLog->sub)->first();
                $usuario  = User::where('id', $userLog->sub)
                               ->update($params_array);
                $act    =   User::where('id', $userLog->sub)->first(); 
          
                //devolver respuesta con resultado
                $data = [ 'code'  =>200,
                          'status'=>'success',
                          'ant'=>$ant,
                          'act' => $act];
            }else{
                $data = ['code' => 400,
                         'status'=> 'error',
                         'message' => 'Error de datos, error',
                         'errors'    =>  $validate->errors()];
            }

        }else{
            $data = [
              'code' => 400,
              'status' => 'error',
              'message' => 'El usuario no esta identificado'
            ];
        }

        return response()->json($data, $data['code']);
    }



    public function updatePassUserLog(Request $request)
    {
        //recoger datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);     //objet
        $params_array = json_decode($json, true);    //array
        //comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  //TRUE O FALSE

        if($checkToken && !empty($params_array))
        {
            //sacar usuario identificado
            $userLog = $jwtAuth->checkToken($token, true);
            //validar datos
            $validate = \Validator::make($params_array,[
                      'password' =>  'required'
            ]);

            if(!$validate->fails()){

                //quitar campos que no se actualizan
                unset($params_array['id']);
                unset($params_array['name']);
                unset($params_array['surname']);
                unset($params_array['email']);
                unset($params_array['identificador']);
                unset($params_array['role']);
                unset($params_array['description']);
                unset($params_array['image']);
                unset($params_array['condicion']);
                unset($params_array['created_at']);
                unset($params_array['remember_token']);

                 $pwd = hash('sha256', $params_array['password']);
                 $params_array['password'] = $pwd;
                  
                $usuario = User::where('id', $userLog->sub)
                               ->update($params_array);
                //devolver respuesta con resultado
                $data = [ 'code'  =>200,
                          'status'=>'success'];
          }else{
             $data = ['code' => 400,
                       'status'=> 'error',
                       'message' => 'Error de datos, error',
                       'errors'    =>  $validate->errors()];
          }

        }else{
            $data = [
              'code' => 400,
              'status' => 'error',
              'message' => 'El usuario no esta identificado'
            ];
        }

        return response()->json($data, $data['code']);

    }



    public function uploadImage(Request $request)
    {
        //comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  //TRUE O FALSE

        //recoger datos por post
        $image = $request->file('file0');

        $validate = \Validator::make($request->all(), [
          'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
   
        if($image || !$validate->fails())
        {
            if($checkToken)
            {
                $image_name = time().$image->getClientOriginalName();
                \Storage::disk('users')->put($image_name, \File::get($image));
               
                $data = ['code'   =>200,
                         'status' => 'success',
                         'image' => $image_name];
            }else{
                  $data = [
                  'code' => 400,
                  'status' => 'error',
                  'message' => 'Error al subir imagen, usuario no esta identificado'
                ]; 
            }

          }else{
                $data = [ 'code'=> 400,
                  'status' => 'error',
                  'message'=> 'Error al subir imagen'];
        }
    
          return response()->json($data, $data['code']);
    }


    public function getImageUser($filename)
    {
      //existe
      $isset = \Storage::disk('users')->exists($filename); 
      if($isset){
        $file = \Storage::disk('users')->get($filename);
        return new Response($file, 200);
      }else{
        $data = ['code'   =>400,
                 'status' => 'error',
                 'message' => 'Imagen no existe'];
      }
     return response()->json($data, $data['code']); 
    }






}
