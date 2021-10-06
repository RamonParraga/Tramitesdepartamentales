<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_TipoDocumentoFlujoModel extends Model
{
    //
    protected $table = 'td_tipo_documento_flujo';
    protected $primaryKey  = 'idtipo_documento_flujo';
    public $timestamps = false;

    public function tipo_documento(){
        return $this->hasOne('App\td_TipoDocumentoModel','idtipo_documento','idtipo_documento');
    }
}
