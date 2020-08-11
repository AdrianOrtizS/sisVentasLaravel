<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Venta;
use App\Ingreso;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $anio = date('Y');

        $ventas = DB::table('ventas as v')
                  ->select( DB::raw('MONTH(v.fecha) as mes'),
                            DB::raw('YEAR(v.fecha) as anio'),
                            DB::raw('SUM(v.total) as total'))
                  ->where('v.condicion','=','1')
                  ->whereYear('v.fecha',$anio)
                  ->groupBy(DB::raw('MONTH(v.fecha)'),
                            DB::raw('YEAR(v.fecha)'))
                  ->get();



         $ingresos = DB::table('ingreso as i')
                  ->select( DB::raw('MONTH(i.fecha) as mes'),
                            DB::raw('YEAR(i.fecha) as anio'),
                            DB::raw('SUM(i.total) as total'))
                  ->where('i.condicion','=','1')
                  ->whereYear('i.fecha',$anio)
                  ->groupBy(DB::raw('MONTH(i.fecha)'),
                            DB::raw('YEAR(i.fecha)'))
                  ->get();


        return response()->json([ 'code'      => 200,
                                  'status'    => 'success',
                                  'Ingresos' => $ingresos,
                                  'Ventas' => $ventas], 200);
    }



}



