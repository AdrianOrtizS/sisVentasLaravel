<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\PedidoCliente;
use App\DetallePedidoCliente;
use Carbon\Carbon;
// use App\Notifications\VentaNotification;
// use App\Events\VentaEvent;
// use App\Events\StockEvent;
use App\Producto;

use Paypalpayment;


class PedidoClienteController extends Controller
{
    
    public function __construct(){

    }




    // public function numpedido(Request $request)
    // {
    //      $pedido = PedidoCliente::all();
    //      $ult =   $pedido->last();
         
    //      if(empty($ult) || is_null($ult) )
    //      {
    //         $ult['numpedido'] = 0;
    //      }

    //      $ultimo =  $ult['numpedido'];
    //      $ultimo++;
                        
    //      return response()->json(['code'      => 200,
    //                               'status'    => 'success',
    //                               'Numpedido' => $ultimo], 200);
    // } 



    public function index()
    {
        $pedidos = PedidoCliente::join('users','pedidocliente.iduser','=','users.id')
                           	->select(	'pedidocliente.id','pedidocliente.fecha','pedidocliente.numpedido',
                          		      	'pedidocliente.subtotal','pedidocliente.iva','pedidocliente.iva0',
                                		'pedidocliente.total','pedidocliente.condicion',
                                		'users.name as user', 'users.id as idusers','users.role' )
                       		->orderBy(	'pedidocliente.id','desc')
                       		->get();
    
        return response()->json(['code'      => 200,
					             'status'    => 'success',
					             'Pedidos'   => $pedidos], 200);
    }





    public function misPedidos(Request $request)
    {
        //comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  //TRUE O FALSE

        if($checkToken) 
        {
            $userLog = $jwtAuth->checkToken($token, true);
            
            $pedidos = PedidoCliente::join('users','pedidocliente.iduser','=','users.id')
                      ->select( 'pedidocliente.id','pedidocliente.fecha','pedidocliente.numpedido',
                                'pedidocliente.subtotal','pedidocliente.iva','pedidocliente.iva0',
                              'pedidocliente.total','pedidocliente.condicion',
                              'users.name as user', 'users.id as idusers','users.role' )
                      ->where('pedidocliente.iduser', '=', $userLog->sub)  
                      ->orderBy('pedidocliente.id','desc')
                      ->get();  
    
            return response()->json(['code' => 200,
                           'status'    => 'success',
                           'Pedidos'   => $pedidos], 200);
        }


    }




    public function store(Request $request)
    {

        $pedido = PedidoCliente::all();
        $ult =   $pedido->last();
         
        if(empty($ult) || is_null($ult) )
        {
           $ult['numpedido'] = 0;
        }

        $sigPed =  $ult['numpedido'];
        $sigPed++;

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        //comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  //TRUE O FALSE


        if ($checkToken) 
        {
                if(!empty($params_array))
                {
                      //sacar usuario identificado
                      $userLog = $jwtAuth->checkToken($token, true);
                      $prodveri= 0;

                      try{
                            DB::beginTransaction();
                            $mytime = Carbon::now('America/Guayaquil');
                            $pedido = new PedidoCliente();
                            
                            $pedido->fecha     = $mytime;
                            $pedido->numpedido = $sigPed;
                            $pedido->subtotal  = $params_array['subtotal'];
                            $pedido->iva       = $params_array['iva'];
                            $pedido->iva0      = $params_array['iva0'];
                            $pedido->total     = $params_array['total'];
                            
                            $pedido->longitud  = $params_array['long'];
                            $pedido->latitud   = $params_array['lat'];
                            $pedido->referencia  = $params_array['referencia'];

                            $pedido->condicion = 1;                
                            $pedido->iduser    = $userLog->sub;
                            $pedido->save();

                            $detalles = $params_array['detalles'];
                                           //$detalles as $key => $value
                                    foreach ($detalles as $key => $det) 
                                    {
                                        $detalle = new DetallePedidoCliente();
                                        $detalle->idpedido  = $pedido->id;
                                        //$detalle->idproducto = $det['idproducto']; 
                                        $detalle->idproducto = $det['id']; 
                                        
                                            $producto = Producto::where('id','=',$detalle->idproducto)->first();
                                            if($producto->stock <= $producto->stockminimo && $prodveri < 1)
                                            {
                                              //event(new StockEvent($producto));
                                              $prodveri++;
                                            }

                                        $detalle->cantidad = $det['cantidad']; 
                                        $detalle->precio = $det['precio']; 
                                        $detalle->save();
                                    }

                                    //event(new VentaEvent($venta));


                                    $data = [ 'code'  =>200,
                                              'status'=>'success',
                                              'Pedido'=>$pedido  //, 'Detalle'=>$detalle
                                            ];
                            DB::commit();   
             

                        }catch(Exception $e){
                            DB::rollback();
                            $data = [ 'code'  =>400,
                                      'status'=>'error',
                                      'message'=> $e];
                        }

                }else{
                        $data = [ 'code'  =>400,
                                  'status'=>'error',
                                  'message'=> 'Sin datos'];
                }
          
        }else{
               $data = ['code'  =>400,
                        'status'=>'error',
                        'message'=> 'Usuario no identificado'];

        }


        return response()->json($data, $data['code']);
    }



    public function destroy(Request $request, $id)
    {
         $pedido = PedidoCliente::find($id);
          if(!empty($pedido)){
              $pedido->condicion = 0;
              $pedido->update();
             
              $data = [ 'code'  => 200,
                        'status'=>'success',
                      ];
          }else{
              $data = [ 'code'  => 400,
                        'status'=>'error',
                      ];              
          }
           return response()->json($data, $data['code']);
    }


    public function show($id)
    {

        $pedido = PedidoCliente::join('users','pedidocliente.iduser','=','users.id')
                            ->select( 'pedidocliente.id','pedidocliente.fecha','pedidocliente.numpedido',
                                      'pedidocliente.subtotal','pedidocliente.iva','pedidocliente.iva0','pedidocliente.iva0',
                                      'pedidocliente.total','pedidocliente.condicion',
                                      'pedidocliente.referencia','pedidocliente.longitud','pedidocliente.latitud',
                                      'users.name as user', 'users.id as idusers','users.role' )
                            
                          ->where('pedidocliente.id','=', $id)  
                          ->first();
   
        $detalles = DetallePedidoCliente::join('producto','detallepedidocliente.idproducto','=','producto.id')
                          
                          ->select('producto.nombre as producto',

                                   'detallepedidocliente.id', 'detallepedidocliente.idpedido', 
                                   'detallepedidocliente.idproducto',
                                   'detallepedidocliente.cantidad', 'detallepedidocliente.precio')
                          
                          ->where('detallepedidocliente.idpedido', '=', $id)
                          ->get();


        return response()->json(['code'      => 200,
                       'status'    => 'success',
                       'Pedido'   => $pedido,
                       'Detalles'  => $detalles ], 200);
    }

    

}
