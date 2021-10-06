<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_BodegaModel extends Model
{
    //
    protected $table = 'td_bodega';
    protected $primaryKey  = 'id_bodega';
    
    public $timestamps = false;

    

    public function departamento(){
        return $this->belongsTo('App\DepartamentoModel','iddepartamento','iddepartamento');
    }

    public function sector(){
        return $this->hasMany('App\td_SectorModel','id_sector','id_sector');
    }

    
}
