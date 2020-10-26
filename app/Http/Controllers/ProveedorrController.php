<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Proveedor;


class ProveedorrController extends Controller
{
    
    public function buscarproveedornombre(Request $request)
    {
        
        $buscar   = $request->buscar;


        if($buscar == ''){
           $proveedor = Proveedor::where('proveedor.condicion','=','1')
                                 ->take(10)
                                 ->get();
        }else{
            $proveedor = Proveedor::where('proveedor.nombre', 'like', '%'.$buscar.'%')
                               ->where('proveedor.condicion','=','1')
                               ->take(10)
                               ->get();
        }


        return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Proveedor' => $proveedor], 200);
    } 



    public function buscarproveedorcodigo(Request $request)
    {
            $filtro = $request->filtro;
            $proveedor = Proveedor::where('proveedor.id','=',$filtro)
                          ->select('id','nombre')->orderBy('nombre','asc')
                          ->first();
          
            if(is_object($proveedor)){
                $data = ['code' => 200,
                         'status' => 'success',
                         'Proveedor' => $proveedor];
            }else{
                $data = ['code'=>404,
                         'status'=> 'error',
                         'message' => 'Error '];
            }
            return response()->json($data, $data['code']);
    }





    public function index()
    {
        $proveedor = Proveedor::get();
  
        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'Proveedor'  => $proveedor], 200);
    }




    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
                $validate = \Validator::make($params_array,[
                  'identificador'   => 'required|unique:proveedor',
                  'nombre'      => 'required',
                  'direccion'   => 'required'
                ]);

                if($validate->fails()){
                $data = ['code'     => 400,
                         'status'   => 'error',
                         'message'  => 'Datos incorrectos, error'];
                }else{

                $proveedor = new Proveedor();
                $proveedor->nombre  =  $params_array['nombre'];
                $proveedor->direccion   =  $params_array['direccion'];
                $proveedor->identificador  =  $params_array['identificador'];
                $proveedor->foto    =  $params_array['foto'];
                $proveedor->condicion  = 1;
                $proveedor->telefono  = $params_array['telefono'];
                $proveedor->celular   = $params_array['celular'];
                
                $proveedor->save();

                $data = [ 'code'    =>  200,
                          'status'  =>  'success',
                          'Proveedor'=>  $proveedor];
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
       $proveedor = Proveedor::where('id','=',$id)
                            ->first();
      
        if(is_object($proveedor)){
            $data = ['code' => 200,
                     'status' => 'success',
                     'Proveedor' => $proveedor];
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
                  'identificador' => 'required|unique:proveedor,identificador,'.$id,
                  'nombre'      => 'required',
                  'direccion'   => 'required'
           ]);

                if($validate->fails()){
                $data = ['code' => 400,
                         'status'=> 'error',
                         'message' => 'No se actualizo proveedor, verifique datos'];
                }else{
                
//                $params_array['idtipo'] = $params_array['idtipo'];
                unset($params_array['tipo']);

                $ant    =   Proveedor::where('id', $id)->first();
                $proveedor = Proveedor::where('id', $id)
                                  ->update($params_array);
                $act = Proveedor::where('id', $id)->first(); 
                
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
          $proveedor = Proveedor::find($id);
          if(!empty($proveedor)){

              if($proveedor->condicion == 1){
                $proveedor->condicion = 0;
              }else{
                $proveedor->condicion = 1;
              }

              $proveedor->update();
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
        \Storage::disk('proveedores')->put($image_name, \File::get($image));
        $data = ['code'   =>200,
                 'status' => 'success',
                 'image' => $image_name];
      }
      return response()->json($data, $data['code']);
    }





    public function getImage($filename)
    {
      $isset = \Storage::disk('proveedores')->exists($filename); 
      if($isset){
        $file = \Storage::disk('proveedores')->get($filename);
        return new Response($file, 200);
      }else{
        $data = ['code'   =>400,
                 'status' => 'error',
                 'message' => 'Imagen no existe'];
      }
     return response()->json($data, $data['code']); 
    }


}
