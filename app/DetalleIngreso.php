<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleIngreso extends Model
{
     protected $table = 'detalleingreso';
     protected $primaryKey = 'id';
	 protected $fillable = ['idingreso','idproducto',
	 						'cantidad','precio'];
	 public  $timestamps= false;	


     public function ingreso(){
 	    return $this->belongsTo('App\Ingreso');
     }

     public function producto(){
 	    return $this->belongsTo('App\Producto');
     }

}
