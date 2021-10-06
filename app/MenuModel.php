<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuModel extends Model
{
    protected $table = 'menu';
    protected $primaryKey  = 'idmenu';
    public $timestamps = false;

    public function gestion(){
        return $this->hasMany('App\GestionModel','idmenu','idmenu')->with('TipoFPGestion');
    }
}
