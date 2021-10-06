<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_DestinoModel extends Model
{
    //
    protected $table = 'td_destino';
    protected $primaryKey  = 'iddestino';
    public $timestamps = false;

    public function detalle_tramite()
    {
    	return $this->belongsTo('App\td_DetalleTramiteModel','iddetalle_tramite','iddetalle_tramite')->with('tramite', 'flujo','departamento_origen');
    }

    public function departamento(){
        return $this->belongsTo('App\DepartamentoModel', 'iddepartamento', 'iddepartamento')->with('jefe_departamento');
    }

    public function detalle_tramite_atendido(){
        return $this->hasMany('App\td_DetalleTramiteModel', 'iddestino_atendido', 'iddestino');
    }
}
