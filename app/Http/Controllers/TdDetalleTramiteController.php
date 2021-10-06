<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\td_DetalleTramiteModel;
use App\td_DocumentoModel;
use App\td_us001_tipofpModel;
use App\td_DestinoModel;
use App\ParametrosGeneralesModel;
use App\td_TipoTramiteModel;
use App\td_PrioridadTramiteModel;
use App\td_FlujoModel;
use APp\td_TramiteModel;
use App\td_TipoDocumentoModel;
use App\td_TramiteInteresadoModel;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TdTramiteController;
use Illuminate\Support\Facades\Validator;
use DB;
use Log;

class TdDetalleTramiteController extends Controller
{

    // funcion para retornar los documentos generados en un detalle de tramite (el id viene encryptado)
    public function obtenerDocumentos($iddetalle_tramite){

        try {
        
            $iddetalle_tramite = decrypt($iddetalle_tramite);
            $detalle_tramite = td_DetalleTramiteModel::with('documento')
                ->where('iddetalle_tramite', $iddetalle_tramite)
                ->first();
            return response()->json([
                "error" => false,
                "resultado" => $detalle_tramite
            ]);

        }catch(\Throwable $th) {
            Log::error("TdDetalleTramiteController => obtenerDocumentos Mensaje => ".$th->getMessage());    
            return response()->json([
                "error" => true,
                "mensaje" => "No se pudo obtener los documentos del trámite"
            ]);
        }

    }


    // funcion para retornar la informacion de un tramite para editarlo (el id viene encryptado)
    public function editarDetalleTramite(){

        // ------------- obtenemos datos del detalle tramite ------------------------------------

            $iddetalle_tramite = $_GET["iddetalle_tramite"];
            $iddetalle_tramite = decrypt($iddetalle_tramite);

            $detalle_tramite = td_DetalleTramiteModel::with('tramite','destino', 'departamento_origen', 'documento')
                ->where('iddetalle_tramite', $iddetalle_tramite)
                ->where(function($query){
                    $query->where('estado','B')
                          ->orWhere("estado",'R');
                })
                ->first();

            $numReferencia = td_DocumentoModel::where('iddetalle_tramite', $iddetalle_tramite)
                ->where('tipo_creacion','E')
                ->pluck('numReferencia')
                ->first();


            if(is_null($detalle_tramite)){
                return back();
            }

        // ------------- obtenemos el jefe del departamento

            $jefeDepartamento = jefeDetartamentoLogueado();
            if($jefeDepartamento == false){ return back(); }

        // ------------- obtenemos los tipo de documentos prioritarios --------------------------

            $listaTipoDocPrioritarios = td_TipoDocumentoModel::where('prioridad',1)->where('estado', 1)->get();
            $documentoPrincipal = td_DocumentoModel::with('tipo_documento')
                ->where('iddetalle_tramite',$iddetalle_tramite)
                ->where('tipo_creacion', 'E')->first();
                
            #obtenemos codigo html del documento principal
            $contenidoDocumentoPrincipal = \Storage::disk('disksServidorSFTPborradores')->get($documentoPrincipal->rutaDocumento.".txt");
        
        // ------------- verificar si el flujo esta definido o no -------------------------------
            
            $idflujo = $detalle_tramite->idflujo;

            if(is_null($idflujo)){ #flujo no definido
                $flujo = false;
                $listaTipoDocumentos = td_TipoDocumentoModel::where('prioridad',0)->get();
            }else{ #flujo definido
                $flujo = true;
                $listaTipoDocumentos = td_TipoDocumentoModel::with('tipo_documento_flujo')
                ->whereHas('tipo_documento_flujo',function($query) use($idflujo){
                    $query->where('idflujo',$idflujo);
                })->get();
            }

        
        // ------------- obtener departamentos PARA COPIA Y INTERESADOS -------------------------
            $listaPara = [];
            $listaCopia = [];
            $listaInteresados = [];
            foreach($detalle_tramite->destino as $dt => $destino){

                if($destino->tipo_envio == "P"){
                    array_push($listaPara, $destino);
                }else if($destino->tipo_envio == "C"){
                    array_push($listaCopia, $destino);                
                }

            }
            
        // ------------- obtener contenido de documentos editados -------------------------------

            $listaDocumentoAdjunto = []; // documentos adjuntados al trámite
            $listaTipoDocumentosUsados = []; // para almacenar el id de los tipo de documentos usados o creados (para no cargarlos en el combo)
            
            foreach ($detalle_tramite->documento as $doc => $documento){
                
                if($documento->tipo_creacion == "A"){
                    array_push($listaDocumentoAdjunto, $documento);
                }

                $listaTipoDocumentosUsados[$documento->idtipo_documento] = true;

            }


        // ------------- retorno ----------------------------------------------------------------

    
        return view('tramitesDepartamentales.detalleTramite.editar.editarDetalleTramite')->with([
            'detalle_tramite' => $detalle_tramite,
            'flujo' => $flujo,
            'listaPara' => $listaPara,
            'listaCopia' => $listaCopia,
            'contenidoDocumentoPrincipal' => $contenidoDocumentoPrincipal,
            'listaDocumentoAdjunto' => $listaDocumentoAdjunto,
            'listaTipoDocumentos' => $listaTipoDocumentos,
            'listaTipoDocumentosUsados' => $listaTipoDocumentosUsados,
            'listaTipoDocPrioritarios' => $listaTipoDocPrioritarios,
            'documentoPrincipal' => $documentoPrincipal,
            'iddetalle_tramite' => encrypt($detalle_tramite->iddetalle_tramite),
            'jefeDepartamento' => $jefeDepartamento,
            'numReferencia' => $numReferencia
        ]);            


    }


    // funcion para enviar un tramite a la bandeja del jefe (recive el id encriptado)
    public function subirDetalleTramite($iddetalle_tramite){
        
        try{

            $iddetalle_tramite = decrypt($iddetalle_tramite);
            $detalleTramite = td_DetalleTramiteModel::with('documento','destino')
                ->where('iddetalle_tramite', $iddetalle_tramite)->first();
            $detalleTramite->estado = "T";

            $numReferencia = td_DocumentoModel::where('iddetalle_tramite', $iddetalle_tramite)
                ->where('tipo_creacion','E')
                ->pluck('numReferencia')
                ->first();

            // le quitamos lo borrador al los documentos
            foreach ($detalleTramite->documento as $key => $documento){
                if($documento->tipo_creacion == "E"){

                    $texto_documento =  Storage::disk('disksServidorSFTPborradores')->get($documento->rutaDocumento.".txt");
                    $listArrPara = [];
                    $listArrCopia = [];
                    
                    foreach ($detalleTramite->destino as $des => $destino){
                        if($destino->tipo_envio=="P"){
                            array_push($listArrPara, $destino->iddepartamento);
                        }else if($destino->tipo_envio=="C"){
                            array_push($listArrCopia, $destino->iddepartamento);
                        }
                    }

                    $listaAnexos = td_DocumentoModel::where('iddetalle_tramite',$iddetalle_tramite)
                        ->where('tipo_creacion', 'A')
                        ->pluck('codigoDocumento')
                        ->toArray();

                    // agregamos el para, codigo documento y copia
                    $obj_texto_generado = getInfoDocumento(
                        $texto_documento,
                        $detalleTramite->asunto,
                        $listArrPara,
                        $listArrCopia,
                        $documento->idtipo_documento,
                        $numReferencia,
                        $listaAnexos,
                        false //no mostrar que esta firmado electrónicamente
                    );

                    // CREAR EL DOCUMENTO EL PDF =======================================
                        $borrador = false; // agregar marca de agua
                        $pdf = getEstructuraDocumento($obj_texto_generado->texto_documento_completo, $borrador);
                        $documentoListo = $pdf->stream();
                        Storage::disk('disksServidorSFTPborradores')->put(str_replace("", "",$documento->rutaDocumento.".pdf"), $documentoListo); // guardamos el documentos
                    // ===============================================================================

                }
            }

            $detalleTramite->save();

            return response()->json([
                "error" => false,
                "resultado" => [
                    "status" => "success",
                    "mensaje" => "El trámite se envió a la bandeja de entrada del jefe del departamento"
                ]
            ]);  

        }catch(\Throwable $th){
            Log::error("TdDetalleTramiteController => subirDetalleTramite => Mensaje:".$th->getMessage());
            return response()->json([
                "error" => true,
                "resultado" => [
                    "status" => "error",
                    "mensaje" => "No se pudo subir el trámite"
                ]
            ]);
        }

    }


    // funcion para eliminar los documentos registrados en caso de error
    public function eliminarDocumentosRegistradosEdit($listaDocEliminar, $iddetalle_tramite, $listaDocumentosAllOld){

        try {

            // eliminamos documentos en base de datos solo los nuevos que se ingresaron
            $documentosElimiar = td_DocumentoModel::where("iddetalle_tramite", $iddetalle_tramite)
                ->whereNotIn('iddocumento', $listaDocumentosAllOld);
            $documentosElimiar->delete();

            // eliminamos documentos en servidor sftp
            foreach ($listaDocEliminar as $doc => $docDel){
                // eliminamos el doc de texto
                $exists = \Storage::disk('disksServidorSFTPborradores')->exists($docAdj.".txt");
                if ($exists){
                    Storage::disk("disksServidorSFTPborradores")->delete($docAdj.".txt");
                }

                // eliminamos el doc pdf
                $exists = \Storage::disk('disksServidorSFTPborradores')->exists($docAdj.".pdf");
                if ($exists){
                    Storage::disk("disksServidorSFTPborradores")->delete($docAdj.".pdf");
                }
                
            }

        } catch (\Throwable $th) {
            Log::error("TdDetalleTramiteController => eliminarDocumentosRegistradosEdit => Mensaje:".$th->getMessage());
        }

    }

