<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Configuracion;


class ConfiguracionController extends Controller
{
    


    public function index()
    {
        $configuracion = Configuracion::get();
  
        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'Configuracion'  => $configuracion], 200);
    }

    public function getiva()
    {
        $iva = Configuracion::where('configuracion.descripcion','=','iva')
                            ->first();
  
        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'Iva'  => $iva], 200);
    }


    public function show($id)
    {
       $configuracion = Configuracion::where('configuracion.id','=',$id)
                            ->first();
      
        if(is_object($configuracion)){
            $data = ['code' => 200,
                     'status' => 'success',
                     'Configuracion' => $configuracion];
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
                  'descripcion' => 'required',
                  'valor' 		=> 'required'
                ]);

                if($validate->fails()){
                $data = ['code' => 400,
                         'status'=> 'error',
                         'message' => 'No se actualizo configuracion, verifique datos'];
                }else{

//                $params_array['idtipo'] = $params_array['idtipo'];
//                unset($params_array['idtipo']);

                $ant    =   Configuracion::where('id', $id)->first();
                $configuracion = Configuracion::where('id', $id)
                                  ->update($params_array);
                $act = Configuracion::where('id', $id)->first(); 
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



}
