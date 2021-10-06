<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_GestionArchivoModel extends Model
{
    protected $table = 'td_gestion_archivo';
    protected $primaryKey  = 'id_gestion_archivo';
    protected $appends = ['id_gestion_archivo_encrypt'];
    public $timestamps = false;

    public function getIdGestionArchivoEncryptAttribute()
    {
        return encrypt($this->attributes['id_gestion_archivo']);
    }
   
   /* public function tramitedoc(){
        return $this->hasMany('App\td_TramiteModel', 'idtramite', 'idtramite');
    }
*/
      public function tramitedoc(){
        return $this->belongsTo('App\td_TramiteModel', 'idtramite', 'idtramite')->with('detalle_tramite');
    }

    public function seccion(){
        return $this->belongsTo('App\td_SeccionModel', 'id_seccion', 'id_seccion')->with('sector');
    }


}