    // funcion que actualiza o correige un tramite guardado
    public function update(Request $request, $id)
    { 
        // dd($request);
        try {

            DB::beginTransaction();

            $mensajeError = ""; // para almacenar el error en caso de que ocurra
            $listaNombreDocDel = []; // para almacenar el nombre de los documentos (solo para eliminarlos)
            $objTramiteController = new TdTramiteController(); // seteamos un objeto del controlador  TdTramiteController para reutilizar sus metodos

            // ------------- obtenemos datos generales --------------------------------------------------------

                $iddetalle_tramite = decrypt($id);
                $detalleTramiteEdit = td_DetalleTramiteModel::with('tramite')
                    ->where('iddetalle_tramite', $iddetalle_tramite)
                    ->where(function($query){ // solo los tramites que estan en borrador o fueron enviados a corregir
                        $query->where('estado','B')
                              ->orWhere('estado','R');
                    })
                    ->first();

                $numReferencia = td_DocumentoModel::where('iddetalle_tramite', $iddetalle_tramite)
                        ->where('tipo_creacion','E')
                        ->pluck('numReferencia')
                        ->first();

                if(is_null($detalleTramiteEdit)){
                    $mensajeError = "Usted ya no puede editar el trámite";
                    goto RETORNARERROR;
                }

                $idtipo_tramite_edit = $detalleTramiteEdit->tramite->idtipo_tramite; // id del tipo de tramite en edicion

            // ------------- verificamos que se envien departamentos para -------------------------------------
                if (!isset($request->input_depaEnviarPara)) { // si no se envian departamentos como para (si esta definido el flujo por defecto va a enviar)
                    $mensajeError = "Falta agregar departamentos destino";
                    goto RETORNARERROR;
                }
            // ------------- validamos los caracteres especiales ----------------------------------------------

                $dataValidar = [
                    "asunto" => $request->gt_asunto,
                    "observaciones"  => $request->gt_observaciones,
                    "input_descripcion_documento" => $request->input_descripcion_documento,
                    "input_codigo_documento_adjunto" => $request->input_codigo_documento_adjunto,
                    "input_descripcion_documento_adjunto" => $request->input_descripcion_documento_adjunto
                ];
                $reglatexto = 'required|string|regex:/^[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ\s+\-.]+$/';
                $validator = Validator::make($dataValidar, [
                    "asunto" => $reglatexto,
                    "observaciones"  => $reglatexto,
                    "input_descripcion_documento.*" => $reglatexto,
                    "input_codigo_documento_adjunto.*" => $reglatexto,
                    "input_descripcion_documento_adjunto.*" => $reglatexto
                ]);
            
                if ($validator->fails()) {
                    $mensajeError = "No se permite ingresar caracteres especiales";
                    goto RETORNARERROR;
                }

            // ------------- obtenemos los documentos y departamento destinos antigos para borrarles ----------

                $docAdjConservar = array(0); // iniciamos en cero para evitar errores
                if(isset($request->id_documento_adjunto_conservado)){
                    foreach ($request->id_documento_adjunto_conservado as $key => $iddocmento_conservar){
                        array_push($docAdjConservar, decrypt($iddocmento_conservar));
                    }
                }

                // id de documentos a borrar (sin los que se conservan)
                $listaDocumentosDelete = td_DocumentoModel::where('iddetalle_tramite',$detalleTramiteEdit->iddetalle_tramite)
                    ->whereNotIn('iddocumento', $docAdjConservar)
                    ->pluck('iddocumento');

                // id de todos los documentos antes de la edicion (con los que se conservan)
                $listaDocumentosAllOld = td_DocumentoModel::where('iddetalle_tramite',$detalleTramiteEdit->iddetalle_tramite)->pluck('iddocumento');

                // $borrar = td_DocumentoModel::whereIn('iddocumento',$listaDocumentosDelete)->get(); //(estos se borran
                
                // id de los departamento destino a borrar (todos los registrados antes de la edicion)
                $listaDestinoDelete = td_DestinoModel::where('iddetalle_tramite', $detalleTramiteEdit->iddetalle_tramite)
                    ->pluck('iddestino');

            // ------------- verificamos el tamaño y formato de los documentos adjuntos -----------------------

                if(isset($request->file_documento_adjunto)){

                    // obtenemos los formatos de documento permitidos
                    $parGenFormato = ParametrosGeneralesModel::where("codigo","FORMDOC")->get();
                        if(sizeof($parGenFormato)==0){
                            Log::error("NO HAY FORMATOS DE DOCUMENTOS AGREGADOS (FORMDOC) EN LA TABLA DE PARAMETROS GENERALES");
                            $mensajeError = "Formato de documento no permitido";
                            goto RETORNARERROR;
                        }

                        $formatos = []; // arreglo de todos los formatos
                        foreach ($parGenFormato as $f => $fort){
                            $formatos[$fort->valor] = true;
                        }
            
                    // obtenemos el tamaño maximo de documentos (en megas)
                    $maxMegas = ParametrosGeneralesModel::where("codigo","DOCMAX")->first();
                    $maxMegas = $maxMegas->valor;


                    foreach ($request->file_documento_adjunto as $doc => $docAdj){

                        $extension = pathinfo($docAdj->getClientOriginalName(), PATHINFO_EXTENSION); // obtenemos la extencion del documento adjunto
                        
                        if(!isset($formatos[$extension])) { // si la extension del documento no existe
                            $mensajeError = "Hay documentos adjuntos con formato no permitido";
                            goto RETORNARERROR;
                        }

                        $tamArchivo = filesize($docAdj);
                        $tamArchivo = (($tamArchivo/1024)/1024); // convertimos en megas
                        if($tamArchivo>$maxMegas || $tamArchivo==0){   

                            $mensajeError = "Solo se permite adjuntar documentos con un tamaño de ".$maxMegas."MB";
                            goto RETORNARERROR;

                        }   
                    }
                }

            // ------------- verificar si el flujo esta definido o no -----------------------------------------

                $listArrPara = [];// lista de departamentos PARA
                $listArrCopia = [];// lista de departamentos COPIA
                $idflujo = $detalleTramiteEdit->idflujo;

                if(!is_null($idflujo)){ // tiene un flujo definido

                    #verificar que se envia idtipodocumentos creados
                        $listaIdTipoDocCreado = [];
                        if(isset($request->input_id_tipo_documento)){ $listaIdTipoDocCreado= $request->input_id_tipo_documento;}

                    #verificar que se envia idtipodocumentos adjuntos
                        $listaIdTipoDocAdjunto = [];
                        if(isset($request->input_id_tipo_documento_adjunto)){ $listaIdTipoDocAdjunto = $request->input_id_tipo_documento_adjunto;}
                    
                    #verificar documentos adjuntos que se conservan
                        if(isset($request->input_id_tipo_documento_conservado)){ $listaIdTipoDocAdjunto = array_merge($listaIdTipoDocAdjunto, $request->input_id_tipo_documento_conservado); }

                    $datosFLujoTramite = $objTramiteController->verificarDatosFLujoTramite($idflujo, $listaIdTipoDocCreado, $listaIdTipoDocAdjunto); #departamento destino definido en el flujo
                
                    // obtener departamentos a enviar y verificar documentos requeridos
                    if($datosFLujoTramite->status==true){ #todo ready
                        $listArrPara = $datosFLujoTramite->listArrPara;
                        $listArrCopia = $datosFLujoTramite->listArrCopia;
                    }else{
                        $mensajeError = "Faltan documentos por agregar.";
                        goto RETORNARERROR;
                    }                         



                }else{
                    $mensajeError = "Flujo de trámite no definido.";
                    goto RETORNARERROR;
                }   

            // ------------- creamos los documentos del editor de texto (registramos en base de datos) --------
                if (isset($request->input_contenido_documento)) {
                    $listaTipoDocumento = [];
                    foreach ($request->input_id_tipo_documento as $td => $idtipo_documento) {
                        array_push($listaTipoDocumento, decrypt($idtipo_documento));
                    }

                    $listaAnexos = [];

                    $listaAnexos = td_DocumentoModel::where('iddetalle_tramite',$detalleTramiteEdit->iddetalle_tramite)
                        ->where('tipo_creacion', 'A')
                        ->whereIn('iddocumento', $docAdjConservar)
                        ->pluck('codigoDocumento')
                        ->toArray();

                    if(isset($request->input_codigo_documento_adjunto)){
                        $listaAnexos = array_merge($request->input_codigo_documento_adjunto, $listaAnexos);
                    }

                    $crearDocumento = $objTramiteController->crearDocumentoEditado(
                        $request->input_contenido_documento, 
                        $detalleTramiteEdit->iddetalle_tramite, 
                        $request->input_descripcion_documento, 
                        $listaTipoDocumento,
                        $request->gt_asunto,
                        $listArrPara,
                        $listArrCopia,
                        $numReferencia,
                        $listaAnexos
                    );

                    $listaNombreDocDel = array_merge($listaNombreDocDel, $crearDocumento->listaNombreDoc); // almacenamos el nombre de los documentos creados

                    if($crearDocumento->status=="error"){ // si ocurre un error al crear los documentos
                        $mensajeError = "No se pudo registrar los documentos generados por el editor de texto.";
                        goto RETORNARERROR;
                    }               
                }


            // ------------- almacenamos los documentos adjuntos (registramos en base de datos) ---------------

                if(isset($request->file_documento_adjunto)){

                    $listaTipoDocumentoAdj = [];
                    foreach ($request->input_id_tipo_documento_adjunto as $td => $idtipo_documento) {
                        array_push($listaTipoDocumentoAdj, decrypt($idtipo_documento));
                    }

                    $adjuntarDocumentos = $objTramiteController->adjuntarDocumentos(
                        $request->file_documento_adjunto,
                        $listaTipoDocumentoAdj,
                        $request->input_codigo_documento_adjunto,
                        $request->input_descripcion_documento_adjunto,
                        $detalleTramiteEdit->iddetalle_tramite
                    ); 

                    $listaNombreDocDel = array_merge($listaNombreDocDel, $adjuntarDocumentos->listaNombreDoc); // almacenamos los nombre de los documentos adjuntos

                    if($adjuntarDocumentos->status=="error"){
                        // eliminamos los documentos generados
                        $this->eliminarDocumentosRegistradosEdit($listaNombreDocDel, $iddetalle_tramite, $listaDocumentosAllOld);

                        $mensajeError = "No se pudo registrar los documentos adjuntos.";
                        goto RETORNARERROR;
                    }
                
                }       
                
            // ------------- registrar envio de tramite a departamento ----------------------------------------
            
                $envioDepartamento = $objTramiteController->enviarDepartamentos(
                    $listArrPara, 
                    $listArrCopia, 
                    $detalleTramiteEdit->iddetalle_tramite,
                    $idtipo_tramite_edit
                );

                if($envioDepartamento->status == "error"){
                    // eliminamos los departamentos destino se si se pudieron registrar
                    $destino_eliminar = td_DestinoModel::where("iddetalle_tramite", $detalleTramite->iddetalle_tramite)
                        ->whereNotIn('iddestino', $listaDestinoDelete); // no eliminamos los antiguos (los que estaban antes la edicion)
                    $destino_eliminar->delete();

                    // eliminamos los documentos generados
                    $this->eliminarDocumentosRegistradosEdit($listaNombreDocDel, $iddetalle_tramite, $listaDocumentosAllOld);

                    $mensajeError = "No se pudo registrar los departamentos destino.";
                    goto RETORNARERROR;
                }
            
            // ------------- actualizamos el detalle tramite y borramos los datos antiguos --------------------
                
                $detalleTramiteEdit->asunto = strtoupper($request->gt_asunto);
                $detalleTramiteEdit->observacion = strtoupper($request->gt_observaciones);
                $detalleTramiteEdit->save();

                // eliminamos los documentos antiguos
                $eliminarDocumentos = td_DocumentoModel::whereIn('iddocumento', $listaDocumentosDelete);
                $eliminarDocumentos->delete();

                // eliminamos los departamento destinos
                $eliminarDestino = td_DestinoModel::whereIn('iddestino', $listaDestinoDelete);
                $eliminarDestino->delete();

            // ------------- si se realiza la actualización con exito ------------------------------------------
                
                DB::commit();
                return response()->json([
                    "error" => false,
                    "resultado" => [
                        "status" => "success",
                        "mensaje" => "Trámite actualizado con éxito",
                        "codTramite" => $detalleTramiteEdit->tramite->codTramite
                    ]
                ]);

            RETORNARERROR:
                
                DB::rollback();
                return response()->json([
                    "error" => true,
                    "resultado" => [
                        "status" => "error",
                        "mensaje" => $mensajeError
                    ]
                ]);  

        }catch (\Throwable $th){
            Log::error("TdDetalleTramiteController => update | Mensaje => ".$th->getMessage());
            
            DB::rollback();
            return response()->json([
                "error" => true,
                "resultado" => [
                    "status" => "error",
                    "mensaje" => "No se puede actualizar el trámite"
                ]
            ]);
        }

    }


