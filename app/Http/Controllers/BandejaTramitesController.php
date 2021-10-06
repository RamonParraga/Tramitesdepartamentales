<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\td_DestinoModel;
use App\td_TipoTramiteModel;
use App\DepartamentoModel;
use App\td_TramiteModel;
use App\td_DetalleTramiteModel;
use App\ParametrosGeneralesModel;
use App\Http\Controllers\TdTramiteController;
use Log;
class BandejaTramitesController extends Controller
{

    // -------- funciones para la bandeja de entrada

        public function bandejaEntrada()
        {
            $listTipoTramite = td_TipoTramiteModel::all();
            $listDepartamento = DepartamentoModel::with('periodo')
                ->whereHas('periodo', function($query_periodo){
                    $query_periodo->where("estado", "A");
                })->get();

            $listaTramite = td_DestinoModel::with('detalle_tramite')
                ->whereHas('detalle_tramite', function($query_det_tram){
                    $query_det_tram->whereHas('tramite', function($query_tramite){
                        $query_tramite->where('finalizado', "0");
                    });
                })
                ->where('iddepartamento', departamentoLogueado()['iddepartamento'])
                ->where("estado", "P")
                ->get();

            return view('tramitesDepartamentales.bandejaTramite.bandejaEntrada')->with([
                'listaTramite'=>$listaTramite,
                'listTipoTramite'=>$listTipoTramite,
                'listDepartamento'=>$listDepartamento
            ]);
        }


        // funcion para filtrar la bandeja de entrada con jquery
        public function filtrarEntrada($iddepartamento, $idtipo_tramite)
        {
            try{
                
                $iddepartamento = decrypt($iddepartamento);
                $idtipo_tramite = decrypt($idtipo_tramite);

                $filtroDep = "=";
                $filtroTipTram = "=";

                if($iddepartamento == 0){ $filtroDep = "!="; } // para que busque por todos los departamentos
                if($idtipo_tramite == 0){ $filtroTipTram = "!="; } // para que busque por to

                $listaTramite = td_DestinoModel::with('detalle_tramite')
                ->where('iddepartamento', departamentoLogueado()['iddepartamento'])
                ->where("estado", "P")
                ->whereHas("detalle_tramite", function($query_destino) use($iddepartamento, $idtipo_tramite, $filtroDep, $filtroTipTram){ // filtramos el detalle tramite
                    $query_destino->where("iddepartamento_origen",$filtroDep,$iddepartamento) // filtramos por departamentos
                                  ->whereHas('tramite', function($query_tramite) use($idtipo_tramite, $filtroTipTram){
                                        $query_tramite->where("idtipo_tramite",$filtroTipTram, $idtipo_tramite) // filtramos por tipo de trámite
                                                      ->where("finalizado", "0"); // solo los tramites que no estan finalizados
                                  });
                })
                ->orderBy('iddestino', "DESC")
                ->get();
    
                return response()->json([
                    'error' => false,
                    'resultado' => $listaTramite
                ]);

            }catch(\Throwable $th){

                Log::error("BandejaTramiteController => filtrarEntrada => Mensaje:".$th->getMessage());
                return response()->json([
                    'error' => true,
                    'mensaje' => "Error al realizar la consulta de trámites"
                ]);

            }
        }

    // ---------------------------------------

    public function enElaboracion()
    {
        $listaTramite = td_DetalleTramiteModel::with('tramite')
            ->where('iddepartamento_origen', departamentoLogueado()['iddepartamento'])
            ->where("estado", "B")
            ->get();

        return view("tramitesDepartamentales.bandejaTramite.bandejaElaboracion")->with([
            'listaTramite' => $listaTramite
        ]);
    }

    public function aprobarEnvio()
    {

        $listaTramite = td_DetalleTramiteModel::with('tramite')
            ->where('iddepartamento_origen', departamentoLogueado()['iddepartamento'])
            ->where("estado", "T")
            ->where("aprobado",0)
            ->get();
            
        return view("tramitesDepartamentales.bandejaTramite.bandejaAprobarEnvio")->with([
            'listaTramite' => $listaTramite
        ]);
    }

    public function enRevision()
    {
        $listaTramite = td_DetalleTramiteModel::with('tramite')
        ->where('iddepartamento_origen', departamentoLogueado()['iddepartamento'])
        ->where("estado", "R")
        ->where("aprobado",0)
        ->get();

        return view("tramitesDepartamentales.bandejaTramite.bandejaEnRevision")->with([
            'listaTramite' => $listaTramite
        ]);
    }
    
    // -------- funciones para la bandeja de trámites atendidos y enviados ---------------

        public function atendidosEnviados()
        {
                $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];
                $limiteBandeja = ParametrosGeneralesModel::where("codigo","LIMBAND")->pluck("valor")->first();
                if(is_null($limiteBandeja)){ $limiteBandeja=100; };

