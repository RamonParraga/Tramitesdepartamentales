<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_TipoTramiteDepartamentoModel extends Model
{
    protected $table = 'td_tipotramite_departamento';
    protected $primaryKey  = 'idtipotramite_departamento';
    public $timestamps = false;


     public function tramite(){
        return $this->belongsTo('App\td_TipoTramiteModel', 'idtipotramite', 'idtipotramite');
    }

    public function departamentotramite(){
        return $this->belongsTo('App\DepartamentoModel', 'iddepartamento', 'iddepartamento');
    }
}
