<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_EstructuraDocumentoModel extends Model
{
    //
    protected $table = 'td_estructura_documento';
    protected $primaryKey  = 'idestructura_documento';
    public $timestamps = false;

    public function tipo_documento(){
        return $this->belongsTo('App\td_TipoDocumentoModel','idtipo_documento', 'idtipo_documento');
    }

    public function departamento(){
        return $this->belongsTo('App\DepartamentoModel', 'iddepartamento', 'iddepartamento');
    }
}