                $listTipoTramite = td_TipoTramiteModel::all();
                $listDepartamento = DepartamentoModel::with('periodo')
                    ->whereHas('periodo', function($query_periodo){
                        $query_periodo->where("estado", "A");
                    })->get();

                $listaDetalleTramite = td_DetalleTramiteModel::with("tramite", "departamento_origen", "destino")
                    ->where("iddepartamento_origen", $iddepartamentoLogueado)
                    ->where("estado","<>", "F")
                    ->orderBy("fecha", "DESC")
                    ->take($limiteBandeja)
                    ->get();

                return view('tramitesDepartamentales.bandejaTramite.bandejaAtendidosEnviados')->with([
                    'listaDetalleTramite'=>$listaDetalleTramite,
                    'listTipoTramite'=>$listTipoTramite,
                    'listDepartamento'=>$listDepartamento,
                    'limiteBandeja'=>$limiteBandeja
                ]);

        }


        public function filtrarAtendidoEnviado($iddepartamento, $idtipo_tramite, $nivelAtencion)
        {
            try{

                $limiteBandeja = ParametrosGeneralesModel::where("codigo","LIMBAND")->pluck("valor")->first();
                if(is_null($limiteBandeja)){ $limiteBandeja=100; };
                
                $iddepartamento = decrypt($iddepartamento);
                $idtipo_tramite = decrypt($idtipo_tramite);
                $nivelAtencion = decrypt($nivelAtencion);
                $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];

                $filtroDep = "=";
                $filtroTipTram = "=";
                $filtroNivAtencion = ">"; // si biene cero (buscamos los nivelAtencion mayor a cero, osea todos)
                if($nivelAtencion=="A"){ $nivelAtencion = 1; } // los atendidos (buscamos los nivelAtencion mayores a uno)
                else if($nivelAtencion=="E"){ $filtroNivAtencion = "="; $nivelAtencion = 1; } // los enviados (buscamos los nivelAtencion igual a uno)
                
                
                if($iddepartamento == 0){ $filtroDep = "<>"; }
                if($idtipo_tramite == 0){ $filtroTipTram = "<>"; }

                $listaDetalleTramite = td_DetalleTramiteModel::with("tramite", "departamento_origen", "destino")
                    ->where("iddepartamento_origen", $iddepartamentoLogueado)
                    ->where("nivelAtencion", $filtroNivAtencion, $nivelAtencion)
                    ->whereHas("tramite", function($query_tramite) use($filtroTipTram, $idtipo_tramite){
                        $query_tramite->where("idtipo_tramite", $filtroTipTram, $idtipo_tramite);
                    })
                    ->whereHas("destino", function($query_destino) use($filtroDep, $iddepartamento){
                        $query_destino->where("iddepartamento", $filtroDep, $iddepartamento);
                    })
                    ->take($limiteBandeja)
                    ->orderBy("fecha", "DESC")
                    ->get();


                return response()->json([
                    'error' => false,
                    'resultado' => $listaDetalleTramite,
                    'nivelAtencion' => $nivelAtencion,
                    'filtroNivAtencion' => $filtroNivAtencion
                ]);

            }catch(\Throwable $th){

                Log::error("BandejaTramiteController => filtrarAtendidoIniciado => Mensaje:".$th->getMessage());
                return response()->json([
                    'error' => true,
                    'mensaje' => "Error al realizar la consulta de trámites"
                ]);

            }


        }

    // -------- funciones para la bandeja de trámites finalizados ---------------
    
        public function finalizados()
        {   
            
            $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];
            $limiteBandeja = ParametrosGeneralesModel::where("codigo","LIMBAND")->pluck("valor")->first();
            
            if(is_null($limiteBandeja)){ $limiteBandeja=100; };

            $listTipoTramite = td_TipoTramiteModel::all();
            $listDepartamento = DepartamentoModel::with('periodo')
            ->whereHas('periodo', function($query_periodo){
                $query_periodo->where("estado", "A");
            })->get();
            
            $listaDetalleTramite = td_DetalleTramiteModel::with("tramite", "departamento_origen", "detalle_tramite_padre","destino")
                ->where("iddepartamento_origen", $iddepartamentoLogueado)
                ->where('estado', 'F')
                ->orderBy("fecha", "DESC")
                ->take($limiteBandeja)
                ->get();

            // dd($listaDetalleTramite);

            return view('tramitesDepartamentales.bandejaTramite.bandejaFinalizados')->with([
                'listaDetalleTramite'=>$listaDetalleTramite,
                'listTipoTramite'=>$listTipoTramite,
                'listDepartamento'=>$listDepartamento,
                'limiteBandeja'=>$limiteBandeja
            ]);

        }

        public function filtrarFinalizado($iddepartamento, $idtipo_tramite)
        {

            try {

                $limiteBandeja = ParametrosGeneralesModel::where("codigo","LIMBAND")->pluck("valor")->first();
                if(is_null($limiteBandeja)){ $limiteBandeja=100; };

                $iddepartamento = decrypt($iddepartamento);
                $idtipo_tramite = decrypt($idtipo_tramite);
                $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];
                
                $filtroDep = "=";
                $filtroTipTram = "=";                

                if($iddepartamento == 0){ $filtroDep = "!="; } // para que busque por todos los departamentos
                if($idtipo_tramite == 0){ $filtroTipTram = "!="; }                

                $listaDetalleTramite = td_DetalleTramiteModel::with("tramite", "departamento_origen", "detalle_tramite_padre", "destino")
                    ->where("iddepartamento_origen", $iddepartamentoLogueado)
                    ->where('estado', 'F')               
                    ->whereHas("tramite", function($query_tramite) use($filtroTipTram, $idtipo_tramite){
                        $query_tramite->where("idtipo_tramite", $filtroTipTram, $idtipo_tramite);
                    })
                    ->whereHas("detalle_tramite_padre", function($query_det_padre) use($filtroDep, $iddepartamento){
                        $query_det_padre->where("iddepartamento_origen", $filtroDep, $iddepartamento);
                    })
                    ->take($limiteBandeja)
                    ->orderBy("fecha", "DESC")
                    ->get();

                return response()->json([
                    'error' => false,
                    'resultado' => $listaDetalleTramite                
                ]);

            } catch (\Throwable $th) {
                Log::error("BandejaTramitesController => filtrarFinalizado => Mnesaje => ".$th->getMessage());
                return response()->json([
                    'error' => true,
                    'resultado' => []                
                ]);
            }
            
        }     
    


    // --------- funcion para cargar las notificaciones

        public function cargarNotificacionBandejas()
        {
            try{
                
                $numMaxNotif = ParametrosGeneralesModel::where('codigo', "MAXNOTIFI")->pluck('valor')->first();
                if(is_null($numMaxNotif)){ $numMaxNotif = 5; }
                $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];

                #buscamos la info de los ultimos tramites entrantes
                    $listaDestinos = td_DestinoModel::with('detalle_tramite')
                    ->where('iddepartamento', $iddepartamentoLogueado)
                    ->where("estado", "P")
                    ->orderBy('iddestino', "DESC")
                    ->take($numMaxNotif)
                    ->get();

                #contamos todos los tramites no atendiso
                    $totalTramEntrada =td_DestinoModel::with('detalle_tramite')
                        ->where('iddepartamento', $iddepartamentoLogueado)
                        ->where("estado", "P")
                        ->count('iddestino');

                #contamos todos los trámites en borrador
                    $totalTramBorrador = td_DetalleTramiteModel::with('tramite')
                        ->where('iddepartamento_origen', $iddepartamentoLogueado)
                        ->where("estado", "B")
                        ->count('iddetalle_tramite');

                #contamos todos los trámites no aprobados
                    $totalTramAprobar = td_DetalleTramiteModel::with('tramite')
                        ->where('iddepartamento_origen', $iddepartamentoLogueado)
                        ->where("estado", "T")
                        ->where("aprobado",0)
                        ->count('iddetalle_tramite');

                #contamos todos los trámites por revisar
                    $totalTramRevision = td_DetalleTramiteModel::with('tramite')
                        ->where('iddepartamento_origen', $iddepartamentoLogueado)
                        ->where("estado", "R")
                        ->where("aprobado",0)
                        ->count('iddetalle_tramite');
                

                return response()->json([
                    'error' => false,
                    'resultado' => [
                        'listaDestinos' => $listaDestinos,
                        'totalTramEntrada' => $totalTramEntrada,
                        'totalTramBorrador' => $totalTramBorrador,
                        'totalTramAprobar' => $totalTramAprobar,
                        'totalTramRevision' => $totalTramRevision
                    ]
                ]);

            }catch(\Throwable $th){

                Log::error("BandejaTramiteController => cargarNotificacionBandejas => Mensaje:".$th->getMessage());
                return response()->json([
                    'error' => true,
                    'mensaje' => "Error al realizar la consulta de trámites"
                ]);

            }
        }

        public function abrirTetalleTramite($bandeja, $iddetalle_tramite){
            $iddetalle_tramite = decrypt($iddetalle_tramite);

            //verificamos a que bandeja vamos a redireccionar
            switch ($bandeja) {
                case 1: // bandeja de entrada
                    return redirect('/gestionBandeja/entrada')->with(['iddetalle_tramite'=>$iddetalle_tramite]);  
                break;
            }
        }

}
