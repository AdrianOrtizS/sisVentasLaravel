<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Venta;
use App\DetalleVenta;
use Carbon\Carbon;
use App\Notifications\VentaNotification;
use App\Events\VentaEvent;
use App\Events\StockEvent;
use App\Producto;


class VentaController extends Controller
{

    public function ventasPdf(Request $request)
    {
              $fechaini   = $request->fechaini;
              $fechafin   = $request->fechafin;

              $fechaini = substr($fechaini, 1, -1);
              $fechafin = substr($fechafin, 1, -1);

              if(empty($fechaini) || empty($fechafin)){
                    $ventas = Venta::join('persona','ventas.idpersona','=','persona.id')
                            ->join('users','ventas.iduser','=','users.id')
                            ->select('persona.id','persona.nombre','ventas.id',
                                'ventas.fecha','ventas.numcomprobante',
                                'ventas.subtotal','ventas.iva','ventas.iva0','ventas.descuento','ventas.total','ventas.condicion','ventas.descuento',
                                'users.name as user','users.surname', 'users.id as idusers','users.role' )
                            ->where('ventas.condicion','=','1')
                            ->orderBy('ventas.id','desc')
                            
                            ->get();
                    $cont = $ventas->count();
                    $subtotal = Venta::where('ventas.condicion','=','1')
                                    ->sum('ventas.subtotal');
                    $total = Venta::where('ventas.condicion','=','1')
                                    ->sum('ventas.total');
              }else{


                    $ventas = Venta::join('persona','ventas.idpersona','=','persona.id')
                            ->join('users','ventas.iduser','=','users.id')
                            ->select('persona.id','persona.nombre','ventas.id',
                                'ventas.fecha','ventas.numcomprobante',
                                'ventas.subtotal','ventas.iva','ventas.iva0','ventas.descuento','ventas.total','ventas.condicion','ventas.descuento',
                                'users.name as user','users.surname', 'users.id as idusers','users.role' )
                            ->where('ventas.condicion','=','1')
                            ->where('ventas.fecha', '>=', $fechaini)
                            ->where('ventas.fecha', '<=', $fechafin)
                            ->orderBy('ventas.id','desc')
                            
                            ->get();
                    $cont = $ventas->count();       
                    $subtotal = Venta::where('ventas.condicion','=','1')
                                    ->where('ventas.fecha', '>=', $fechaini)
                                    ->where('ventas.fecha', '<=', $fechafin)
                                    ->sum('ventas.subtotal');
                    $total = Venta::where('ventas.condicion','=','1')
                                    ->where('ventas.fecha', '>=', $fechaini)
                                    ->where('ventas.fecha', '<=', $fechafin)
                                    ->sum('ventas.total');

              }
          $pdf = \PDF::loadView('pdf.ventapdf',['ventas'=>$ventas, 'cont'=>$cont, 'subtotal'=>$subtotal, 'total'=>$total]);
          
          return $pdf->download('ventas.pdf');
    }



    public function buscarventa(Request $request)
    {
        $buscar   = $request->buscar;

        if($buscar == ''){
            $ventas = Venta::join('persona','ventas.idpersona','=','persona.id')
                            ->join('users','ventas.iduser','=','users.id')
                            ->select('persona.id','persona.nombre','ventas.id',
                                'ventas.fecha','ventas.numcomprobante',
                                'ventas.subtotal','ventas.iva','ventas.iva0','ventas.descuento','ventas.total','ventas.condicion',
                                'users.name as user', 'users.id as idusers','users.role' )
                            ->orderBy('ventas.id','desc')
                            ->get();
        }else{
            $ventas = Venta::join('persona','ventas.idpersona','=','persona.id')
                                ->join('users','ventas.iduser','=','users.id')
                                ->select('persona.id','persona.nombre','ventas.id',
                                      'ventas.fecha','ventas.numcomprobante',
                                      'ventas.subtotal','ventas.iva','ventas.iva0','ventas.descuento','ventas.total','ventas.condicion',
                                      'users.name as user', 'users.id as idusers','users.role' )
                               ->where('persona.nombre', 'like', '%'.$buscar.'%')
                               ->orWhere('ventas.numcomprobante', 'like', '%'.$buscar.'%')
                               ->orWhere('users.name', 'like', '%'.$buscar.'%')
                               ->get();
        }
        return response()->json([ 'code'    => 200,
                                  'status'  => 'success',
                                  'Ventas'  => $ventas], 200);
    } 


