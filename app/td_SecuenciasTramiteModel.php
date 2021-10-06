<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class td_SecuenciasTramiteModel extends Model
{
    protected $table = 'td_secuencias_tramite';
    protected $primaryKey  = 'idsecuencias_tramite';
    public $timestamps = false;

     public function td_prioridad(){
        return $this->hasMany('App\td_PrioridadTramiteModel','idprioridad_tramite','idprioridad_tramite');
    }
    
}
