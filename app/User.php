<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'us001';
    protected $primaryKey  = 'idus001';
    protected $appends = ['idus001_encrypt'];

    public function getIdus001EncryptAttribute()
    {
        return encrypt($this->attributes['idus001']);
    }

    public function us001_tpofp(){
        return $this->hasMany('App\td_us001_tipofpModel', 'idus001', 'idus001')->with('tipofp','departamento');
    }

    public function detalle_tramite(){
        return $this->hasMany('App\td_DetalleTramiteModel', 'idus001Atiende', 'idus001');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

}
