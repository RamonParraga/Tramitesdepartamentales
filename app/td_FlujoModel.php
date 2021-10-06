<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_FlujoModel extends Model
{
    //
    protected $table = 'td_flujo';
    protected $primaryKey  = 'idflujo';
    protected $appends = ['idflujo_encrypt'];
    public $timestamps = false;

    public function getIdflujoEncryptAttribute()
    {
        return encrypt($this->attributes['idflujo']);
    }

    public function departamento(){
        return $this->hasOne('App\DepartamentoModel','iddepartamento','iddepartamento');
    }

    public function departamento_jefe(){ // para obtener el departamento con el jefe del mismo
        return $this->hasOne('App\DepartamentoModel','iddepartamento','iddepartamento')->with(['us001_tipofp'=>function($query_us001_tipofp){
            $query_us001_tipofp->where("jefe_departamento",1); // solo el jefe del departamento 
        }]);
    }

    public function flujo_padre(){ // para obtener el flujo padre
        return $this->belongsTo('App\td_FlujoModel', 'idflujo_padre', 'idflujo')->with("departamento");
    }

    public function flujo_hijo(){ // para obtener todos los flujos hijos
        return $this->hasMany('App\td_FLujoModel', 'idflujo_padre', 'idflujo')->with(["departamento", "departamento_jefe"]);
    }

    public function flujo_actividad(){
        return $this->hasMany('App\td_FlujoActividadModel',"idflujo", "idflujo")->with("actividad");
    }

    public function tipo_documento_flujo(){
        return $this->hasMany('App\td_TipoDocumentoFlujoModel','idflujo','idflujo')->with("tipo_documento");
    }

}
