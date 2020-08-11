<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Persona;


class PersonaController extends Controller
{


    public function buscarpersonanombre(Request $request)
    {
        $buscar   = $request->buscar;

        if($buscar == ''){
           $persona = Persona::where('persona.condicion','=','1')
                               ->get();
        }else{
            $persona = Persona::where('persona.nombre', 'like', '%'.$buscar.'%')
                               ->where('persona.condicion','=','1')
                               ->get();
        }
        return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Persona' => $persona], 200);
    } 






    public function buscarpersonacodigo(Request $request)
    {
            $filtro = $request->filtro;
            $persona = Persona::where('id','=',$filtro)
                          ->select('id','nombre')->orderBy('nombre','asc')
                          ->first();
          
            if(is_object($persona)){
                $data = ['code' => 200,
                         'status' => 'success',
                         'Persona' => $persona];
            }else{
                $data = ['code'=>404,
                         'status'=> 'error',
                         'message' => 'Error '];
            }
            return response()->json($data, $data['code']);
    }




    public function index()
    {
        $persona = Persona::get();
  
        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'Persona'  => $persona], 200);
    }




    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
                $validate = \Validator::make($params_array,[
                  'identificador'   => 'required|unique:persona',
                  'nombre'      => 'required',
                  'direccion'   => 'required'
                ]);

                if($validate->fails()){
                $data = ['code'     => 400,
                         'status'   => 'error',
                         'message'  => 'Datos incorrectos, error'];
                }else{

                $persona = new Persona();
                $persona->nombre  =  $params_array['nombre'];
                $persona->direccion   =  $params_array['direccion'];
                $persona->identificador  =  $params_array['identificador'];
                $persona->foto    =  $params_array['foto'];
                $persona->condicion  = 1;
                $persona->telefono  = $params_array['telefono'];
                $persona->celular   = $params_array['celular'];
                
                $persona->save();

                $data = [ 'code'    =>  200,
                          'status'  =>  'success',
                          'Persona'=>  $persona];
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
       $persona = Persona::where('persona.id','=',$id)
                            ->first();
      
        if(is_object($persona)){
            $data = ['code' => 200,
                     'status' => 'success',
                     'Persona' => $persona];
        }else{
            $data = ['code'=>404,
                     'status'=> 'error',
                     'message' => 'Error '];
        }
        return response()->json($data, $data['code']);
    }


    public function update(Request $request, $id)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
      
        if(!empty($params_array)){
                $validate    = \Validator::make($params_array,[
                  'identificador' => 'required|unique:persona,identificador,'.$id,
                  'nombre'      => 'required',
                  'direccion'   => 'required'
           ]);

                if($validate->fails()){
                $data = ['code' => 400,
                         'status'=> 'error',
                         'message' => 'No se actualizo persona, verifique datos'];
                }else{
                

                $ant    =   Persona::where('id', $id)->first();
                $persona = Persona::where('id', $id)
                                  ->update($params_array);
                $act = Persona::where('id', $id)->first(); 
                
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
          $persona = Persona::find($id);
          if(!empty($persona)){

              if($persona->condicion == 1){
                $persona->condicion = 0;
              }else{
                $persona->condicion = 1;
              }

              $persona->update();
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
        \Storage::disk('personas')->put($image_name, \File::get($image));
        $data = ['code'   =>200,
                 'status' => 'success',
                 'image' => $image_name];
      }
      return response()->json($data, $data['code']);
    }





    public function getImage($filename)
    {
      $isset = \Storage::disk('personas')->exists($filename); 
      if($isset){
        $file = \Storage::disk('personas')->get($filename);
        return new Response($file, 200);
      }else{
        $data = ['code'   =>400,
                 'status' => 'error',
                 'message' => 'Imagen no existe'];
      }
     return response()->json($data, $data['code']); 
    }


}
