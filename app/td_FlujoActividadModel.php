<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_FlujoActividadModel extends Model
{
    //
    protected $table = 'td_flujo_actividad';
    protected $primaryKey  = 'idflujo_actividad';
    public $timestamps = false;

    public function actividad(){
        return $this->hasOne('App\td_ActividadModel', "idactividad", "idactividad");
    }
}
