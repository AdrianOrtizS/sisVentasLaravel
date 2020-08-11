<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
     protected $table = 'producto';
     protected $primaryKey = 'id';
	 protected $fillable = [
	 			'nombre', 'stock','stockminimo','foto', 
	 			'condicion','idtipo','precio','codigo','iva'];

	  public function tipoproducto(){
 	    return $this->belongsTo('App\TipoProducto');
     }

    public function detalleingreso(){
	    return $this-hasMany('App\DetalleIngreso');
	}


}