    //----------- METODOS PARA ANTEDER UN TRÁMITE -----------------------------------------------

        // retorna la vista para atender un trámite
        public function atenderDetalleTramite(){

            // ------------- obtenemos datos del detalle tramite ------------------------------------

                $iddetalle_tramite = $_GET["iddetalle_tramite"];
                $iddetalle_tramite = decrypt($iddetalle_tramite);
                $numReferencia = null;     

                $detalle_tramite = td_DetalleTramiteModel::with('tramite','destino', 'departamento_origen', 'documento')
                    ->where('iddetalle_tramite', $iddetalle_tramite)               
                    ->where('estado','T') // el tramite debe estar enviado
                    ->where('aprobado','1')  // el tramite debe estar aprobada con el jefe
                    ->first();

                $numReferencia = td_DocumentoModel::where('iddetalle_tramite', $iddetalle_tramite)
                    ->where('tipo_creacion','E')
                    ->pluck('codigoDocumento')
                    ->first();

                if(is_null($detalle_tramite) || is_null($numReferencia)){
                    return back();
                }

                

            // ------------- obtenemos el jefe del departamento

                $jefeDepartamento = jefeDetartamentoLogueado();
                if($jefeDepartamento == false){ return back(); }
            
                $depLogueado = departamentoLogueado();

            // ------------- verificar si el flujo esta definido o no -------------------------------

                $objTramiteController = new TdTramiteController();
                $listaTipoDocumentos = [];
                $idflujo_atender = $detalle_tramite->idflujo;

                $listaFlujoHijoPara = [];
                $listaFlujoHijoCopia = [];

                $flujo = null;
                $flujo_registrar = null;
                $idflujo_registrar = null;

                if(!is_null($idflujo_atender)){ #FLUJO DEFINIDO
                    $flujo = true;

                    //verificamos el flujo en el que estamos
                    $flujoAtender = td_FlujoModel::with('flujo_hijo')
                        ->where('idflujo',$idflujo_atender)
                        ->first();

                    // identificamos el nodo del flujo en el que se encuentra el departamento
                    foreach($flujoAtender->flujo_hijo as $fh => $flujo_hijo){
                        if($flujo_hijo->iddepartamento == $depLogueado['iddepartamento']){

                            if($flujo_hijo->tipo_flujo=="D" && $flujo_hijo->estado_finalizar==1){ #es el nodo finalizado del flujo departamental
                                #pasar al primer nodo del flujo general
                                $primNodoFlujoGen = td_FlujoModel::with('flujo_hijo')
                                    ->where('idtipo_tramite', $flujo_hijo->idtipo_tramite)
                                    ->where('tipo_flujo', 'G')
                                    ->orderBy('orden','ASC')
                                    ->first();
                                $flujo_registrar = $primNodoFlujoGen;
                                $idflujo_registrar = $primNodoFlujoGen->idflujo;

                            }else{
                                $nodoFlujoHijo = td_FlujoModel::with('flujo_hijo')
                                ->where('idflujo', $flujo_hijo->idflujo)
                                ->first();
                                $flujo_registrar = $nodoFlujoHijo;                     
                                $idflujo_registrar = $nodoFlujoHijo->idflujo;
                            }
                            break;

                        }
                    }

                    $listaFlujoHijoPara = [];
                    $listaFlujoHijoCopia = [];
                    $listaInteresados = [];

                    foreach($flujo_registrar->flujo_hijo as $fh => $flujo_hijo){
        
                        if($flujo_hijo->tipo_envio == "P"){
                            array_push($listaFlujoHijoPara, $flujo_hijo);
                        }else if($flujo_hijo->tipo_envio == "C"){
                            array_push($listaFlujoHijoCopia, $flujo_hijo);                
                        }
        
                    }


                    //verificamos si el el departamento puede atender el trámite (si no tiene destinos no puede)
                    if(sizeof($listaFlujoHijoPara)==0 && sizeof($listaFlujoHijoCopia)==0){
                        return view("error");
                    }

                    //filtramos todos los tipos de documentos ligados al flujo
                    $listaTipoDocumentos = td_TipoDocumentoModel::with('tipo_documento_flujo')
                        ->whereHas('tipo_documento_flujo',function($query) use($idflujo_registrar){
                            $query->where('idflujo',$idflujo_registrar);
                        })->get();

                }else{ // FLUJO NO DEFINIDO

                    abort(500, "Flujo de definido");

                }

            // ------------- obtenemos el listado de todos los tramites globales y asociados al departamento

                $listaTipoTramites = td_TipoTramiteModel::with('tipotramite_departamento')
                ->where(function($queryTipDep) use ($depLogueado){
                    $queryTipDep->whereHas('tipotramite_departamento', function($queryTipoTraDepa) use ($depLogueado){ // obtenemos los tramites asocciados al departamento
                        $queryTipoTraDepa->where('iddepartamento', $depLogueado['iddepartamento']);
                    })
                    ->orWhere('tramite_global','1'); // o obtenemos los tramites globales                
                })
                ->where("estado",1)
                ->get();
                
            // ------------- retornar informacion -----------------------------------------

                $listaPrioridad = td_PrioridadTramiteModel::all(); // lista de prioridades
                // lista de documentos prioridad
                $listaDocPrioritarios = td_TipoDocumentoModel::where('prioridad',1)->where('estado', 1)->get();
                
                return view('tramitesDepartamentales.detalleTramite.atender.atencionTramite')
                    ->with([
                        'listaPrioridad' => $listaPrioridad,
                        'listaTipoTramites' => $listaTipoTramites,
                        'jefeDepartamento' => $jefeDepartamento,
                        'detalle_tramite' => $detalle_tramite,
                        'flujo' => $flujo,
                        'listaFlujoHijoPara' => $listaFlujoHijoPara,
                        'listaFlujoHijoCopia' => $listaFlujoHijoCopia,
                        'listaTipoDocumentos' => $listaTipoDocumentos,
                        'listaDocPrioritarios' => $listaDocPrioritarios,
                        'numReferencia' => $numReferencia
                    ]);

            // -----------------------------------------------------------------------------------
        }

