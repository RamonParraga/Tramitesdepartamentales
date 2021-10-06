<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_DocumentoModel extends Model
{
    //
    protected $table = 'td_documento';
    protected $primaryKey  = 'iddocumento';
    protected $appends = ['iddocumento_encrypt'];
    public $timestamps = false;

    public function getIddocumentoEncryptAttribute()
    {
        return encrypt($this->attributes['iddocumento']);
    }

    public function tipo_documento(){
        return $this->belongsTo('App\td_TipoDocumentoModel','idtipo_documento', 'idtipo_documento');
    }

    public function us001_de(){
        return $this->belongsTo('App\User', 'idus001_de', 'idus001');
    }
}
