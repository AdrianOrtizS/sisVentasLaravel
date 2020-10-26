<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetallePedidoCliente extends Model
{
     protected $table = 'detallepedidocliente';
	 protected $primaryKey = 'id';
	 protected $fillable = ['idpedido','idproducto',
	 						'cantidad','precio'];
	
	 public  $timestamps= false;	


	 public function pedidocliente(){
		    return $this->belongsTo('App\PedidoCliente');
	 }

	 public function producto(){
		    return $this->belongsTo('App\Producto');
	 }
	 
}
