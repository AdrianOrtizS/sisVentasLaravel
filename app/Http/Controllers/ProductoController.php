<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Producto;
use App\TipoProducto;


class ProductoController extends Controller
{



    public function stockminimo()
    {
        $productostockminimo = Producto::join('tipoproducto','producto.idtipo','=','tipoproducto.id')
                               ->select('producto.id','producto.nombre','producto.stock','producto.stockminimo',
                                        'producto.foto','producto.condicion','producto.idtipo','producto.iva',
                                        'producto.codigo','producto.precio', 'tipoproducto.descripcion as tipo')
                               ->where('producto.condicion','=','1')
                               ->where('producto.stock','<=','producto.stockminimo')
                               ->get();
        
        return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Productostockminimo' => $productostockminimo], 200);
    
    } 





    public function productosPdf()
    {
          $producto = Producto::join('tipoproducto','producto.idtipo','=','tipoproducto.id')
                          ->select('producto.id','producto.nombre','producto.stock','producto.stockminimo',
                                  'producto.foto','producto.condicion','producto.idtipo',
                                  'producto.precio','producto.codigo', 
                                  'tipoproducto.descripcion as tipo')
                          ->where('producto.condicion','=','1')
                          ->orderBy('producto.nombre')
                          ->get();
          $cont = Producto::where('producto.condicion','=','1')->count();

          $pdf = \PDF::loadView('pdf.productopdf',['producto'=>$producto, 'cont'=>$cont]);
          
          return $pdf->download('producto.pdf');
    }




    public function buscarproductotipo(Request $request)
    {
        $buscar   = $request->buscar;

        if($buscar == ''){
         $producto = Producto::join('tipoproducto','producto.idtipo','=','tipoproducto.id')
                               ->select('producto.id','producto.nombre','producto.stock','producto.stockminimo',
                                        'producto.foto','producto.condicion','producto.idtipo',
                                        'producto.codigo', 'producto.precio','producto.iva', 
                                        'tipoproducto.descripcion as tipo')
                               ->where('producto.condicion','=','1')
                               ->get();
        }else{
            $producto = Producto::join('tipoproducto','producto.idtipo','=','tipoproducto.id')
                               ->select('producto.id','producto.nombre','producto.stock','producto.stockminimo',
                                        'producto.foto','producto.condicion','producto.idtipo','producto.iva',
                                        'producto.codigo','producto.precio', 'tipoproducto.descripcion as tipo')
                               ->where('producto.nombre', 'like', '%'.$buscar.'%')
                               ->orWhere('tipoproducto.descripcion', 'like', '%'.$buscar.'%')
                               ->where('producto.condicion','=','1')
                               ->get();
        }
        return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Producto' => $producto], 200);
    } 






    public function buscarproductonombre(Request $request)
    {
        $buscar   = $request->buscar;

        if($buscar == ''){
         $producto = Producto::join('tipoproducto','producto.idtipo','=','tipoproducto.id')
                               ->select('producto.id','producto.nombre','producto.stock','producto.stockminimo',
                                        'producto.foto','producto.condicion','producto.idtipo',
                                        'producto.codigo', 'producto.precio','producto.iva', 
                                        'tipoproducto.descripcion as tipo')
                               ->where('producto.condicion','=','1')
                               ->get();
        }else{
            $producto = Producto::join('tipoproducto','producto.idtipo','=','tipoproducto.id')
                               ->select('producto.id','producto.nombre','producto.stock','producto.stockminimo',
                                        'producto.foto','producto.condicion','producto.idtipo','producto.iva',
                                        'producto.codigo','producto.precio', 'tipoproducto.descripcion as tipo')
                               ->where('producto.nombre', 'like', '%'.$buscar.'%')
                               ->orWhere('tipoproducto.descripcion', 'like', '%'.$buscar.'%')
                               ->where('producto.condicion','=','1')
                               ->get();
        }
        return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Producto' => $producto], 200);
    } 



