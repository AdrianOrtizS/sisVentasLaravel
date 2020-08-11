<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Ingreso;
use App\Producto;
use App\DetalleIngreso;
use Carbon\Carbon;
use App\Notifications\IngresoNotification;
use App\Events\IngresoEvent;
use App\Events\StockEvent;

class IngresoController extends Controller
{

    public function __construct( )
    {

    }

    public function ingresosPdf(Request $request)
    {
              $fechaini   = $request->fechaini;
              $fechafin   = $request->fechafin;

              $fechaini = substr($fechaini, 1, -1);
              $fechafin = substr($fechafin, 1, -1);

              if(empty($fechaini) || empty($fechafin)){
                   $ingreso = Ingreso::join('proveedor','ingreso.idproveedor','=','proveedor.id')
                            ->join('users','ingreso.iduser','=','users.id')
                            ->select('proveedor.id','proveedor.nombre','ingreso.id',
                                'ingreso.fecha','ingreso.numcomprobante','ingreso.created_at',
                                'ingreso.subtotal','ingreso.iva','ingreso.total','ingreso.condicion',
                                'users.name as user','users.surname', 'users.id as idusers','users.role' )
                            ->where('ingreso.condicion','=','1')
                            ->orderBy('ingreso.id','desc')
                            ->get();
                    $cont = $ingreso->count();
                    $subtotal = Ingreso::where('ingreso.condicion','=','1')
                                    ->sum('ingreso.subtotal');
                    $total = Ingreso::where('ingreso.condicion','=','1')
                                    ->sum('ingreso.total');


              }else{

                   $ingreso = Ingreso::join('proveedor','ingreso.idproveedor','=','proveedor.id')
                            ->join('users','ingreso.iduser','=','users.id')
                            ->select('proveedor.id','proveedor.nombre','ingreso.id',
                                'ingreso.fecha','ingreso.numcomprobante','ingreso.created_at',
                                'ingreso.subtotal','ingreso.iva','ingreso.total','ingreso.condicion',
                                'users.name as user','users.surname', 'users.id as idusers','users.role' )
                            ->where('ingreso.condicion','=','1')
                            ->where('ingreso.fecha', '>=', $fechaini)
                            ->where('ingreso.fecha', '<=', $fechafin)
                            ->orderBy('ingreso.id','desc')
                            ->get();
                    $cont = $ingreso->count();       
                    $subtotal = Ingreso::where('ingreso.condicion','=','1')
                                    ->where('ingreso.fecha', '>=', $fechaini)
                                    ->where('ingreso.fecha', '<=', $fechafin)
                                    ->sum('ingreso.subtotal');
                    $total = Ingreso::where('ingreso.condicion','=','1')
                                    ->where('ingreso.fecha', '>=', $fechaini)
                                    ->where('ingreso.fecha', '<=', $fechafin)
                                    ->sum('ingreso.total');
              }

          $pdf = \PDF::loadView('pdf.ingresopdf',['ingreso'=>$ingreso, 'cont'=>$cont, 'subtotal'=>$subtotal, 'total'=>$total]);
          
          return $pdf->download('ingreso.pdf');
    }



    public function buscaringreso(Request $request)
    {
        $buscar   = $request->buscar;

        if($buscar == ''){
         $ingresos = Ingreso::join('proveedor','ingreso.idproveedor','=','proveedor.id')
                            ->join('users','ingreso.iduser','=','users.id')
                            ->select('proveedor.id','proveedor.nombre','ingreso.id',
                                'ingreso.fecha','ingreso.numcomprobante',
                                'ingreso.subtotal','ingreso.iva','ingreso.total','ingreso.condicion',
                                'users.name as user','users.surname', 'users.id as idusers','users.role' )
                            ->orderBy('ingreso.id','desc')
                            ->get();
        }else{
            $ingresos = Ingreso::join('proveedor','ingreso.idproveedor','=','proveedor.id')
                                ->join('users','ingreso.iduser','=','users.id')
                                ->select('proveedor.id','proveedor.nombre','ingreso.id',
                                      'ingreso.fecha','ingreso.numcomprobante',
                                      'ingreso.subtotal','ingreso.iva','ingreso.total','ingreso.condicion',
                                      'users.name as user','users.surname', 'users.id as idusers','users.role' )
                               ->where('proveedor.nombre', 'like', '%'.$buscar.'%')
                               ->orWhere('ingreso.numcomprobante', 'like', '%'.$buscar.'%')
                               ->orWhere('users.name', 'like', '%'.$buscar.'%')

                               ->get();
        }
        return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Ingresos' => $ingresos], 200);
    } 




