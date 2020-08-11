<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
     protected $table = 'persona';
     protected $primaryKey = 'id';
	 protected $fillable = [
	 			'nombre', 'direccion', 
	 			'foto','identificador','condicion','telefono','celular'];


	 public function tipopersona(){
 	    return $this->belongsTo('App\TipoPersona');
     }


 public function venta(){
	    return $this-hasMany('App\Venta');
	}


}
