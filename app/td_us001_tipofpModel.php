<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_us001_tipofpModel extends Model
{
    //
    protected $table = 'td_us001_tipofp';
    protected $primaryKey  = 'idus001_tipofp';
    public $timestamps = false;

    public function tipofp(){
        return $this->belongsTo('App\TipoFPModel','idtipoFP','idtipoFP');
    }

    public function departamento(){
        return $this->belongsTo('App\DepartamentoModel','iddepartamento','iddepartamento');
    }

    public function us001(){
        return $this->belongsTo('App\User','idus001','idus001');
    }
}
