<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_DetalleTramiteModel extends Model
{
    //
    protected $table = 'td_detalle_tramite';
    protected $primaryKey  = 'iddetalle_tramite';
    protected $appends = ['iddetalle_tramite_encrypt'];
    public $timestamps = false;

    public function getIddetalleTramiteEncryptAttribute(){
        return encrypt($this->attributes['iddetalle_tramite']);
    }

    public function tramite(){
    	return $this->hasOne('App\td_TramiteModel','idtramite','idtramite')->with('tipo_tramite', 'departamento_genera', 'prioridad');
    }

    public function destino(){
        return $this->hasMany('App\td_DestinoModel', 'iddetalle_tramite', 'iddetalle_tramite')->with('departamento', 'detalle_tramite_atendido');
    }

    public function departamento_origen(){
        return $this->belongsTo('App\DepartamentoModel', 'iddepartamento_origen', 'iddepartamento');
    }

    public function documento(){
        return $this->hasMany('App\td_DocumentoModel', 'iddetalle_tramite', 'iddetalle_tramite')->with('tipo_documento','us001_de');
    }

    public function flujo(){
        return $this->belongsTo('App\td_FlujoModel', 'idflujo', 'idflujo')->with('flujo_hijo');
    }

    public function detalle_tramite_padre(){
        return $this->belongsTo('App\td_DetalleTramiteModel', 'iddetalle_tramite_padre', 'iddetalle_tramite')->with('departamento_origen');
    }

    public function destino_atendido(){
        return $this->belongsTo('App\td_DestinoModel', 'iddestino_atendido', 'iddestino');
    }

    // public function detalle_tramite_hijo(){
    //     return $this->hasMary('App\td_DetalleTramiteModel', 'iddetalle_tramite_padre', 'iddetalle_tramite')->with('departamento_origen');
    // }

}