        // registrar la atención de un trámite
        public function registrarAtencion(Request $request, $iddetalle_tramite_atender){
            try {

                DB::beginTransaction();
                // dd($request);

                $mensajeError = ""; // para almacenar el error en caso de que ocurra
                $listaNombreDocDel = []; // para almacenar el nombre de los documentos (solo para eliminarlos)
                $objTramiteController = new TdTramiteController(); // seteamos un objeto del controlador  TdTramiteController para reutilizar sus metodos
                $iddepartamentoLogueado = departamentoLogueado()["iddepartamento"];
                
                // ------------- obtener el detalle del tramite a atender --------------------------

                    $iddetalle_tramite_atender = decrypt($iddetalle_tramite_atender);
                    $detalle_tramite_atender = td_DetalleTramiteModel::with('tramite', 'destino')
                        ->where('iddetalle_tramite', $iddetalle_tramite_atender)
                        ->first();

                    $tramite = $detalle_tramite_atender->tramite;

                    $numReferencia = td_DocumentoModel::where('iddetalle_tramite', $iddetalle_tramite_atender)
                        ->where('tipo_creacion','E')
                        ->pluck('codigoDocumento')
                        ->first();

                    # buscamos el destimo que estamos atendiendo
                    $destino_atender = null;
                    foreach ($detalle_tramite_atender->destino as $des => $destino){
                        if($destino->iddepartamento == $iddepartamentoLogueado){
                            $destino_atender = $destino;
                        }
                    }


                // ------------- verificamos que se envien departamentos para ----------------------
                    if (!isset($request->input_depaEnviarPara)) { // si no se envian departamentos como para (si esta definido el flujo por defecto va a enviar)
                        $mensajeError = "Falta agregar departamentos destino";
                        goto RETORNARERROR;
                    }
                
                // ------------- verificamos el tamaño y formato de los documentos adjuntos
                    if(isset($request->file_documento_adjunto)){

                        // obtenemos los formatos de documento permitidos
                        $parGenFormato = ParametrosGeneralesModel::where("codigo","FORMDOC")->get();
                            if(sizeof($parGenFormato)==0){
                                Log::error("NO HAY FORMATOS DE DOCUMENTOS AGREGADOS (FORMDOC) EN LA TABLA DE PARAMETROS GENERALES");
                                $mensajeError = "Formato de documento no permitido";
                                goto RETORNARERROR;
                            }

                            $formatos = []; // arreglo de todos los formatos
                            foreach ($parGenFormato as $f => $fort){
                                $formatos[$fort->valor] = true;
                            }
                
                        // obtenemos el tamaño maximo de documentos (en megas)
                        $maxMegas = ParametrosGeneralesModel::where("codigo","DOCMAX")->first();
                        $maxMegas = $maxMegas->valor;


                        foreach ($request->file_documento_adjunto as $doc => $docAdj){

                            $extension = pathinfo($docAdj->getClientOriginalName(), PATHINFO_EXTENSION); // obtenemos la extencion del documento adjunto
                            
                            if(!isset($formatos[$extension])) { // si la extension del documento no existe
                                $mensajeError = "Hay documentos adjuntos con formato no permitido";
                                goto RETORNARERROR;
                            }

                            $tamArchivo = filesize($docAdj);
                            $tamArchivo = (($tamArchivo/1024)/1024); // convertimos en megas
                            if($tamArchivo>$maxMegas || $tamArchivo==0){   

                                $mensajeError = "Solo se permite adjuntar documentos con un tamaño de ".$maxMegas."MB";
                                goto RETORNARERROR;

                            }   
                        }
                    }

                    

                // ------------- verificar si el flujo esta definido o no --------------------------
            
                    $idflujo_atender = $detalle_tramite_atender->idflujo;
                    $idflujo_registrar = null; // inportante que se anulo
                    $listArrPara = [];// lista de departamentos PARA
                    $listArrCopia = [];// lista de departamentos COPIA

                    if(!is_null($idflujo_atender)){ #FLUJO DEFINIDO
                        
                        
                        //verificamos el flujo en el que estamos
                        $flujoAtender = td_FlujoModel::with('flujo_hijo')
                            ->where('idflujo',$idflujo_atender)
                            ->first();

                        // identificamos el nodo del flujo en el que se encuentra el departamento
                        foreach($flujoAtender->flujo_hijo as $fh => $flujo_hijo){
                            if($flujo_hijo->iddepartamento == $iddepartamentoLogueado){
        
                                if($flujo_hijo->tipo_flujo=="D" && $flujo_hijo->estado_finalizar==1){ #es el nodo finalizado del flujo departamental
                                    #pasar al primer nodo del flujo general
                                    $primNodoFlujoGen = td_FlujoModel::with('flujo_hijo')
                                        ->where('idtipo_tramite', $flujo_hijo->idtipo_tramite)
                                        ->where('tipo_flujo', 'G')
                                        ->orderBy('orden','ASC')
                                        ->first();
                                    $idflujo_registrar = $primNodoFlujoGen->idflujo;
                                    
                                }else{
                                    $nodoFlujoHijo = td_FlujoModel::with('flujo_hijo')
                                    ->where('idflujo', $flujo_hijo->idflujo)
                                    ->first();                 
                                    $idflujo_registrar = $nodoFlujoHijo->idflujo;
                                }
                                break;
        
                            }
                        }


                        #verificar que se envia idtipodocumentos creados
                            $listaIdTipoDocCreado = [];
                            if(isset($request->input_id_tipo_documento)){ $listaIdTipoDocCreado= $request->input_id_tipo_documento; }

                        #verificar que se envia idtipodocumentos adjuntos
                            $listaIdTipoDocAdjunto = [];
                            if(isset($request->input_id_tipo_documento_adjunto)){ $listaIdTipoDocAdjunto = $request->input_id_tipo_documento_adjunto; }

                            $datosFLujoTramite = $objTramiteController->verificarDatosFLujoTramite($idflujo_registrar, $listaIdTipoDocCreado, $listaIdTipoDocAdjunto); #departamento destino definido en el flujo
        
                        // obtener departamentos a enviar y verificar documentos requeridos
                        if($datosFLujoTramite->status==true){ #todo ready
                            $listArrPara = $datosFLujoTramite->listArrPara;
                            $listArrCopia = $datosFLujoTramite->listArrCopia;
                        }else{
                            $mensajeError = "Faltan documentos por agregar";
                            goto RETORNARERROR;
                        }


                    }else{
                        // --------- verificamos si los departamentos estan permitidos --------
                            $input_depaEnviarCopia = [];
                            if(isset($request->input_depaEnviarCopia)){
                                $input_depaEnviarCopia = $request->input_depaEnviarCopia;
                            }

                            $verificarDepasDestino = $objTramiteController->verificarNivelDepartamento($request->input_depaEnviarPara, $input_depaEnviarCopia);
                            if($verificarDepasDestino->status=="error"){
                                // continuamos o retornamos error
                                $mensajeError = "Hay departamentos a los que no se puede enviar desde la instancia actual.";
                                goto RETORNARERROR;
                            }else{
                                // dd($verificarDepasDestino);
                                $listArrPara = $verificarDepasDestino->listArrPara;
                                $listArrCopia = $verificarDepasDestino->listArrCopia;
                            }
                    }

                // ------------- registramos en table 'td_tramite' ---------------------------------

                    $dataValidar = [
                        "asunto" => $request->gt_asunto,
                        "observaciones"  => $request->gt_observaciones,
                        "input_descripcion_documento" => $request->input_descripcion_documento,
                        "input_codigo_documento_adjunto" => $request->input_codigo_documento_adjunto,
                        "input_descripcion_documento_adjunto" => $request->input_descripcion_documento_adjunto
                    ];
                    $reglatexto = 'required|string|regex:/^[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ\s+\-.]+$/';
                    $validator = Validator::make($dataValidar, [
                        "asunto" => $reglatexto,
                        "observaciones"  => $reglatexto,
                        "input_descripcion_documento.*" => $reglatexto,
                        "input_codigo_documento_adjunto.*" => $reglatexto,
                        "input_descripcion_documento_adjunto.*" => $reglatexto
                    ]);
                
                    if ($validator->fails()) {
                        $mensajeError = "No se permite ingresar caracteres especiales";
                        goto RETORNARERROR;
                    }


                // ------------- registramos en table 'td_detalle_tramite' -------------------------

                    $detalleTramite = new td_DetalleTramiteModel();
                    $detalleTramite->fecha = date("Y-m-d H:i:s");
                    $detalleTramite->asunto =  strtoupper($request->gt_asunto);
                    $detalleTramite->observacion = strtoupper($request->gt_observaciones);
                    $detalleTramite->estado = "B";
                    $detalleTramite->aprobado = 0;
                    $detalleTramite->idtramite = $tramite->idtramite;
                    $detalleTramite->iddepartamento_origen = $iddepartamentoLogueado;
                    $detalleTramite->idus001Envia = auth()->user()->idus001;
                    $detalleTramite->nivelAtencion = ($detalle_tramite_atender->nivelAtencion+1);
                    $detalleTramite->idflujo = $idflujo_registrar;
                    $detalleTramite->iddetalle_tramite_padre = $detalle_tramite_atender->iddetalle_tramite;
                    $detalleTramite->iddestino_atendido = $destino_atender->iddestino;
                    $detalleTramite->save();

                // ------------- actualizamos el detalle tramilte atendido ---------------------

                    $detalle_tramite_atender->fechaAtiende = date("Y-m-d H:i:s");
                    $detalle_tramite_atender->idus001Atiende = auth()->user()->idus001;
                    $detalle_tramite_atender->iddepartamento_atiende = $iddepartamentoLogueado;
                    $detalle_tramite_atender->save();
        
                // ------------- creamos los documentos del editor de texto (registramos en base de datos) ----------------
                    if (isset($request->input_contenido_documento)) {
                        $listaTipoDocumento = [];
                        foreach ($request->input_id_tipo_documento as $td => $idtipo_documento) {
                            array_push($listaTipoDocumento, decrypt($idtipo_documento));
                        }

                        $listaAnexos = [];
                        if(isset($request->input_codigo_documento_adjunto)){
                            $listaAnexos = $request->input_codigo_documento_adjunto;
                        }

                        $crearDocumento = $objTramiteController->crearDocumentoEditado(
                            $request->input_contenido_documento, 
                            $detalleTramite->iddetalle_tramite, 
                            $request->input_descripcion_documento, 
                            $listaTipoDocumento,
                            $request->gt_asunto,
                            $listArrPara,
                            $listArrCopia,
                            $numReferencia,
                            $listaAnexos
                        );

                        $listaNombreDocDel = array_merge($listaNombreDocDel, $crearDocumento->listaNombreDoc); // almacenamos el nombre de los documentos creados

                        if($crearDocumento->status=="error"){ // si ocurre un error al crear los documentos
                            // eliminamos los documentos generados
                            $objTramiteController->eliminarDocumentosRegistrados($listaNombreDocDel, $detalleTramite, null);

                            $mensajeError = "No se pudo registrar los documentos generados por el editor de texto.";
                            goto RETORNARERROR;
                        }               
                    }


                // ------------- almacenamos los documentos adjuntos (registramos en base de datos) -----------------------

                    if(isset($request->file_documento_adjunto)){

                        $listaTipoDocumentoAdj = [];
                        foreach ($request->input_id_tipo_documento_adjunto as $td => $idtipo_documento) {
                            array_push($listaTipoDocumentoAdj, decrypt($idtipo_documento));
                        }

                        $adjuntarDocumentos = $objTramiteController->adjuntarDocumentos(
                            $request->file_documento_adjunto,
                            $listaTipoDocumentoAdj,
                            $request->input_codigo_documento_adjunto,
                            $request->input_descripcion_documento_adjunto,
                            $detalleTramite->iddetalle_tramite
                        ); 

                        $listaNombreDocDel = array_merge($listaNombreDocDel, $adjuntarDocumentos->listaNombreDoc); // almacenamos los nombre de los documentos adjuntos

                        if($adjuntarDocumentos->status=="error"){
                            // eliminamos los documentos generados
                            $objTramiteController->eliminarDocumentosRegistrados($listaNombreDocDel, $detalleTramite, null);

                            $mensajeError = "No se pudo registrar los documentos adjuntos.";
                            goto RETORNARERROR;
                        }
                    
                    }       
                    
                // ------------- registrar envio de tramite a departamento --------------------------
                
                    $envioDepartamento = $objTramiteController->enviarDepartamentos(
                        $listArrPara, 
                        $listArrCopia, 
                        $detalleTramite->iddetalle_tramite,
                        $detalle_tramite_atender->tramite->idtipo_tramite
                    );

                    if($envioDepartamento->status == "error"){
                        // eliminamos los departamentos destino se si se pudieron registrar
                        $destino_eliminar = td_DestinoModel::where("iddetalle_tramite", $detalleTramite->iddetalle_tramite);
                        $destino_eliminar->delete();

                        // eliminamos los documentos generados
                        $objTramiteController->eliminarDocumentosRegistrados($listaNombreDocDel, $detalleTramite, $tramite);

                        $mensajeError = "No se pudo registrar los departamentos destino.";
                        goto RETORNARERROR;
                    }

                // ------------- actualizamos el detalle atendido ----------------------------------
            
                    // actualizamos el estado del destino
                    DB::table('td_destino')
                        ->where('iddetalle_tramite', $detalle_tramite_atender->iddetalle_tramite)
                        ->where('iddepartamento', $iddepartamentoLogueado)
                        ->update(['estado' => 'A']);

                    //veririficamos si el tramite fué atendido en todos sus destinos
                    $destinosNoAtendidos = td_DestinoModel::where('iddetalle_tramite', $detalle_tramite_atender->iddetalle_tramite) // todos sus destino
                        ->where(function($query){
                            $query->where('estado', "B") // destino en borrador
                                ->orWhere('estado', "P"); // destino enviado y no antendido
                        })->where('tipo_envio', 'P') // solo importan los destinos enviados como para (las copias no se atienden)
                        ->first();

                    if(is_null($destinosNoAtendidos)){ // trámite atendido en todos sus departamentos destino
                        $detalle_tramite_atender->estado="A";
                        $detalle_tramite_atender->save();   
                    }     
            
                // ------------- si se realiza con exito --------------------------------------------

                    DB::commit();
                    return response()->json([
                        "error" => false,
                        "resultado" => [
                            "status" => "success",
                            "mensaje" => "Trámite registrdo con éxito",
                            "codTramite" => $tramite->codTramite,
                            "iddetalle_tramite_encrypt" => encrypt($detalleTramite->iddetalle_tramite)
                        ]
                    ]);
                    
                RETORNARERROR:
                    
                    DB::rollback(); 
                    return response()->json([
                        "error" => true,
                        "resultado" => [
                            "status" => "error",
                            "mensaje" => $mensajeError
                        ]
                    ]);

            }catch (\Throwable $th){
                
                Log::error("TdDetalleTramiteController => registrarAtencion | Mensaje => ".$th->getMessage());

                DB::rollback();
                return response()->json([
                    "error" => true,
                    "resultado" => [
                        "status" => "error",
                        "mensaje" => "No se puede ingresar el trámite"
                    ]
                ]);
               
            }

        }



