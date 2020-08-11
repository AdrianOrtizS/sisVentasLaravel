<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
     protected $table = 'detalleventa';
     protected $primaryKey = 'id';
	 protected $fillable = ['idventa','idproducto',
	 						'cantidad','precio'];
	 public  $timestamps= false;	


     public function venta(){
 	    return $this->belongsTo('App\Venta');
     }

     public function producto(){
 	    return $this->belongsTo('App\Producto');
     }

}
