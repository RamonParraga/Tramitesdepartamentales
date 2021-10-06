<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametrosGeneralesModel extends Model
{
    protected $table = 'td_parametros_generales';
    protected $primaryKey  = 'idparametros_generales';
    protected $appends = ['idparametros_generales_encrypt'];
    protected $hidden = ['idparametros_generales'];
    public $timestamps = false;

    public function getIdparametrosGeneralesEncryptAttribute()
    {
        return encrypt($this->attributes['idparametros_generales']);
    }
}
