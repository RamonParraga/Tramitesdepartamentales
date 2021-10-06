<?php

namespace App\Http\Middleware;

use Closure;
use Log;

class ValidarRutaPorTipoFP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //FP=funcionario publico
        //ADFP=Usuario administrador

        if(auth()->guest()){ // si no hay usuarios logueados no permitimos el acceso a la ruta
            goto NOPERMIRIR;
        }

        $rutaLlamada = \Request::route()->uri; // obtenemos el nombre de la ruta que se esta llamando
        // Log::info("Middleware => ValidarRutaPorTipoFP => Ruta llamada: $rutaLlamada");

        if(userEsTipo('ADFP')){goto PERMITIR;} // preguntamos si es un usuario FP administrador en ese caso permitimos el acceso a la ruta     

        $idtipoFP=auth()->user()->idtipoFP; // si no FP administrador obtenemos el tipo FP del usuario logueado
        $listaRutasAasignadas = \App\TipoFPGestionModel::where('idtipoFP',$idtipoFP)->with('gestion')->get(); // obtenemos todas las gestiones(rutas) que tiene asignadas dicho tipo de FP

        foreach ($listaRutasAasignadas as $r => $gestion){ // recorremos todas las rutas asignadas al usuario
            if($gestion->gestion->ruta==$rutaLlamada){ // si la ruta llamada concuenda con una de las rutas asignadas al usuaro FP, permitimos el acceso
                goto PERMITIR;
            }
        }
        // si no se encuentran coincidencias se redirecciona al login
        NOPERMIRIR:
        return redirect('/login');

        PERMITIR:
        return $next($request);
    }
}
