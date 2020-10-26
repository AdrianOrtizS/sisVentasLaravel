<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
     protected $table = 'proveedor';
     protected $primaryKey = 'id';
	 protected $fillable = [
	 			'nombre', 'direccion', 
	 			'foto','identificador','condicion','telefono','celular'];


	public function ingreso(){
	    return $this->hasMany('App\Ingreso');
	}
}
