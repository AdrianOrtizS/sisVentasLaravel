<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoCliente extends Model
{

     protected $table = 'pedidocliente';
     protected $primaryKey = 'id';
	protected $fillable = ['fecha',
	 						'numpedido','subtotal',
	 						'condicion','iduser','iva','iva0','total',
                                   'longitud','latitud','referencia'];

     // public function proveedor(){
 	   //  return $this->belongsTo('App\Proveedor');
     // }

     public function detallepedidocliente(){
	    return $this->hasMany('App\DetallePedidoCliente');
	}



     public function user(){
 	    return $this->belongsTo('App\User');
     }

}
