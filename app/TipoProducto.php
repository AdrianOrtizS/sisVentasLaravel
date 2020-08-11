<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    protected $table = 'tipoproducto';
     protected $primaryKey = 'id';
	 protected $fillable = ['descripcion','condicion'];


	public function producto(){
	    return $this-hasMany('App\Producto');
	}

}
