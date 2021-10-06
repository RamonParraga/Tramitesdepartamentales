<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoFPGestionModel extends Model
{
    protected $table = 'tipofp_gestion';
    protected $primaryKey  = 'idtipoFP_gestion';
    public $timestamps = false;

    public function gestion(){
        return $this->belongsTo('App\GestionModel','idgestion','idgestion');
    }

    public function tipoFP(){
        return $this->belongsTo('App\TipoFPModel','idtipoFP','idtipoFP');
    }
}
