<?php

namespace App\Http\Controllers;

use App\td_FlujoModel;
use Illuminate\Http\Request;
use App\td_TipoTramiteModel;
use App\DepartamentoModel;
use App\td_TipoDocumentoModel;
use App\td_ActividadModel;
use App\td_TipoDocumentoFlujoModel;
use App\td_FlujoActividadModel;
use App\td_us001_tipofpModel;
use DB;
use Log;

class TdFlujoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $obj_tipoTramite = td_TipoTramiteModel::where("estado", 1)->get();
        $obj_tipoDocumetno = td_TipoDocumentoModel::where('prioridad', 0)->get();
        $obj_actividades = td_ActividadModel::all();

        return view('tramitesDepartamentales.gestionFlujo.gestionFlujo')->with([
            "listaTipoTramite"=>$obj_tipoTramite,
            "listaTipoDocumento"=>$obj_tipoDocumetno,
            "listaActividades"=>$obj_actividades
        ]);
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // ***************** VALIDACIONES DE DATOS ****************************
        
            //comprobamos que todos los combos (menos el cmb de departamento_padre) se seleccionaron
            if(is_null($request->gf_select_tipo_tramite) || !validarSoloNumero($request->gf_hora_maxima) || is_null($request->gf_select_departamento_destino)){
                
                // regresamos con un mensaje 
                MENSAJERROR:
                return back()->with([
                    "old_tipo_tramite" => $request->gf_select_tipo_tramite,
                    'mensajeInfo'=>"Faltan datos por seleccionar.",
                    'mensajeColor'=>"default"
                ]);
            }

            
            //verificamos si el departamento seleccionado tiene un jefe(de no ser asi no se pued
            $depaTieneJefe = td_us001_tipofpModel::where('iddepartamento', $request->gf_select_departamento_destino)->first();
            if(is_null($depaTieneJefe)){
                return back()->with([
                    "old_tipo_tramite" => $request->gf_select_tipo_tramite,
                    'mensajeInfo'=>"No puede agregar el departamento porque no tiene un jefe asignado.",
                    'mensajeColor'=>"default"
                ]);
            }


            
            // evaluamos para determinar si se esta registrando el primer nodo del flujo
            if(is_null($request->id_flujo_padre)){ // si el flujo padre no esta seleccionado       
                // buscamos los nodos del flujo del tipo de tramite seleccionado                       
                $objFlujo = td_FlujoModel::where('idtipo_tramite',$request->gf_select_tipo_tramite)
                ->where("tipo_flujo", "G")
                ->get();
                if(sizeof($objFlujo)>0){  //si se encuentran datos quiere decir que ya tiene un flujo definido, entonces es un error            
                    goto MENSAJERROR;
                }
            }

        // ***************** FIN VALIDACIONES DE DATOS ************************


        // ***************** REGISTRO DE NODO DEL FLUJO DE UN TIPO DE TRÁMITE ********************

            // obtenemos el valor mayor registrador en el campo orden en los flujos del tipo de tramite seleccionado
            $orden_nodo=td_FlujoModel::where('idtipo_tramite',$request->gf_select_tipo_tramite)
                ->where("tipo_flujo", "G")
                ->max("orden");
            // si viene nulo se inicia con un orden de 0
            if(is_null($orden_nodo)){
                $orden_nodo=0;
            }

            $finalizarFLujo  = 0;
            // verificamos si se desea finalizar el flujo
            if($request->input_flujo_finalizar=="on"){
                $finalizarFLujo=1;
            }

            // registramos el nuevo nodo del flujo en la base de datos
            $obj_flujo = new td_FlujoModel();
            $obj_flujo->notificar="";
            $obj_flujo->orden=($orden_nodo+1);
            $obj_flujo->estado="1";
            $obj_flujo->idtipo_tramite = $request->gf_select_tipo_tramite;
            $obj_flujo->idflujo_padre = $request->id_flujo_padre;        
            $obj_flujo->hora_maxima=$request->gf_hora_maxima;
            $obj_flujo->estado_finalizar=$finalizarFLujo;
            $obj_flujo->tipo_envio = $request->tipo_envio;
            $obj_flujo->tipo_flujo = "G";
            
            $obj_flujo->iddepartamento = $request->gf_select_departamento_destino;

            $obj_flujo->save();

            //guardamos el id del nuevo nodo "flujo" registrado en bd
            $idFlujo = $obj_flujo->idflujo;
            
            //REGISTRO DE TIPOS DE DOCUMENTOS REQUERIDOS DE UN NODO DEL FLUJO DE UN TIPO DE TRÁMITE
            //recorremos cada uno de los tipos de documentos agregados
            if (isset($request->input_tipo_documento)){
                foreach ($request->input_tipo_documento as $td => $idtipo_documento){
                    //registramos en base de datos cada tipo de documento
                    $obj_tipoDoc_flujo = new td_TipoDocumentoFlujoModel();
                    $obj_tipoDoc_flujo->idflujo = $idFlujo;
                    $obj_tipoDoc_flujo->idtipo_documento = (int)$idtipo_documento;
                    $obj_tipoDoc_flujo->save();
                }            
            }

        //****************** REGISTRO DE ACTIVIDADES A REALIZAR EN UN NODO DEL FLUJO DE UN TIPO DE TRÁMITE
            //recorremos cada una de las actividades agregadas
            if (isset($request->input_actividad)) {
                foreach ($request->input_actividad as $a => $idactividad) {
                    //registramos en base de datos cada actividad
                    $obj_actividad_flujo = new td_FlujoActividadModel();
                    $obj_actividad_flujo->idflujo = $idFlujo;
                    $obj_actividad_flujo->idactividad = (int)$idactividad;
                    $obj_actividad_flujo->save(); 
                }            
            }


        //****************** REGRESAMOS A LA VISTA ANTERIOR ********************

        return back()->with([
            "old_tipo_tramite" => $request->gf_select_tipo_tramite,
            'mensajeInfo'=>"Nodo del flujo registrada con extito.",
            'mensajeColor'=>"success"
        ]);            

    }


    public function filtrarFlujoYDepartamentosPorTipoTramite($idTipoTramite)
    {
        try {
            //verificamos si el tipo de tramite tiene un flujo iniciado
            $objFlujo = td_FlujoModel::with('departamento','flujo_padre','flujo_actividad','tipo_documento_flujo')
                ->where('idtipo_tramite',$idTipoTramite)
                ->where('tipo_flujo',"G") // los los flujos generales
                ->orderBy('orden',"ASC")
                ->get();
            
            //obtenemos un listado de todos los departamentos para llenar el combo de departamentos destino
            $listaTodosLosDepartamentos = DepartamentoModel::with('periodo')
                ->whereHas('periodo',function($periodo){
                    $periodo->where("estado","A");
                })->get();

            // si no hay ningun flujo definido no retornamos ningun departamento
            if(sizeof($objFlujo)==0){
                //listamos todos los departamento
                // enviamos todos los departamentos que pertenescan solo al periodo activo
                return  response()->json([
                    "listaTodosLosDepartamentos" => $listaTodosLosDepartamentos,
                    "nodosFlujo" => $objFlujo
                ]);
            }else{ // ya hay departamentos en el flujo
                
                return response()->json([
                    "listaTodosLosDepartamentos" => $listaTodosLosDepartamentos,
                    "nodosFlujo" => $objFlujo
                ]);
            }
        } catch (\Throwable $th) {
            Log::error("Error en TdFlujoController filtrarFlujoYDepartamentosPorTipoTramite Mensaje =>".$th->getMessage());
            return 0;
        }
    }



    public function filtratFlujosHijos($idflujo)
    {
        try {

            // obtenemos todos los nodos hijos del flujo enviado
            $listaFlujoHijos = td_FlujoModel::where("idflujo_padre", $idflujo)->get();

            return response()->json([
                "listaFlujoHijos"=>$listaFlujoHijos
            ]);

        } catch (\Throwable $th) {
            Log::error("Error en TdFlujoController filtratFlujosHijos Mensaje =>".$th->getMessage());
            return 0;
        }
    }



    public function filtrarActividadesPorDepartamento($idDepartamento)
    {
        try {
            $listaActividades = td_ActividadModel::with('departamento')
                ->where('iddepartamento',$idDepartamento)
                ->get();
            return response()->json($listaActividades);            
        } catch (\Throwable $th) {
            Log::error("Error en TdFlujoController filtrarActividadesPorDepartamento Mensaje =>".$th->getMessage());
            return 0;
        }

    }



    public function mostrarTipoDocActivDeNodoFlujo($idFlujo)
    {
        try {
            // obtenemos el listado de todas los tipos de documentos requeridos
            // solo obtenemos los que esten relacionados con el tipo de documentos
            // y en su relacion pertenescan al flujo seleccionado
            $listaTipoDocumentos = td_TipoDocumentoModel::with('tipo_documento_flujo')
                ->whereHas('tipo_documento_flujo', function($tipo_documento_flujo) use ($idFlujo){
                    $tipo_documento_flujo->where('idflujo',$idFlujo);
                })->get();
            
            // hacemos la misma consulta para las actividades agregadas
            $listaActividades = td_ActividadModel::with('flujo_actividad')
                ->whereHas('flujo_actividad',function($flujo_actividad) use ($idFlujo){
                    $flujo_actividad->where('idflujo',$idFlujo);
                })->get();

            return response()->json([
                'listaTipoDocumentos' => $listaTipoDocumentos,
                'listaActividades' => $listaActividades
            ]);            
        } catch (\Throwable $th) {
            Log::error("Error en TdFlujoController mostrarTipoDocActivDeNodoFlujo Mensaje =>".$th->getMessage());
            return 0;
        }

    }


    public function destroy($id)
    {
        
        try {
            //VALIDAMOS QUE SE ESTE ELIMINANDO EL ULTIMO NODO
            // obtenemos el nodo (flujo) que a eliminar 
            $flujoEliminar = td_FlujoModel::find($id);
            $idTipoTramite = $flujoEliminar->idtipo_tramite;
            // De la lista de nodos al que pertenece el nodo a eliminar, obtenemos el que tenta un orden mayor
            $nodosHijos=td_FlujoModel::where('idflujo_padre',$flujoEliminar->idflujo)->count(); // contamos cuantos nodos hijo tiene el nodo a eliminar

            //comparamos si el a eliminar es el mayor
            if($nodosHijos > 0){ // si el nodo tiene hijos
                $plural=""; if($nodosHijos>1){ $plural="s"; }
                return back()->with([
                    "old_tipo_tramite" => $idTipoTramite,
                    'mensajeInfo'=>"Este nodo no se puede eliminar, porque tiene $nodosHijos nodo$plural hijo$plural.",
                    'mensajeColor'=>"default"
                ]);   
            }

            // eliminamos los documentos asignados
            // obtenemos todos los tipos de documentos asignados al nodo a eliminar
            $listaTipoDocumentos = td_TipoDocumentoFlujoModel::where('idflujo',$id)->get();
            // recorremos cada tipo de documentos asignado
            foreach ($listaTipoDocumentos as $td => $tipoDocumento) {
                $tipoDocumento->delete(); // eliminamos de la tabla "td_tipo_documentos_flujo"
            }


            // eliminamos las actividades asignadas
            // obtenemos todas las actividades asignadas al nodo a eliminar
            $listaActividades = td_FlujoActividadModel::where('idflujo',$id)->get();
            foreach ($listaActividades as $a => $actividad) {
                $actividad->delete(); // eliminamos  de la tabla "td_flujo_actividad"
            }


            // eliminamos el node del flujo
            if($flujoEliminar->delete()){
                return back()->with([
                    "old_tipo_tramite" => $idTipoTramite,
                    'mensajeInfo'=>"Nodo del flujo eliminado con extito.",
                    'mensajeColor'=>"success"
                ]);   
            } 

        }catch (\Throwable $th) {
            return back()->with([
                "old_tipo_tramite" => $id,
                'mensajeInfo'=>"No se pudo eliminar el nodo del flujo.",
                'mensajeColor'=>"error"
            ]);   
        }
        
    }
}
