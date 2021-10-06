<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_ActividadModel extends Model
{
    //
    protected $table = 'td_actividad';
    protected $primaryKey  = 'idactividad';
    public $timestamps = false;

    public function departamento(){
        return $this->belongsTo('App\DepartamentoModel','iddepartamento','iddepartamento');
    }

    public function flujo_actividad(){
        return $this->hasMany('App\td_FlujoActividadModel','idactividad','idactividad');
    }
}
