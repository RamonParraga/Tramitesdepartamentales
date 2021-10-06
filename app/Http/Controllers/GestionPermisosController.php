<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MenuModel;
use App\GestionModel;
use App\TipoFPModel;
use App\User;
use App\DepartamentoModel;
use App\Us001_tipoUsuarioModel;

use DB;

class GestionPermisosController extends Controller
{
    public function index(){

        // listarMenuSession();

        $listaMenu = MenuModel::all();
        $listagestion = DB::table('gestion')
        ->join('menu','menu.idmenu','=','gestion.idmenu')
        ->select('gestion.*','menu.nombremenu')
        ->get(); 
        $listatipousuarioFP= TipoFPModel::all();
        $listaMenuGestion = MenuModel::with('gestion')->get();
        $listaGestionesAsignadas = TipoFPModel::with('tipoFPGestion')->get();

        // listamos solo a los usuario que son funcionario publico
        // lo obtenemos con los tipos fp que tienen asignados
        $listaFuncionariosPublicos = Us001_tipoUsuarioModel::with(['Usuarios', 'TipoUsuario'])
            ->whereHas('TipoUsuario', function($query){
                $query->where('tipo','FP');
            })->get();
            
        // return $listaFuncionariosPublicos;

        // $listaFuncionariosPublicos = DB::table('us001')
        //                         ->join('us001_tipoUsuario','us001_tipoUsuario.idus001','=','us001.idus001')
        //                         ->join('tipoUsuario','tipoUsuario.idtipousuario','=','us001_tipoUsuario.idtipousuario')
        //                         ->where('tipoUsuario.tipo','FP')
        //                         ->orwhere('tipoUsuario.tipo','ADFP')
        //                         ->select('us001.idus001','us001.ciu', 'us001.cedula', 'us001.name', 'us001.idtipoFP')
        //                         ->groupBy('us001.idus001','us001.ciu', 'us001.cedula', 'us001.name', 'us001.idtipoFP')
        //                         ->orderBy('us001.idus001')
        //                         ->get();

        $listaDepartamentos = DepartamentoModel::with('periodo')
        ->whereHas('periodo', function($periodos){
            $periodos->where('estado',"A");
        })->get();
        
        return view('gestionPermisos.admPermidos')->with([
            'listaMenu'=>$listaMenu,
            'listagestion'=>$listagestion,
            'listatipousuarioFP'=>$listatipousuarioFP,
            'listaMenuGestion'=>$listaMenuGestion,
            'listaGestionesAsignadas'=>$listaGestionesAsignadas,
            'listaFuncionariosPublicos'=>$listaFuncionariosPublicos,
            'listaDepartamentos'=>$listaDepartamentos,
            'inicial'=>true,
        ]);
    }
}
