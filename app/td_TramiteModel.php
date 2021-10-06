<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_TramiteModel extends Model
{
    //
    protected $table = 'td_tramite';
    protected $primaryKey  = 'idtramite';
    protected $appends = ['idtramite_encrypt'];
    public $timestamps = false;

    public function getIdtramiteEncryptAttribute(){
        return encrypt($this->attributes['idtramite']);
    }

    public function departamento_genera(){
        return $this->belongsTo('App\DepartamentoModel', 'iddepartamento_genera', 'iddepartamento');
    }

    public function detalle_tramite(){
        return $this->hasMany('App\td_DetalleTramiteModel', 'idtramite', 'idtramite')->with('destino', 'departamento_origen','documento');
    }

    public function tipo_tramite(){
        return $this->belongsTo('App\td_TipoTramiteModel', 'idtipo_tramite', 'idtipo_tramite');
    }

    public function prioridad(){
        return $this->belongsTo('App\td_PrioridadTramiteModel', 'idprioridad_tramite', 'idprioridad_tramite');
    }

    public function gestion_archivo(){
        return $this->hasMany('App\td_GestionArchivoModel', 'idtramite', 'idtramite')->with('seccion');
    }

   
}
