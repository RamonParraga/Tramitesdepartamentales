<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoFPModel extends Model
{
    protected $table = 'tipofp';
    protected $primaryKey  = 'idtipoFP';
    public $timestamps = false;

    public function tipoFPGestion(){
        return $this->hasMany('App\TipoFPGestionModel','idtipoFP','idtipoFP')->with('gestion');
    }
    public function requisitos(){
        return $this->hasMany('App\RequisitosModel','idtipoFP','idtipoFP')->with('CertificadoRequisitos');
    }
}