    public function numcomprobante(Request $request)
    {
         $venta = Venta::all();

         $ult =   $venta->last();
         
         if(empty($ult) || is_null($ult) ){
            $ult['numcomprobante'] = 0;
         }

         $ultimo =  $ult['numcomprobante'];
         $ultimo++;
                        
         return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Numventa' => $ultimo], 200);
    } 


    public function index()
    {
        $ventas = Venta::join('persona','ventas.idpersona','=','persona.id')
                            ->join('users','ventas.iduser','=','users.id')
                           ->select('persona.id','persona.nombre','ventas.id',
                                'ventas.fecha','ventas.numcomprobante',
                                'ventas.subtotal','ventas.descuento','ventas.iva','ventas.iva0','ventas.total','ventas.condicion',
                                'users.name as user', 'users.id as idusers','users.role' )
                       ->orderBy('ventas.id','desc')
                       ->get();
    
        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'Ventas'  => $ventas], 200);
    }



    public function store(Request $request)
    {
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
                      $prodveri=0;

                      try{
                            DB::beginTransaction();
                            $mytime = Carbon::now('America/Guayaquil');
                            $venta = new Venta();
                            $venta->idpersona = $params_array['idpersona'];
                            $venta->fecha     = $mytime;
                            $venta->numcomprobante = $params_array['numcomprobante'];
                            $venta->subtotal  = $params_array['subtotal'];
                            $venta->iva       = $params_array['iva'];
                            $venta->iva0       = $params_array['iva0'];
                            $venta->total     = $params_array['total'];
                            $venta->descuento = $params_array['descuento'];

                            $venta->condicion = 1;                
                            $venta->iduser    = $userLog->sub;
                            $venta->save();

                            $detalles = $params_array['detalles'];
                                           //$detalles as $key => $value
                                    foreach ($detalles as $key => $det) {
                                        
                                        $detalle = new Detalleventa();
                                        $detalle->idventa  = $venta->id;
                                        $detalle->idproducto = $det['idproducto']; 
                                        

                                            $producto = Producto::where('id','=',$detalle->idproducto)->first();
                                            if($producto->stock <= $producto->stockminimo && $prodveri < 1)
                                            {
                                              event(new StockEvent($producto));
                                              $prodveri++;
                                            }



                                        $detalle->cantidad = $det['cantidad']; 
                                        $detalle->precio = $det['precio']; 
                                        $detalle->save();
                                    }

                                    event(new VentaEvent($venta));


                                    $data = [ 'code'  =>200,
                                            'status'=>'success',
                                            'Venta'=>$venta  //, 'Detalle'=>$detalle
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



    public function show($id)
    {
      $venta = Venta::join('persona','ventas.idpersona','=','persona.id')
                               ->join('users','ventas.iduser','=','users.id')
                               ->select('persona.id as idpersona','persona.nombre as persona',
                                  'ventas.id','ventas.fecha','ventas.numcomprobante','ventas.subtotal',
                                  'ventas.iva','ventas.iva0','ventas.total','ventas.descuento',
                                  'users.name','users.surname', 'users.id as idusers','users.role' )
                         ->where('ventas.id', '=', $id)
                         ->first();
   
      $detalles = DetalleVenta::join('producto','detalleventa.idproducto','=','producto.id')
                        ->select('producto.id as idproducto','producto.nombre as producto',
                                 'detalleventa.id','detalleventa.idventa','detalleventa.idproducto','detalleventa.cantidad','detalleventa.precio')
                        ->where('detalleventa.idventa', '=', $id)
                        ->get();

    
        if(is_object($venta) && is_object($detalles)){
            $data = ['code' => 200,
                     'status' => 'success',
                     'Venta' => $venta,
                     'Detalle'=> $detalles];
        }else{
            $data = ['code'=>404,
                     'status'=> 'error',
                     'error' => 'No hay datos'];
        }

        return response()->json($data, $data['code']);
    }



    public function destroy(Request $request, $id)
    {
         $venta = Venta::find($id);
          if(!empty($venta)){
              $venta->condicion = 0;
              $venta->update();
             
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



}

