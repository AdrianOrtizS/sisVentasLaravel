<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\TipoProducto;



class TipoProductoController extends Controller
{

    public function buscartipoproductonombre(Request $request)
    {
        $buscar   = $request->buscar;

        if($buscar == ''){
         $tipoproducto = TipoProducto::get();
        }else{
            $tipoproducto = TipoProducto::Where('tipoproducto.descripcion', 'like', '%'.$buscar.'%')
                               ->get();
        }
        return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Tipoproducto' => $tipoproducto], 200);
    } 



    public function index()
    {
        $tipoproducto = TipoProducto::get();
  
        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'Tipoproducto'  => $tipoproducto], 200);
    }




    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
                $validate = \Validator::make($params_array,[
                  'descripcion'   => 'required'
                ]);

                if($validate->fails()){
                $data = ['code'     => 400,
                         'status'   => 'error',
                         'message'  => 'Datos incorrectos, error'];
                }else{

                $tipoproducto = new TipoProducto();
                $tipoproducto->descripcion  =  $params_array['descripcion'];
                $tipoproducto->condicion  =  1;
                
                $tipoproducto->save();

                $data = [ 'code'    =>  200,
                          'status'  =>  'success',
                          'Tipoproducto'=>  $tipoproducto];
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
       $tipoproducto = TipoProducto::where('tipoproducto.id','=',$id)
                            ->first();
      
        if(is_object($tipoproducto)){
            $data = ['code' => 200,
                     'status' => 'success',
                     'Tipoproducto' => $tipoproducto];
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
                  'descripcion'   => 'required'
                ]);

                if($validate->fails()){
                $data = ['code' => 400,
                         'status'=> 'error',
                         'message' => 'No se actualizo tipo producto, verifique datos'];
                }else{

//                $params_array['idtipo'] = $params_array['idtipo'];
//                unset($params_array['idtipo']);

                $ant    =   TipoProducto::where('id', $id)->first();
                $tipoproducto = TipoProducto::where('id', $id)
                                  ->update($params_array);
                $act = TipoProducto::where('id', $id)->first(); 
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
          $tipoproducto = TipoProducto::find($id);
          if(!empty($tipoproducto)){


              if($tipoproducto->condicion == 1){
                 $tipoproducto->condicion = 0;
              }else{
                $tipoproducto->condicion = 1;
              }

              $tipoproducto->update();
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






}
