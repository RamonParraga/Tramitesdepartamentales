<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class td_TipoDocumentoModel extends Model
{
    //
    protected $table = 'td_tipo_documento';
    protected $primaryKey  = 'idtipo_documento';
    protected $appends = ['idtipo_documento_encrypt'];
    public $timestamps = false;

    public function getIdtipoDocumentoEncryptAttribute()
    {
        return encrypt($this->attributes['idtipo_documento']);
    }

    public function estructura_documento(){
        return $this->hasMany('App\td_EstructuraDocumentoModel','idtipo_documento','idtipo_documento');
    }

    public function tipo_documento_flujo(){
        return $this->hasMany('App\td_TipoDocumentoFlujoModel','idtipo_documento','idtipo_documento');
    }
}
