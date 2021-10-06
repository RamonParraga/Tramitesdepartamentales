<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_UsuarioModel extends Model
{
    //
    protected $table = 'td_usuario';
    protected $primaryKey  = 'idusuario';
    public $timestamps = false;
}
