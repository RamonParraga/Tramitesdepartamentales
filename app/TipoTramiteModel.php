<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoTramiteModel extends Model
{
    protected $table = 'td_tipo_tramite';
    protected $primaryKey  = 'idtipo_tramite';
    public $timestamps = false;
    
    public function estructura_tipo_tramite(){
        return $this->hasMany('App\EstructuraTipoTramite','idtipo_tramite','idtipo_tramite')->with('estructura_documento');
    }



    // public function destino(){
    //     return $this->hasOne('App\Destino','idTipoTramite','idTipoTramite')->with('detalle_tramite');
    // }
    // public function tramite(){
    //     return $this->hasMany('App\Tramite', 'idTipoTramite', 'idTipoTramite');
    // }
   

}