    public function index()
    {
            $ingresos = Ingreso::join('proveedor','ingreso.idproveedor','=','proveedor.id')
                         ->join('users','ingreso.iduser','=','users.id')
                       ->select('proveedor.id','proveedor.nombre','ingreso.id',
                                'ingreso.fecha','ingreso.numcomprobante',
                                'ingreso.subtotal','ingreso.iva','ingreso.total','ingreso.condicion',
                                'users.name as user','users.surname', 'users.id as idusers','users.role' )
                       ->orderBy('ingreso.id','desc')
                       ->get();


        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'Ingresos'  => $ingresos], 200);
    }



    public function store(Request $request)
    {

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        //comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  //TRUE O FALSE

        if($checkToken) 
        {
                if(!empty($params_array))
                {
                      //sacar usuario identificado
                      $userLog = $jwtAuth->checkToken($token, true);
                      $prodveri=0;
                      
                      try{
                            DB::beginTransaction();
                            $mytime = Carbon::now('America/Lima');
                            $ingreso = new Ingreso();
                            $ingreso->idproveedor = $params_array['idproveedor'];
                            $ingreso->fecha = $params_array['fecha'];
                            $ingreso->numcomprobante = $params_array['numcomprobante'];
                            $ingreso->subtotal= $params_array['subtotal'];
                            $ingreso->iva= $params_array['iva'];
                            $ingreso->total= $params_array['total'];
                            $ingreso->condicion= 1;                
                            $ingreso->iduser = $userLog->sub;
                            $ingreso->save();

                            $detalles = $params_array['detalles'];
                                           //$detalles as $key => $value
                                    foreach ($detalles as $key => $det) {
                                        
                                        $detalle = new DetalleIngreso();
                                        $detalle->idingreso  = $ingreso->id;
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
                                    }     //$user = User::find($userLog->sub);


                                    event(new IngresoEvent($ingreso));

                      
                            $data = [ 'code'  =>200,
                                      'status'=>'success',
                                      'Ingreso'=>$ingreso
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
      $ingreso = Ingreso::join('proveedor','ingreso.idproveedor','=','proveedor.id')
                               ->join('users','ingreso.iduser','=','users.id')
                               ->select('proveedor.id as idproveedor','proveedor.nombre as proveedor',
                                  'ingreso.id','ingreso.subtotal','ingreso.iva',
                                  'ingreso.fecha','ingreso.total','ingreso.numcomprobante',
                                  'users.name', 'users.surname','users.id as idusers','users.role' )
                         ->where('ingreso.id', '=', $id)
                         ->first();
   
      $detalles = DetalleIngreso::join('producto','detalleingreso.idproducto','=','producto.id')
                        ->select('producto.id as idproducto','producto.nombre as producto',
                                 'detalleingreso.id','detalleingreso.idingreso','detalleingreso.idproducto','detalleingreso.cantidad','detalleingreso.precio')
                        ->where('detalleingreso.idingreso', '=', $id)
                        ->get();

    
        if(is_object($ingreso) && is_object($detalles)){
            $data = ['code' => 200,
                     'status' => 'success',
                     'Ingreso' => $ingreso,
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
         $ingreso = Ingreso::find($id);
          if(!empty($ingreso)){
              $ingreso->condicion = 0;
              $ingreso->update();
             
              $data = [ 'code'  => 200,
                        'status'=>'success'
                      ];
          }else{
              $data = [ 'code'  => 400,
                        'status'=>'error'
                      ];              
          }
           return response()->json($data, $data['code']);
    }





}