    // ---------- MÉTODOS PARA TERMINAR UN TRÁMITE ----------------------------------------------

        #valida si el depatamento puede finalizar o no el trámite
        public function verificarTramiteTerminar($iddetalle_tramite)
        {
            try{

                //---------- variables a retornar ----------
                    $listaTipoDocumentos = [];
                    $detalle_tramite = null; // info del detalle tramite que se está terminando
                    $flujoDefinido = false; // para verificar si es flujo definido o no
                    $flujo = null; // para almacenar el flujo
                    $status = "success"; // el estado de la validacion 

                $detalle_tramite = td_DetalleTramiteModel::with('tramite','flujo','destino')
                    ->where('iddetalle_tramite', $iddetalle_tramite)
                    ->first();

                $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];

                if(is_null($detalle_tramite)){
                    $status = "error";
                    goto FINALDELTODO;
                }

                $idflujo = $detalle_tramite->idflujo;

                if(!is_null($idflujo)){ // FLUJO DEFINIDO

                    $flujoDefinido = true;

                    $flujoTermiar = td_FlujoModel::where('idflujo', $idflujo)->first();
                    $flujoDepaActual = null;
                    
                    if($flujoTermiar->tipo_flujo == "D"){ // es el ultimo del flujo departamental
                    
                        $status = "error"; // no puede finalizar le proceso desde este departamento
                        goto FINALDELTODO;

                    }else{ // es un flujo general

                        $flujoDepaActual = td_FlujoModel::with('tipo_documento_flujo')
                            ->where('idtipo_tramite', $flujoTermiar->idtipo_tramite) // mismo tipo de tramite
                            ->where('iddepartamento', $iddepartamentoLogueado) // destino departamento actual
                            ->where('estado_finalizar',1) // que este definido para finalizar
                            ->where('idflujo_padre', $idflujo) // que se hijo del flujo que estamos terminando
                            ->first();

                    }

                    if(is_null($flujoDepaActual)){
                        $status = "error"; // no puede finalizar le proceso desde este departamento
                        goto FINALDELTODO;
                    }

                    
                    $destinoActual = td_DestinoModel::where('iddetalle_tramite', $detalle_tramite->iddetalle_tramite)
                        ->where('iddepartamento', $iddepartamentoLogueado)
                        ->where('estado','P')
                        ->first();
                    if(is_null($destinoActual)){
                        $status = "error";
                        goto FINALDELTODO;
                    }

                    $flujo = $flujoDepaActual;

                    $listaTipoDocumentos = td_TipoDocumentoModel::with('tipo_documento_flujo')
                        ->whereHas('tipo_documento_flujo',function($query) use($flujoDepaActual){
                            $query->where('idflujo',$flujoDepaActual->idflujo);
                        })->get();;

                }else{ // FLUJO NO DEFINIDO

                    $destinoActual = td_DestinoModel::where('iddetalle_tramite', $detalle_tramite->iddetalle_tramite)
                        ->where('iddepartamento', $iddepartamentoLogueado)
                        ->where('estado','P')
                        ->first();
                    if(is_null($destinoActual)){
                        $status = "error";
                        goto FINALDELTODO;
                    }

                    //filtramos todos los tipos de documentos ligados al departamento
                    $listaTipoDocumentos = td_TipoDocumentoModel::with('estructura_documento')
                        // ->where('prioridad',0) // solo los documento secundarios
                        ->where('estado',1) // solo los que no estan eliminados
                        ->get();
                }

                FINALDELTODO:

                $retornar = collect();
                $retornar->listaTipoDocumentos = $listaTipoDocumentos;
                $retornar->detalle_tramite = $detalle_tramite;
                $retornar->flujoDefinido = $flujoDefinido;
                $retornar->flujo = $flujo;
                $retornar->status = $status;

                return $retornar;

            } catch (\Throwable $th) {
                Log::error("TdDetalleTramiteController => verificarTramiteTerminar => Mensaje => ".$th->getMessage());
                $retornar = collect();
                $retornar->listaTipoDocumentos = [];
                $retornar->detalle_tramite = null;
                $retornar->flujoDefinido = false;
                $retornar->flujo = null;
                $retornar->status = "error";
            }



        }


        #retorna la vista para terminar tramite
        public function terminarTramite()
        {
            $iddetalle_tramite = $_GET["iddetalle_tramite"];
            $iddetalle_tramite = decrypt($iddetalle_tramite);

            $resultadoValidar = $this->verificarTramiteTerminar($iddetalle_tramite);
            if($resultadoValidar->status=="error"){
                return view("error");
            }

            return view('tramitesDepartamentales.detalleTramite.terminar.terminarTramite')->with([
                'detalle_tramite' => $resultadoValidar->detalle_tramite,
                'listaTipoDocumentos' => $resultadoValidar->listaTipoDocumentos
            ]);
        }


        #registra la terminación del tramite
        public function registrarTerminacion(Request $request, $iddetalle_tramite)
        {

            try {

                $iddetalle_tramite = decrypt($iddetalle_tramite);
                $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];

                $detalle_tramite_terminar = td_DetalleTramiteModel::with('tramite', 'destino')
                    ->where('iddetalle_tramite', $iddetalle_tramite)
                    ->first();

                # buscamos el destimo que estamos atendiendo
                $destino_atender = null;
                foreach ($detalle_tramite_terminar->destino as $des => $destino){
                    if($destino->iddepartamento == $iddepartamentoLogueado){
                        $destino_atender = $destino;
                    }
                }

                $resultadoValidar = $this->verificarTramiteTerminar($iddetalle_tramite);
                $objTramiteController = new TdTramiteController(); // seteamos un objeto del controlador  TdTramiteController para reutilizar sus metodos
                $idflujo_terminar = null; // importante que este null por defecto

                $mensajeError = "";

                //------------ verificamos que el departamento pueda terminar --------------------------------

                    if($resultadoValidar->status == "error"){
                        $mensajeError = "Error al realizar el registro";
                        goto RETORNARERROR;
                    }

                //------------ validar caracteres especiales -------------------------------------------------
                    $dataValidar = [
                        "detalle_terminacion" => $request->detalle_terminacion,
                        "input_codigo_documento_adjunto" => $request->input_codigo_documento_adjunto,
                        "input_descripcion_documento_adjunto" => $request->input_descripcion_documento_adjunto
                    ];
                    $reglatexto = 'required|string|regex:/^[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ\s+\-.]+$/';
                    $validator = Validator::make($dataValidar, [
                        "detalle_terminacion" => $reglatexto,
                        "input_codigo_documento_adjunto.*" => $reglatexto,
                        "input_descripcion_documento_adjunto.*" => $reglatexto
                    ]);
                
                    if ($validator->fails()) {
                        $mensajeError = "No se permite ingresar caracteres especiales";
                        goto RETORNARERROR;
                    }
                
                //------------ validamos documentos solo si tiene flujo o sin flujo --------------------------

                    if($resultadoValidar->flujoDefinido == true){ // TIENE FLUJO DEFINIDO

                        #obtenemos el id del flujo
                        $idflujo_terminar = $resultadoValidar->flujo->idflujo;

                        #verificar que envie los documentos obligados
                        foreach($resultadoValidar->listaTipoDocumentos as $td => $tipo_documento){ // tipo_documentos oblidados

                            if(isset($request->input_id_tipo_documento_adjunto)){
                                foreach ($request->input_id_tipo_documento_adjunto as $tda => $idtipo_documento_adjunto){ // tipo_documentos adjuntos
                                    $idtipo_documento_adjunto = decrypt($idtipo_documento_adjunto);
                                    if($tipo_documento->idtipo_documento == $idtipo_documento_adjunto){
                                        // si está agregado
                                        goto SIGUIENTE;
                                    }
                                }
                            }

                            // si llega aquí el tipo de documento no se agregó (por lo tanto falta por adjuntar)
                            $mensajeError = "Faltan documentos por adjuntar";
                            goto RETORNARERROR;
                            break;

                            SIGUIENTE:
                        }
                    }
                
                // ----------- registramos el detalle_tramite terminado --------------------------------------
                    
                    $detalleTramite_finalizado = new td_DetalleTramiteModel();
                    $detalleTramite_finalizado->fecha = date("Y-m-d H:i:s");
                    $detalleTramite_finalizado->asunto =  strtoupper("TRAMITE ".$detalle_tramite_terminar->tramite->codTramite." FINALIZADO");
                    $detalleTramite_finalizado->observacion = strtoupper($request->detalle_terminacion);
                    $detalleTramite_finalizado->estado = "F"; // lo dejamos como finalizado (sin destinos)
                    $detalleTramite_finalizado->aprobado = 1;
                    $detalleTramite_finalizado->idtramite = $detalle_tramite_terminar->tramite->idtramite;
                    $detalleTramite_finalizado->iddepartamento_origen = $iddepartamentoLogueado;
                    $detalleTramite_finalizado->nivelAtencion = ($detalle_tramite_terminar->nivelAtencion+1);
                    $detalleTramite_finalizado->idflujo = $idflujo_terminar;
                    $detalleTramite_finalizado->fechaAtiende = date("Y-m-d H:i:s");
                    $detalleTramite_finalizado->idus001Atiende = auth()->user()->idus001;
                    $detalleTramite_finalizado->iddetalle_tramite_padre = $detalle_tramite_terminar->iddetalle_tramite;
                    $detalleTramite_finalizado->iddestino_atendido = $destino_atender->iddestino;
                    $detalleTramite_finalizado->save();
                
                // ----------- almacenamos los documentos ----------------------------------------------------
                    
                    if(isset($request->file_documento_adjunto)){

                        $listaTipoDocumentoAdj = [];
                        foreach ($request->input_id_tipo_documento_adjunto as $td => $idtipo_documento) {
                            array_push($listaTipoDocumentoAdj, decrypt($idtipo_documento));
                        }

                        $adjuntarDocumentos = $objTramiteController->adjuntarDocumentos(
                            $request->file_documento_adjunto,
                            $listaTipoDocumentoAdj,
                            $request->input_codigo_documento_adjunto,
                            $request->input_descripcion_documento_adjunto,
                            $detalleTramite_finalizado->iddetalle_tramite
                        ); 

                        $listaNombreDocDel = $adjuntarDocumentos->listaNombreDoc; // almacenamos los nombre de los documentos adjuntos

                        if($adjuntarDocumentos->status=="error"){
                            // eliminamos los documentos generados
                            $objTramiteController->eliminarDocumentosRegistrados($listaNombreDocDel, $detalleTramite_finalizado, null);

                            $mensajeError = "No se pudo registrar los documentos adjuntos.";
                            goto RETORNARERROR;
                        }
                    
                    }    
                
                // ----------- actualizamos el detalle atendido ----------------------------------------------
                
                    // actualizamos el estado del destino
                    DB::table('td_destino')
                        ->where('iddetalle_tramite', $detalle_tramite_terminar->iddetalle_tramite)
                        ->where('iddepartamento', $iddepartamentoLogueado)
                        ->update(['estado' => 'A']);

                    //verificamos si el tramite fué atendido en todos sus destinos
                    $destinosNoAtendidos = td_DestinoModel::where('iddetalle_tramite', $detalle_tramite_terminar->iddetalle_tramite) // todos sus destino
                        ->where('estado', '<>', "A") // destino que no estan atendidos
                        ->first();

                    if(is_null($destinosNoAtendidos)){ // trámite atendido en todos sus departamentos destino
                        $detalle_tramite_terminar->estado="A";
                        $detalle_tramite_terminar->save();   
                    }   
                
                // ----------- verificamos si esta finalizado en todos sus nodos -----------------------------

                    $tramite_terminar = td_TramiteModel::where('idtramite',  $detalle_tramite_terminar->tramite->idtramite)->first();

                    if($resultadoValidar->flujoDefinido == true){ #TIENE FLUJO DEFINIDO

                        #obtenemos los id de los flujos que se tiene que finalizar
                        $flujos_finalizar = td_FlujoModel::where('tipo_flujo', "G") #solo nodos de los flujos general
                            ->where('idtipo_tramite', $tramite_terminar->idtipo_tramite) #flujo del tipo de tramite por terminar
                            ->where('estado_finalizar', 1) #solo los nodos que deben finalizar
                            ->pluck('idflujo');

                        $num_flujos_finalizar = sizeof($flujos_finalizar); #deben existir este numero de detalle_tramite con estado == F (finalizado)

                        #obtenemos del detalle_tramite finalizados
                        $detalle_tramite_finalizados = td_DetalleTramiteModel::where('idtramite', $tramite_terminar->idtramite) #los detalles del tramite
                            ->whereIn('idflujo', $flujos_finalizar) #los detalles registrados de los flujos finalizar
                            ->get();
                        
                        $num_detalle_tramite_finalizados = sizeof($detalle_tramite_finalizados); #deben existir el mismo numero de $num_flujos_finalizar

                        if($num_flujos_finalizar == $num_detalle_tramite_finalizados){ // todos los detalles estan finalizados
                            $tramite_terminar->finalizado = 1;
                            $tramite_terminar->idus001_termina = auth()->user()->idus001;
                            $tramite_terminar->iddepartamento_temina = $iddepartamentoLogueado;
                            $tramite_terminar->save();
                        }

                        
                    }else{ #NO TIENE UN FLUJO DEFINIDO
                        Log::info("flujo no definido deveria terminarlo");
                        $detalle_tramite_finalizados = td_DetalleTramiteModel::where('idtramite', $tramite_terminar->idtramite) #los detalles del tramite
                            ->where(function($query){
                                $query->where('estado', 'B') #tramite en borrados (editandose)
                                    ->orWhere('estado', 'T') #tramite en bandeja de un departamento
                                    ->orWhere('estado', 'R'); #tramite en revisión
                            })
                            ->first();
                        
                        if(is_null($detalle_tramite_finalizados)){
                            $tramite_terminar->finalizado = 1;
                            $tramite_terminar->idus001_termina = auth()->user()->idus001;
                            $tramite_terminar->iddepartamento_temina = $iddepartamentoLogueado;
                            $tramite_terminar->save();
                        }
                    }

                // ----------- retorno final xd --------------------------------------------------------------
                
                return response()->json([
                    'error' => false,
                    'status' => "success",
                    'mensaje' => "Trámite finalizado correctamente"
                ]);

                RETORNARERROR: #salida por error
                return response()->json([
                    'error' => true,
                    'status' => "error",
                    'mensaje' => $mensajeError
                ]);
                
            } catch (\Throwable $th) {
                Log::error("TdDetalleTramiteController => registrarTerminacion => Mensaje => ".$th->getMessage());
                return response()->json([
                    'error' => true,
                    'status' => "error",
                    'mensaje' => "Error al relaizar el registro de la finalización",
                    'erroInfo' => $th->getMessage()
                ]);
        
            }


        }



    // ---------- MÉTODOS PARA DEVOLVER UN TRÁMITE ----------------------------------------------

        public function devolverTramite(Request $request, $iddetalle_tramite){
            
            try {

                $iddetalle_tramite = decrypt($iddetalle_tramite);
                $mensajeError = "";
                $detalleTramite = td_DetalleTramiteModel::where('iddetalle_tramite', $iddetalle_tramite)->first();

                if(is_null($detalleTramite)){ // error al encontrar el documento
                    goto RETORNARERROR;
                    $mensajeError = "Trámite no encontrado";
                }else{

                    //buscamos los destinos atendidos
                    $destinosAtendidos  = td_DestinoModel::where('iddetalle_tramite', $iddetalle_tramite)
                        ->where('estado', '<>', 'P') // solo los tramite no atendos
                        ->first();

                    if(is_null($destinosAtendidos)){
                        //actualizamos el estado de los destinos                            
                        DB::table('td_destino')
                        ->where('iddetalle_tramite', $iddetalle_tramite)
                        ->update(['estado' => 'B']);

                        $detalleTramite->aprobado = 0;
                        $detalleTramite->estado = "R";
                        $detalleTramite->detRevision = strtoupper($request->textarea_detalle_revision); 
                        $detalleTramite->save();                    
                    }else{
                        goto RETORNARERROR;
                        $mensajeError = "No puede devolver el trámite porque ya fué atendido en otros departamentos";
                    }

                    return response()->json([
                        'error' => false,
                        'resultado' => [
                            'status' => "success",
                            'mensaje' => "Trámite devuelto correctamente"                        
                        ]

                    ]);

                }

                RETORNARERROR:
                return response()->json([
                    "error" => true,
                    "resultado" =>[
                        "mensaje" => $mensajeError,
                        "status" => "error"
                    ]
                ]); 

            }catch(\Throwable $th) {
                Log::error("TdDetalleTramiteController => devolverTramite => Mensaje => ".$th->getMessage());

                return response()->json([
                    'error' => true,
                    'resultado' => [
                        'status' => "error",
                        'mensaje' => "No se pudo devolver el trámite"
                    ]
                ]);
            }

        }

    // ---------- MÉTODOS PARA REVERTIR UN TRÁMITE ----------------------------------------------

        public function revertirTramite($iddetalle_tramite_encrypt){

            $iddetalle_tramite = decrypt($iddetalle_tramite_encrypt);
            $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];
            $objTramiteController = new TdTramiteController();
            $mensajeError = "";

            //buscamos el detalle actual con estado de finalizado
            $detalleTramite = td_DetalleTramiteModel::with("tramite", "departamento_origen", "documento", "detalle_tramite_padre")
                ->where('iddetalle_tramite', $iddetalle_tramite)
                ->first();

            if(is_null($detalleTramite)){
                $mensajeError = "Trámite no encontrado";
                goto RETORNARERROR;
            }

            // buscamos el detalle anterior
            $detalleTramiteAnterior = td_DetalleTramiteModel::where('iddetalle_tramite', $detalleTramite->detalle_tramite_padre->iddetalle_tramite)->first();

            // buscamos el destino del departamento actual del detalle anterior
            $destinoActual = td_DestinoModel::where('iddetalle_tramite', $detalleTramiteAnterior->iddetalle_tramite)
                ->where('iddepartamento', $iddepartamentoLogueado)
                ->first();

            if(is_null($destinoActual)){
                $mensajeError = "Trámite no encontrado 2";
                goto RETORNARERROR;
            }

            //regresamos el destino a la bandeja
            $destinoActual->estado = "P";
            $destinoActual->save();

            //regresamos el detalle previamente atendido
            $detalleTramiteAnterior->estado = "T";
            $detalleTramiteAnterior->save();

            //regresamos a la normalida el trámite
            $tramiteGeneral = $detalleTramite->tramite;
            $tramiteGeneral->finalizado="0";
            $tramiteGeneral->save();

            //borramos todos los documentos adjuntos al detalle tramite finalizado
            $listaDocEliminar = [];
            foreach ($detalleTramite->documento as $doc => $documento){
                array_push($listaDocEliminar, $documento->rutaDocumento);
            }

            $objTramiteController->eliminarDocumentosRegistrados($listaDocEliminar, $detalleTramite, null);
            
            return response()->json([
                "error" => false,
                "resultado" => [
                    "status" => "success",
                    "mensaje" => "Trámite revertido con exito"
                ]
            ]);

            RETORNARERROR:
            return response()->json([
                "error" => true,
                "resultado" => [
                    "status" => "error",
                    "mensaje" => $mensajeError
                ]
            ]);

        }

    // ---------- MÉTODOS PARA ELIMNAR UN DETALLE TÁMITE ----------------------------------------

        public function eliminar($iddetalle_tramite_encrypt){
           
            $iddetalle_tramite = decrypt($iddetalle_tramite_encrypt);
            $detalle_tramite = td_DetalleTramiteModel::with('documento','destino','tramite','detalle_tramite_padre', 'destino_atendido')->where('iddetalle_tramite', $iddetalle_tramite)->first();

            //primero eliminamos los documentos (fisicos y en base de datos)

                foreach ($detalle_tramite->documento as $doc => $documentoDel){
                    if($documentoDel->tipo_creacion == "E"){ // es un documento principal (eliminamos del disco)
                        $exists = Storage::disk('disksServidorSFTPborradores')->exists($documentoDel->rutaDocumento.".pdf");
                        if($exists){                
                            Storage::disk('disksServidorSFTPborradores')->delete($documentoDel->rutaDocumento.".pdf");
                        }

                        $exists = Storage::disk('disksServidorSFTPborradores')->exists($documentoDel->rutaDocumento.".txt");
                        if($exists){                
                            Storage::disk('disksServidorSFTPborradores')->delete($documentoDel->rutaDocumento.".txt");
                        }
                    }
                    $documentoDel->delete();
                }

            //eliminamos los destinos registrados en base de datos
                foreach ($detalle_tramite->destino as $des => $destinoDel){
                    $destinoDel->delete();
                }

            //verificamos si el el primer detalle para eliminar el trámite general y eliminar los interesados

                if($detalle_tramite->nivelAtencion == 1){ //rerminados con todos rastro del trámite
                    $tramite = $detalle_tramite->tramite;
                    $interesados = td_TramiteInteresadoModel::where('idtramite',$tramite->idtramite);
                    $interesados->delete();
                    $detalle_tramite->delete();
                    $tramite->delete();
                }else{ //eliminamos el detalle y actualizamos el detalle atendido
                    
                    $destino_atendido = $detalle_tramite->destino_atendido;
                    $destino_atendido->estado = "P";
                    $destino_atendido->save();

                    $detalle_tramite_padre = $detalle_tramite->detalle_tramite_padre;
                    $detalle_tramite_padre->estado = "T";
                    $detalle_tramite_padre->save();

                    $detalle_tramite->delete();
                }

            return back();
        }
    

    //----------- MÉTODOS PARA RECUPERAR UN TRÉMITE (TRAER DEL DESTINO AL DEPARTAMENTO ORIGEN) --
        
        public function recuperarTramite($iddetalle_tramite){
                
            try {

                $iddetalle_tramite = decrypt($iddetalle_tramite);
                $mensajeError = "";
                $detalleTramite = td_DetalleTramiteModel::with('destino')->where('iddetalle_tramite', $iddetalle_tramite)->first();

                if(is_null($detalleTramite)){ // error al encontrar el documento
                    goto RETORNARERROR;
                    $mensajeError = "Trámite no encontrado";
                }else{

                    //buscamos los destinos atendidos
                    $destinosAtendidos  = td_DestinoModel::where('iddetalle_tramite', $iddetalle_tramite)
                        ->where('estado', '<>', 'P') // solo los tramite no atendos
                        ->first();

                    if(is_null($destinosAtendidos)){
                        //actualizamos el estado de los destinos                            
                        DB::table('td_destino')
                        ->where('iddetalle_tramite', $iddetalle_tramite)
                        ->update(['estado' => 'B']);

                        $detalleTramite->aprobado = 0; // ya no estrá aprobado por el jefe del departamento
                        $detalleTramite->estado = "B"; // regresamos a la bandeja de borradores
                        $detalleTramite->save();
                        
                        //VOLVER A GENERAR EL DOCUMENTO PRINCIPAL

                        $documento = td_DocumentoModel::where('iddetalle_tramite', $iddetalle_tramite)->where('tipo_creacion','E')->first();
                        $documento->firmado = 0;
                        $documento->save();    
    
                        $texto_documento =  Storage::disk('disksServidorSFTPborradores')->get($documento->rutaDocumento.".txt");
                        $listArrPara = [];
                        $listArrCopia = [];
                        
                        foreach ($detalleTramite->destino as $des => $destino){
                            if($destino->tipo_envio=="P"){
                                array_push($listArrPara, $destino->iddepartamento);
                            }else if($destino->tipo_envio=="C"){
                                array_push($listArrCopia, $destino->iddepartamento);
                            }
                        }
    
                        $listaAnexos = td_DocumentoModel::where('iddetalle_tramite',$iddetalle_tramite)
                            ->where('tipo_creacion', 'A')
                            ->pluck('codigoDocumento')
                            ->toArray();
    
                        // agregamos el para, codigo documento y copia
                        $obj_texto_generado = getInfoDocumento(
                            $texto_documento,
                            $detalleTramite->asunto,
                            $listArrPara,
                            $listArrCopia,
                            $documento->idtipo_documento,
                            $documento->numReferencia,
                            $listaAnexos,
                            false //mostrar que esta firmado electrónicamente
                        );
    
                        // CREAR EL DOCUMENTO EL PDF =======================================
                            $borrador = true; // agregar marca de agua
                            $pdf = getEstructuraDocumento($obj_texto_generado->texto_documento_completo, $borrador);
                            $documentoListo = $pdf->stream();
                            Storage::disk('disksServidorSFTPborradores')->put(str_replace("", "",$documento->rutaDocumento.".pdf"), $documentoListo); // guardamos el documentos
                        // ===============================================================================
    
                        
                    }else{
                        goto RETORNARERROR;
                        $mensajeError = "No puede recuperar el trámite porque ya fué atendido en otros departamentos";
                    }

                    return response()->json([
                        'error' => false,
                        'resultado' => [
                            'status' => "success",
                            'mensaje' => "Trámite recuperado correctamente"                        
                        ]

                    ]);

                }

                RETORNARERROR:
                return response()->json([
                    "error" => true,
                    "resultado" =>[
                        "mensaje" => $mensajeError,
                        "status" => "error"
                    ]
                ]); 

            }catch(\Throwable $th) {
                Log::error("TdDetalleTramiteController => recuperarTramite => Mensaje => ".$th->getMessage());

                return response()->json([
                    'error' => true,
                    'resultado' => [
                        'status' => "error",
                        'mensaje' => "No se pudo recuperar el trámite"
                    ]
                ]);
            }

        }

    // ---------- MÉTODOS PARA DENEGAR UN TRÁMITE ----------------------------------------------

        #retorna la vista para denegar tramite
        public function denegarTramite(){

            try{

                $iddetalle_tramite = $_GET["iddetalle_tramite"];
                $iddetalle_tramite = decrypt($iddetalle_tramite);
    
                $detalle_tramite = td_DetalleTramiteModel::where('iddetalle_tramite', $iddetalle_tramite)->first();
                if(is_null($detalle_tramite)){
                    return view("error");
                }

                //filtramos todos los tipos de documentos ligados al departamento
                $listaTipoDocumentos = td_TipoDocumentoModel::with('estructura_documento')
                    // ->where('prioridad',0) // solo los documento secundarios
                    ->where('estado',1) // solo los que no estan eliminados
                    ->get();
    
                return view('tramitesDepartamentales.detalleTramite.denegar.denegarTramite')->with([
                    'detalle_tramite' => $detalle_tramite,
                    'listaTipoDocumentos' => $listaTipoDocumentos
                ]);

            }catch(\Throwable $th){
                Log::error("TdDetalleTramiteController => denegarTramite => Mensaje: ".$th->getMessage());
                abort(500);
            }

        }       

        #registra la denegación del tramite
        public function registrarDenegacion(Request $request, $iddetalle_tramite)
        {

            try{

                $iddetalle_tramite = decrypt($iddetalle_tramite);
                $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];

                $detalle_tramite_terminar = td_DetalleTramiteModel::with('tramite', 'destino')
                    ->where('iddetalle_tramite', $iddetalle_tramite)
                    ->first();

                # buscamos el destimo que estamos atendiendo
                $destino_atender = null;
                foreach ($detalle_tramite_terminar->destino as $des => $destino){
                    if($destino->iddepartamento == $iddepartamentoLogueado){
                        $destino_atender = $destino;
                    }
                }
                
                $objTramiteController = new TdTramiteController(); // seteamos un objeto del controlador  TdTramiteController para reutilizar sus metodos
                $mensajeError = "";

                //------------ validar caracteres especiales -------------------------------------------------
                    $dataValidar = [
                        "detalle_terminacion" => $request->detalle_denegacion,
                        "input_codigo_documento_adjunto" => $request->input_codigo_documento_adjunto,
                        "input_descripcion_documento_adjunto" => $request->input_descripcion_documento_adjunto
                    ];
                    $reglatexto = 'required|string|regex:/^[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ\s+\-.]+$/';
                    $validator = Validator::make($dataValidar, [
                        "detalle_terminacion" => $reglatexto,
                        "input_codigo_documento_adjunto.*" => $reglatexto,
                        "input_descripcion_documento_adjunto.*" => $reglatexto
                    ]);
                
                    if ($validator->fails()) {
                        $mensajeError = "No se permite ingresar caracteres especiales";
                        goto RETORNARERROR;
                    }
                
                
                // ----------- registramos el detalle_tramite terminado --------------------------------------
                    
                    $detalleTramite_finalizado = new td_DetalleTramiteModel();
                    $detalleTramite_finalizado->fecha = date("Y-m-d H:i:s");
                    $detalleTramite_finalizado->asunto =  strtoupper("TRAMITE ".$detalle_tramite_terminar->tramite->codTramite." DENEGADO");
                    $detalleTramite_finalizado->observacion = strtoupper($request->detalle_denegacion);
                    $detalleTramite_finalizado->estado = "D"; // lo dejamos como denegado (sin destinos)
                    $detalleTramite_finalizado->aprobado = 1;
                    $detalleTramite_finalizado->idtramite = $detalle_tramite_terminar->tramite->idtramite;
                    $detalleTramite_finalizado->iddepartamento_origen = $iddepartamentoLogueado;
                    $detalleTramite_finalizado->nivelAtencion = ($detalle_tramite_terminar->nivelAtencion+1);
                    $detalleTramite_finalizado->fechaAtiende = date("Y-m-d H:i:s");
                    $detalleTramite_finalizado->idus001Atiende = auth()->user()->idus001;
                    $detalleTramite_finalizado->iddetalle_tramite_padre = $detalle_tramite_terminar->iddetalle_tramite;
                    $detalleTramite_finalizado->iddestino_atendido = $destino_atender->iddestino;
                    $detalleTramite_finalizado->save();
                
                // ----------- almacenamos los documentos ----------------------------------------------------
                    
                    if(isset($request->file_documento_adjunto)){

                        $listaTipoDocumentoAdj = [];
                        foreach ($request->input_id_tipo_documento_adjunto as $td => $idtipo_documento) {
                            array_push($listaTipoDocumentoAdj, decrypt($idtipo_documento));
                        }

                        $adjuntarDocumentos = $objTramiteController->adjuntarDocumentos(
                            $request->file_documento_adjunto,
                            $listaTipoDocumentoAdj,
                            $request->input_codigo_documento_adjunto,
                            $request->input_descripcion_documento_adjunto,
                            $detalleTramite_finalizado->iddetalle_tramite
                        ); 

                        $listaNombreDocDel = $adjuntarDocumentos->listaNombreDoc; // almacenamos los nombre de los documentos adjuntos

                        if($adjuntarDocumentos->status=="error"){
                            // eliminamos los documentos generados
                            $objTramiteController->eliminarDocumentosRegistrados($listaNombreDocDel, $detalleTramite_finalizado, null);

                            $mensajeError = "No se pudo registrar los documentos adjuntos.";
                            goto RETORNARERROR;
                        }
                    
                    }    
                
                // ----------- actualizamos el detalle atendido ----------------------------------------------
                
                    // actualizamos el estado del destino
                    DB::table('td_destino')
                        ->where('iddetalle_tramite', $detalle_tramite_terminar->iddetalle_tramite)
                        ->where('iddepartamento', $iddepartamentoLogueado)
                        ->update(['estado' => 'A']);

                    //verificamos si el tramite fué atendido en todos sus destinos
                    $destinosNoAtendidos = td_DestinoModel::where('iddetalle_tramite', $detalle_tramite_terminar->iddetalle_tramite) // todos sus destino
                        ->where('estado', '<>', "A") // destino que no estan atendidos
                        ->first();

                    if(is_null($destinosNoAtendidos)){ // trámite atendido en todos sus departamentos destino
                        $detalle_tramite_terminar->estado="A";
                        $detalle_tramite_terminar->save();   
                    }
                
                // ----------- finalizamos por completo todo el trámite -----------------------------

                    $tramite_terminar = td_TramiteModel::where('idtramite',  $detalle_tramite_terminar->tramite->idtramite)->first();                  
                    $tramite_terminar->finalizado = 1;
                    $tramite_terminar->idus001_termina = auth()->user()->idus001;
                    $tramite_terminar->iddepartamento_temina = $iddepartamentoLogueado;
                    $tramite_terminar->save();                                   

                // ----------- retorno final xd --------------------------------------------------------------
                
                return response()->json([
                    'error' => false,
                    'status' => "success",
                    'mensaje' => "Trámite denegado correctamente"
                ]);

                RETORNARERROR: #salida por error
                return response()->json([
                    'error' => true,
                    'status' => "error",
                    'mensaje' => $mensajeError
                ]);
                
            }catch(\Throwable $th){
                Log::error("TdDetalleTramiteController => registrarDenegacion => Mensaje => ".$th->getMessage());
                return response()->json([
                    'error' => true,
                    'status' => "error",
                    'mensaje' => "Error al intentar denegar el trámite",
                    'erroInfo' => $th->getMessage()
                ]);
        
            }


        }
        

    //-------------------------------------------------------------------------------------------

}
