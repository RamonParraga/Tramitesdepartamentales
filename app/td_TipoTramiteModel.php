<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_TipoTramiteModel extends Model
{
    //
    protected $table = 'td_tipo_tramite';
    protected $primaryKey  = 'idtipo_tramite';
    protected $appends = ['idtipo_tramite_encrypt'];
    public $timestamps = false;

    public function getIdtipoTramiteEncryptAttribute()
    {
        return encrypt($this->attributes['idtipo_tramite']);
    }
    
    public function estructura_tipo_tramite(){
        return $this->hasMany('App\EstructuraTipoTramite','idtipo_tramite','idtipo_tramite')->with('estructura_documento');
    }

    public function tipotramite_departamento(){
        return $this->hasMany('App\td_TipoTramiteDepartamentoModel', 'idtipo_tramite', 'idtipo_tramite')->with('departamentotramite');
    }
    //  public function tramite(){
    //     return $this->hasMany('App\td_TramiteModel', 'idtramite', 'idtramite');
    // }

    public function flujo_general(){
        return $this->hasMany('App\td_FlujoModel', 'idtipo_tramite', 'idtipo_tramite')->where('tipo_flujo','G')->with('departamento'); // solo los flujos generales
    }
}
