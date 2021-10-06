<?php

namespace App\Http\Controllers;
use App\User;
use App\td_DetalleTramiteModel;
use App\ParametrosGeneralesModel;
use Log;
use Illuminate\Http\Request;

class EstadisticaController extends Controller
{

    //estadisticas individual
    public function porUsuario(){
        try {

            $numDecimales = ParametrosGeneralesModel::where('codigo',"NUMDECIMAL")->pluck("valor")->first();
            if(is_null($numDecimales)){ $numDecimales = 2; }
            $iddepartamentoLogueado = departamentoLogueado()["iddepartamento"];

            if($iddepartamentoLogueado==0){ //es administrador o no tiene rol asignado
                return redirect('home')->with([
                    "mensajeGeneral"=>"Este módulo solo puede ser utlizado por los jefes de los departamentos.",
                    "status"=>"info"
                ]); 
            }

            //buscamos los usuarios del departamento
            $listUsuariosDepa = User::with(['us001_tpofp', 'detalle_tramite'=> function($query_detalle_tramite) use ($iddepartamentoLogueado){
                    $query_detalle_tramite->where("iddepartamento_atiende", $iddepartamentoLogueado);
                }])
                ->whereHas('us001_tpofp', function($query_ustfp) use ($iddepartamentoLogueado){ // solo en el departamenot logueado
                    $query_ustfp->where('iddepartamento', $iddepartamentoLogueado);
                })
                ->get();
            
            if(sizeof($listUsuariosDepa)==0){ goto FINALDELTODO; }

            $listTiemMedioTram = [];

            //obtenemos la media de atencion por cada usuario
            foreach ($listUsuariosDepa as $u => $user){
                $sumaDias = 0;
                $sumaHoras = 0;
                $n = sizeof($user->detalle_tramite);
                foreach ($user->detalle_tramite as $dt => $detalle_tramite){

                    #obtenemos los dias pasados entre 2 fechas
                    $date1 = $detalle_tramite->fecha;
                    $date2 = $detalle_tramite->fechaAtiende;
                    $timestamp1 = strtotime($date1);
                    $timestamp2 = strtotime($date2);
                    $hour = abs($timestamp2 - $timestamp1)/(60*60);
                    $dias = $hour/24;

                    $sumaHoras = $sumaHoras + $hour;
                    $sumaDias = $sumaDias + $dias;
                }

                $tiempo = 0;
                $horas = 0;
                if($sumaDias>0){ $tiempo = ($sumaDias/$n); $horas = $sumaHoras/$n; }
                
                //solo dejamos los decimales deceados (en parametros generales)
                $tiempo = number_format($tiempo,$numDecimales);
                $horas = number_format($horas,$numDecimales);

                $data = [
                    "usuario" => $user->name,
                    "tiempo_medio_tramite" => $tiempo,
                    "hora_medio_tramite" => $horas,
                    "cantidad_tramites" => $n
                ];
                
                array_push($listTiemMedioTram, $data);
                
            }
            
            FINALDELTODO:
            return view('tramitesDepartamentales.estadistica.porUsuario')->with([
                'listUsuariosDepa' => $listUsuariosDepa,
                'listTiemMedioTram' => json_encode($listTiemMedioTram)
            ]); 
                      
        } catch (\Throwable $th) {
            Log::error("EstadisticaController => index => Mensaje => ".$th->getMessage());
            return view("error");
        }
        

    }


    //funcion para filtrar estadisticas
    public function filtrarEstadisticas($idus001, $fechaInicio, $fechaFin){

        try {

            $idus001 = decrypt($idus001);
            
            $numDecimales = ParametrosGeneralesModel::where('codigo',"NUMDECIMAL")->pluck("valor")->first();
            if(is_null($numDecimales)){ $numDecimales = 2; }
            $iddepartamentoLogueado = departamentoLogueado()["iddepartamento"];

            $filtroUsuario = "<>";
            $filtroFI = "<>";
            $filtroFF = "<>";
            if($idus001 != 0) { $filtroUsuario="="; }
            if($fechaInicio!=0 && $fechaFin!=0 ) { $filtroFI=">="; $filtroFF="<="; };

            //buscamos los usuarios del departamento
            $listUsuariosDepa = User::with(['us001_tpofp', 'detalle_tramite' => function($query_detalle_tramite) use ($fechaInicio, $fechaFin, $filtroFI, $filtroFF, $iddepartamentoLogueado){
                    $query_detalle_tramite->where("iddepartamento_atiende", $iddepartamentoLogueado)
                        ->where("fechaAtiende", $filtroFI, $fechaInicio)
                        ->where("fechaAtiende", $filtroFF, $fechaFin);
                }])
                ->where('idus001',$filtroUsuario, $idus001)
                ->whereHas('us001_tpofp', function($query_ustfp) use ($iddepartamentoLogueado){
                    $query_ustfp->where('iddepartamento', $iddepartamentoLogueado);
                })
                ->get();
                
            if(sizeof($listUsuariosDepa)==0){ goto FINALDELTODO; }

            $listTiemMedioTram = [];

            //obtenemos la media de atencion por cada usuario
            foreach ($listUsuariosDepa as $u => $user){
                $sumaDias = 0;
                $sumaHoras = 0;
                $n = sizeof($user->detalle_tramite);
                foreach ($user->detalle_tramite as $dt => $detalle_tramite){
                    #obtenemos los dias pasados entre 2 fechas
                    $date1 = $detalle_tramite->fecha;
                    $date2 = $detalle_tramite->fechaAtiende;
                    $timestamp1 = strtotime($date1);
                    $timestamp2 = strtotime($date2);
                    $hour = abs($timestamp2 - $timestamp1)/(60*60);
                    $dias = $hour/24;

                    $sumaHoras = $sumaHoras + $hour;
                    $sumaDias = $sumaDias + $dias;
                }

                $tiempo = 0;
                $horas = 0;
                if($sumaDias>0){ $tiempo = ($sumaDias/$n); $horas = $sumaHoras/$n; }
                
                //solo dejamos los decimales deceados (en parametros generales)
                $tiempo = number_format($tiempo,$numDecimales);
                $horas = number_format($horas,$numDecimales);

                $data = [
                    "usuario" => $user->name,
                    "tiempo_medio_tramite" => $tiempo,
                    "hora_medio_tramite" => $horas,
                    "cantidad_tramites" => $n
                ];

                array_push($listTiemMedioTram, $data);
            }

            FINALDELTODO:

            return response()->json([
                "error" => false,
                "resultado" => $listTiemMedioTram
            ]);

        } catch (\Throwable $th) {
            Log::error("EstadisticaController => filtrarEstadisticas => Mensaje => ".$th->getMessage());
            return response()->json([
                "error" => true,
                "mensaje" => "Datos no encontrados",
                "resultado" => []
            ]);
        }


    }



}