    public function buscarproductocodigo(Request $request)
    {
            $filtro = $request->buscar;
            $producto = Producto::where('codigo','=',$filtro)
                          ->select('codigo','nombre','id','precio','iva','stock','stockminimo')->orderBy('nombre','asc')
                          ->first();
          
            if(is_object($producto)){
                $data = ['code' => 200,
                         'status' => 'success',
                         'Producto' => $producto];
            }else{
                $data = ['code'=>404,
                         'status'=> 'error',
                         'message' => 'Error '];
            }
            return response()->json($data, $data['code']);
    }




    public function index()
    {
        $producto = Producto::join('tipoproducto','producto.idtipo','=','tipoproducto.id')
                            ->select('producto.id','producto.nombre','producto.stock','producto.stockminimo',
                                    'producto.foto','producto.condicion','producto.idtipo',
                                    'producto.precio','producto.codigo','producto.iva', 
                                    'tipoproducto.descripcion as tipo')
                            ->get();


        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'Producto'  => $producto], 200);
    }




    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
                $validate = \Validator::make($params_array,[
                  'nombre'  => 'required|unique:producto',
                  'stock'   => 'required',
                  'precio'  => 'required',
                  'idtipo'  => 'required',
                  'iva'     => 'required'
                ]);

                if($validate->fails()){
                $data = ['code'     => 400,
                         'status'   => 'error',
                         'message'  => 'Datos incorrectos, error'];
                }else{

                $producto = new Producto();
                $producto->nombre  =  $params_array['nombre'];
                $producto->stock   =  $params_array['stock'];
                $producto->stockminimo   =  $params_array['stockminimo'];
                $producto->precio  =  $params_array['precio'];
                $producto->foto    =  $params_array['foto'];
                $producto->codigo    =  $params_array['codigo'];
                $producto->condicion  = 1;
                $producto->iva     =  $params_array['iva'];
                $producto->idtipo  =  $params_array['idtipo'];
                
                $producto->save();

                $data = [ 'code'    =>  200,
                          'status'  =>  'success',
                          'Producto'=>  $producto];
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
       $producto = Producto::join('tipoproducto','producto.idtipo','=','tipoproducto.id')
                            ->select('producto.id','producto.nombre','producto.stock','producto.stockminimo',
                                    'producto.foto','producto.condicion','producto.precio',
                                    'producto.idtipo','producto.codigo','producto.iva',
                                    'tipoproducto.descripcion as tipoproducto')
      
                            ->where('producto.id','=',$id)
                            ->first();
      
        if(is_object($producto)){
            $data = ['code' => 200,
                     'status' => 'success',
                     'Producto' => $producto];
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
                  'nombre'   => 'required',
                  'precio'   => 'required',
                  'stock'     => 'required',
                  'codigo'     => 'required',
          //      'condicion' => 'required',
                  'iva'     => 'required',
                  'idtipo'    =>'required'
                ]);

                if($validate->fails()){
                $data = ['code' => 400,
                         'status'=> 'error',
                         'message' => 'No se actualizo producto, verifique datos'];
                }else{
                
//                $params_array['idtipo'] = $params_array['idtipo'];
                unset($params_array['tipoproducto']);

                $ant    =   Producto::where('id', $id)->first();
                $producto = Producto::where('id', $id)
                                  ->update($params_array);
                $act = Producto::where('id', $id)->first(); 
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
          $producto = Producto::find($id);
          if(!empty($producto)){

              if($producto->condicion == 1){
                $producto->condicion = 0;
              }else{
                $producto->condicion = 1;
              }

              $producto->update();
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
        \Storage::disk('productos')->put($image_name, \File::get($image));
        $data = ['code'   =>200,
                 'status' => 'success',
                 'image' => $image_name];
      }
      return response()->json($data, $data['code']);
    }




    public function getImage($filename)
    {
      $isset = \Storage::disk('productos')->exists($filename); 
      if($isset){
        $file = \Storage::disk('productos')->get($filename);
        return new Response($file, 200);
      }else{
        $data = ['code'   =>400,
                 'status' => 'error',
                 'message' => 'Imagen no existe'];
      }
     return response()->json($data, $data['code']); 
    }




}
