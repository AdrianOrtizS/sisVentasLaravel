<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
     protected $table = 'configuracion';
     protected $primaryKey = 'id';
	 protected $fillable = ['descripcion','valor'];

}
