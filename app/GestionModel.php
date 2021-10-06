<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GestionModel extends Model
{
    protected $table = 'gestion';
    protected $primaryKey  = 'idgestion';
    public $timestamps = false;

    public function TipoFPGestion(){
        return $this->hasMany('App\TipoFPGestionModel','idgestion','idgestion')->with('tipoFP');
    }
}
