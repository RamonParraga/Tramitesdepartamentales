<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class td_PrioridadTramiteModel extends Model
{
    //
    protected $table = 'td_prioridad_tramite';
    protected $primaryKey  = 'idprioridad_tramite';
    protected $appends = ['idprioridad_tramite_encrypt'];
    public $timestamps = false;

    public function getIdprioridadTramiteEncryptAttribute()
    {
        return encrypt($this->attributes['idprioridad_tramite']);
    }

    // public function estructura_documento(){
    //     return $this->hasMany('App\td_EstructuraDocumentoModel','idtipo_documento','idtipo_documento');
    // }

    // public function tipo_documento_flujo(){
    //     return $this->hasMany('App\td_TipoDocumentoFlujoModel','idtipo_documento','idtipo_documento');
    // }
}
