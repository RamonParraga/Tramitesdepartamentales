<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoUsuarioModel extends Model
{
    protected $table = 'tipoUsuario';
    protected $primaryKey  = 'idtipoUsuario';
    public $timestamps = false;
}
