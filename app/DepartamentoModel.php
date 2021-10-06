<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class DepartamentoModel extends Model
{
    protected $table = 'td_departamento';
    protected $primaryKey  = 'iddepartamento';
    protected $appends = ['iddepartamento_encrypt'];
    public $timestamps = false;

    public function getIddepartamentoEncryptAttribute()
    {
        return encrypt($this->attributes['iddepartamento']);
    }

    public function periodo(){
        return $this->belongsTo('App\PeriodoModel','idperiodo','idperiodo');
    }

    public function flujo(){      
        return $this->belongsTo('App\td_FlujoModel','iddepartamento','iddepartamento');
    }

    public function us001_tipofp(){
        return $this->hasMany('App\td_us001_tipofpModel','iddepartamento','iddepartamento')->with('us001','tipofp','departamento');
    }

    public function jefe_departamento(){ // para obtener los datos solo del jefe del departamento
        return $this->hasMany('App\td_us001_tipofpModel','iddepartamento','iddepartamento')->where('jefe_departamento',1)->with('us001','tipofp');
    }
}
