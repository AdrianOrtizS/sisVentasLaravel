<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
     protected $table = 'ingreso';
     protected $primaryKey = 'id';
	 protected $fillable = ['idpersona','fecha',
	 						'numcomprobante','subtotal',
	 						'condicion','iduser','iva','total'];


     public function proveedor(){
 	    return $this->belongsTo('App\Proveedor');
     }


     public function detalleingreso(){
	    return $this-hasMany('App\DetalleIngreso');
	 }



     public function user(){
 	    return $this->belongsTo('App\User');
     }


}
