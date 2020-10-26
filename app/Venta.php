<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
     protected $table = 'ventas';
     protected $primaryKey = 'id';
	 protected $fillable = ['idpersona','fecha',
	 						'numcomprobante','subtotal',
	 						'condicion','iduser','iva','iva0','total','descuento'];


     public function persona(){
 	    return $this->belongsTo('App\Persona');
     }


     public function detalleventa(){
	    return $this->hasMany('App\DetalleVenta');
	 }



     public function user(){
 	    return $this->belongsTo('App\User');
     }


}
