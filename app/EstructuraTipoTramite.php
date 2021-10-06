<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstructuraTipoTramite extends Model
{
    protected $table = 'EstructuraTipoTramite';
    protected $primaryKey  = 'idestructura_tipo_tramite';
    public $timestamps = false;

    public function estructura_documento(){
        return $this->hasOne('App\EstructuraDocumento','idestructura_documento','idestructura_documento');
    }

}
