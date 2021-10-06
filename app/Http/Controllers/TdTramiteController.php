<?php

namespace App\Http\Controllers;

use App\td_TramiteModel;
use Illuminate\Http\Request;
use App\td_TipoTramiteModel;
use App\td_FormatoModel;
use App\td_FlujoModel;
use App\td_us001_tipofpModel;
use App\DepartamentoModel;
use App\td_TipoDocumentoModel;
use App\td_DocumentoModel;
use App\td_DetalleTramiteModel;
use App\td_DestinoModel;
use App\td_PrioridadTramiteModel;
use App\td_SecuenciasTramiteModel;
use App\ParametrosGeneralesModel;
use App\td_EstructuraDocumentoModel;
use App\td_TramiteInteresadoModel;
use App\Http\Controllers\TdDetalleTramiteController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use ZipArchive;
use PDF;
use DB;
use Log;


class TdTramiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

        private $clientGad = null;

        public function __construct()
        {
            try {
                $this->clientGad = new Client([
                    'base_uri' => env('URL_SERVICE_WSE'),
                    'verify' => false,
                ]);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }


    // retornar vista principal de iniciar un trámie
        public function index()
        {
            // buscamos el jefe de ese departamento
            $depLogueado = departamentoLogueado(); // obtenemos el departameto en el que esta logueado el usuario que va a acrear el tramite

            $jefeDepartamento = td_us001_tipofpModel::with('us001') // obtenemos el jefe de ese departamento
                    ->where('iddepartamento',$depLogueado['iddepartamento'])
                    ->where('jefe_departamento','1')
                    ->first();
            if(is_null($jefeDepartamento)){ // si no se encuentra nada no permitimos que ingrese en el modulo
                return redirect('home')->with([
                    "mensajeGeneral"=>"Por el momento usted no puede iniciar tramites ya que el departamento ".departamentoLogueado()["departamento"]." no tiene un jefe asignado.",
                    "status"=>"info"
                ]); 
            }else{
                $jefeDepartamento = $jefeDepartamento->us001;
            }

            // obtenemos el listado de todos los tramites globales y asociados al departamento
            $listaTipoTramites = td_TipoTramiteModel::with('tipotramite_departamento')
                ->where(function($queryTipDep) use ($depLogueado){
                    $queryTipDep->whereHas('tipotramite_departamento', function($queryTipoTraDepa) use ($depLogueado){ // obtenemos los tramites asocciados al departamento
                        $queryTipoTraDepa->where('iddepartamento', $depLogueado['iddepartamento']);
                    })
                    ->orWhere('tramite_global','1'); // o obtenemos los tramites globales                
                })
                ->where("estado",1)
                ->get();

            // lista de prioridades
            $listaPrioridad = td_PrioridadTramiteModel::all();

            // lista de documentos prioridad
            $listaDocPrioritarios = td_TipoDocumentoModel::where('prioridad',1)->where('estado', 1)->get();

            // $listaTipoTramites = td_TipoTramiteModel::all(); // para cargar el combo de seleccionar un tramites
            
            return view('tramitesDepartamentales.gestionTramite.gestionTramite')
                ->with([
                    'listaPrioridad' => $listaPrioridad,
                    'listaTipoTramites' => $listaTipoTramites,
                    'listaDocPrioritarios' => $listaDocPrioritarios,
                    'jefeDepartamento' => $jefeDepartamento
                ]);
        }

    // para verificar si un tramite seleccionado tiene un flujo definido
        public function verificarFlujoTipoTramite($id)
        {

            try{          
                
                $idtipo_tramite = decrypt($id); // desencriptamos el id del tipo de tramite seleccionado
                $listaTipoDocumentos = null; // variable para almacenar todos los tipos de documentos requeridos en el flujo

                // obtenemos el primer nodo del flujo (para verificar si hay nodo flujo definido)
                $primerNodoFlujoTramite = td_FlujoModel::with(['flujo_hijo', 'departamento'])
                    ->where('idtipo_tramite',$idtipo_tramite)
                    ->where("tipo_flujo", "G") // solo los trámites generales
                    ->orderBy('orden',"ASC")  // ordenamos de menor a mayor
                    ->first(); // solo obtenemos el primer elemento de la consulta

                if(is_null($primerNodoFlujoTramite)){ 
                    // si viene nulo no tiene un flujo definido
                    goto FLUJO_NO_DEFINIDO;
                }else{

                    
                    // verificamos si dicho flujo esta completado correctamente
                    $flujosNoFinalizados = td_FLujoModel::with('flujo_padre', 'flujo_hijo')
                        ->where('tipo_flujo', "G") // solo flujos general
                        ->whereDoesntHave('flujo_hijo') // nodo que no tenga hijos
                        ->where('tipo_envio', "P") // solo los nodos para
                        ->where('estado_finalizar', '<>', 1) // nodo que no esté finalizado
                        ->where('idtipo_tramite', $idtipo_tramite)
                        ->first();
                    

                    if(is_null($flujosNoFinalizados)){ // si no tiene nodos con hijos nulos el flujo general esta finalizado correctamente
                        
                        // verificamos si el departamento no es el primer nodo del flujo
                        $iddepartamentoLogueado = departamentoLogueado()["iddepartamento"];

                        // if($primerNodoFlujoTramite->iddepartamento != $iddepartamentoLogueado){ // en ese caso buscamos el flujo por departamento
                            
                        //     // reiniciamos el primer nodo del flujo 
                        //     $primerNodoFlujoTramite = td_FlujoModel::with(['flujo_hijo', 'departamento'])
                        //         ->where('idtipo_tramite',$idtipo_tramite)
                        //         ->where("tipo_flujo", "D") // solo los trámites generales
                        //         ->where("iddepartamento_propietario", $iddepartamentoLogueado)
                        //         ->orderBy('orden',"ASC")  // ordenamos de menor a mayor
                        //         ->first(); // solo obtenemos el primer elemento de la consulta

                        //         if(is_null($primerNodoFlujoTramite)){ 
                        //             // si viene nulo no tiene un flujo por departamento definido
                        //             goto FLUJO_NO_DEFINIDO;
                        //         }else{
                        //             $flujosFinalizados = td_FLujoModel::with('flujo_padre', 'flujo_hijo')
                        //                 ->where('tipo_flujo', "D") // solo flujos general
                        //                 ->where('estado_finalizar',1) // en este caso si un nodo esta finalizado se toma el flujo como completo
                        //                 ->where('tipo_envio', "P") // solo los nodos para
                        //                 ->where("iddepartamento_propietario", $iddepartamentoLogueado)
                        //                 ->where('idtipo_tramite', $idtipo_tramite)
                        //                 ->first();
        
                        //             if(is_null($flujosFinalizados)){ // si no existe entonces el flujo no esta completo
                        //                 goto FLUJO_NO_DEFINIDO; 
                        //             }
                        //             // si la comdicion no se comple pasa como flujo bien definido
                        //         }
                            

                        // }

                        //filtramos todos los tipos de documentos ligados al flujo
                        $listaTipoDocumentos = td_TipoDocumentoModel::with('tipo_documento_flujo')
                            ->whereHas('tipo_documento_flujo',function($query) use($primerNodoFlujoTramite){
                                $query->where('idflujo',$primerNodoFlujoTramite->idflujo);
                            })->get();
                        
                        //filtramos los departamento destino

                        return response()->json([
                            'error' => false,
                            'codigo' => 200,
                            'flujo'=>true,
                            'primerNodoFlujoTramite'=>$primerNodoFlujoTramite,
                            'listaTipoDocumentos'=>$listaTipoDocumentos
                        ]);  
                    }
                    // si no se comple pasa como flujo no definido          
                }

                FLUJO_NO_DEFINIDO:

                // preparamos informacion por si el filtro
                $objUs001_tipofp = td_us001_tipofpModel::where('idus001',auth()->user()->idus001)
                    ->where('idtipoFP',auth()->user()->idtipoFP)
                    ->first(); // filtramos la asignacion de tipoFP que el usuario tien asignado de momento
                
                // obtenemos el departamento en el que esta logueado el usuario para saber el id de su departamento padre
                $departamentoUsuario = DepartamentoModel::find($objUs001_tipofp->iddepartamento);

                //filtramos todos los tipos de documentos ligados al departamento
                $listaTipoDocumentos = td_TipoDocumentoModel::where('prioridad',0)->get();

                Log::info("flujo no definido");
                return response()->json([
                    'error' => false,
                    'codigo' => 200,
                    'flujo' =>false,
                    'listaTipoDocumentos'=>$listaTipoDocumentos
                ]);


            }catch (\Throwable $th) {
                Log::error("TdTramiteController => verificarFlujoTipoTramite => Mensaje ".$th->getMessage());
                return response()->json([
                    'error' => true,
                    'codigo' => 500,
                    'message' => $th->getMessage()
                ]); 
            }
    
        }
    

    // funciona para verificar si los departamento a enviar son los permitidos
        public function verificarNivelDepartamento($depaPara, $depaCopia)
        {

            try {
                
                // preparamos informacion por si el filtro de hace por departamento o po funcionario público
                $objUs001_tipofp = td_us001_tipofpModel::where('idus001',auth()->user()->idus001)
                ->where('idtipoFP',auth()->user()->idtipoFP)
                ->first(); // filtramos la asignacion de tipoFP que el usuario tien asignado de momento

                $departamentoUsuario = DepartamentoModel::find($objUs001_tipofp->iddepartamento); // departamento logueado
                $listArrPara = []; // lista de id departamento para enviar como 'PARA'
                $listArrCopia = []; // lista de id departamento para enviar como 'COPIA'

                foreach ($depaPara as $dp => $iddepartamento){
                    array_push($listArrPara, decrypt($iddepartamento));
                }

                foreach ($depaCopia as $dc => $iddepartamento) {
                    array_push($listArrCopia, decrypt($iddepartamento));
                }

                // contamos los deparamento que estan bien (segun su estructura jerarquica)
                $depaContCorrectos = DepartamentoModel::whereIn("iddepartamento", array_merge($listArrPara, $listArrCopia))
                ->where(function($query_depa) use($departamentoUsuario){
                    $query_depa->where("iddepartamento", $departamentoUsuario->iddepartamento_padre)// lo buscamos como padre
                    ->orWhere("iddepartamento_padre", $departamentoUsuario->iddepartamento_padre)// lo buscamos como hermano
                    ->orWhere("iddepartamento_padre", $departamentoUsuario->iddepartamento); // lo buscamos como hijo
                })->count();

                $status = "success"; // para decir si se puede enviar o no (false es cuando un departamento no correcto)
                $numDepa = sizeof($listArrPara) + sizeof($listArrCopia);
                if($depaContCorrectos != $numDepa){ $status="error"; }

                $retornar = collect();
                $retornar->listArrPara = $listArrPara;
                $retornar->listArrCopia = $listArrCopia;
                $retornar->status = $status;
                $retornar->correctos = $depaContCorrectos;

                return $retornar;

            } catch (\Throwable $th){
                Log::error("Error en TdTramiteController => verificarNivelDepartamento Mensaje => ".$th->getMessage());
                $retornar = collect();
                $retornar->status = false;
                return $retornar;
            }

        }

    // funcion crear, almacenar y registrar documentos creados con el editor de texto (registro en 'td_documento') retorna (true, false)
        public function crearDocumentoEditado($listaDocumentos, $iddetalle_tramite, $listaDescripDoc, $listaTipoDoc, $asunto, $listArrPara, $listArrCopia, $numReferencia, $listaAnexos)
        {
            try{
                
                $listaNombreDoc = []; // para almacenar los nombres de los documentos generados
                $iddepartamentoLogueado = departamentoLogueado()["iddepartamento"];

                foreach ($listaDocumentos as $td => $texto_documento){

                    // agregamos el para, codigo documento y copia
                    $obj_texto_generado = getInfoDocumento(
                        $texto_documento,
                        $asunto,
                        $listArrPara,
                        $listArrCopia,
                        $listaTipoDoc[$td],
                        $numReferencia,
                        $listaAnexos,
                        false //no mostrar que esta firmado electrónicamente
                    );

                    // CREAR EL DOCUMENTO EL PDF =======================================
                        $borrador = true; // agregar marca de agua
                        $pdf = getEstructuraDocumento($obj_texto_generado->texto_documento_completo, $borrador);
                        $documentoListo = $pdf->stream();
                    // ===============================================================================
                                            
                    // GUARDAMOS EL DOCUMENTO CREADO EN EL DISCO TEMPORAL ===========
                        $nombreDocumento = "DOC-CREADO-$td-".(auth()->user()->idus001).'-'.date('Ymd').'-'.time(); // creamos un nombre temporal
                        Storage::disk('disksServidorSFTPborradores')->put(str_replace("", "","$nombreDocumento.pdf"), $documentoListo);
                        Storage::disk('disksServidorSFTPborradores')->put(str_replace("", "","$nombreDocumento.txt"), $texto_documento);

                        // ---- generamos el codigo del documento ------------------------------------------
                            $anioAct = date("Y");
                            $estructuraDoc =  td_EstructuraDocumentoModel::with('tipo_documento', 'departamento')
                                ->where('iddepartamento', $iddepartamentoLogueado)
                                ->where('idtipo_documento', $listaTipoDoc[$td])
                                ->where('anio', $anioAct)
                                ->first(); // la secuencia ya se incrementa al crear el contenido del documento

                            $institucion = ParametrosGeneralesModel::where("codigo","INST")->first();
                            $institucion = $institucion->valor;

                            $codigoDocumento = $institucion.'-'.$estructuraDoc->departamento->abreviacion.'-'.$anioAct.'-'.$estructuraDoc->secuencia.'-'.$estructuraDoc->tipo_documento->abreviacion;
                            
                        // ---------------------------------------------------------------------------------

                        // buscamos el jefe de ese departamento
                        $depLogueado = departamentoLogueado();
                        $jefeDepLogueado = td_us001_tipofpModel::with('us001') // obtenemos el jefe de ese departamento
                            ->where('iddepartamento',$depLogueado['iddepartamento'])
                            ->where('jefe_departamento','1')
                            ->first();

                        $obj_documento = new td_DocumentoModel();
                        $obj_documento->rutaDocumento = $nombreDocumento;
                        $obj_documento->extension = "pdf";
                        $obj_documento->numReferencia = $numReferencia;
                        $obj_documento->codigoDocumento = $codigoDocumento;
                        $obj_documento->iddetalle_tramite = $iddetalle_tramite;
                        $obj_documento->idus001_de = $jefeDepLogueado->us001->idus001;
                        $obj_documento->descripcion = strtoupper($listaDescripDoc[$td]);
                        $obj_documento->fechaCarga = date("Y-m-d H:i");
                        $obj_documento->idtipo_documento = $listaTipoDoc[$td];
                        $obj_documento->tipo_creacion = "E";
                        $obj_documento->estado = "B";
                        $obj_documento->save();

                        array_push($listaNombreDoc, $nombreDocumento);
                }

                $retornar = collect();
                $retornar->status = "success";
                $retornar->listaNombreDoc = $listaNombreDoc;
                return $retornar; 

            } catch (\Throwable $th) {
                Log::error("Error TdTramiteController => crearDocumentoEditado Mensaje => ".$th->getMessage());
                $retornar = collect();
                $retornar->status = "error";
                $retornar->listaNombreDoc = $listaNombreDoc;
                return $retornar; 
            }
        }

    // funcion para almacenar y registrar documentos adjuntos (registro en 'td_documento') retorna (true, false)
        public function adjuntarDocumentos($file_documento_adjunto, $listaTipoDoc, $codigo_documento_adjunto, $descripcion_documento_adjunto, $iddetalle_tramite)
        {
            try {

                $listaNombreDoc = []; // para almacenar los nombres de los documentos adjuntos

                foreach ($file_documento_adjunto as $cont => $archivo){
                    // guardamos el documento en disco
                        $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
                        $nombreDocumento =  "DOC-ADJUNTO-$cont-".date('Ymd').'-'.time();
                        \Storage::disk('disksServidorSFTPborradores')->put($nombreDocumento.".".$extension,  \File::get($archivo));
                    // registramos los datos del documento en base de datos
                        $obj_documento = new td_DocumentoModel();
                        $obj_documento->rutaDocumento = $nombreDocumento;
                        $obj_documento->extension = $extension;
                        $obj_documento->iddetalle_tramite = $iddetalle_tramite;
                        $obj_documento->codigoDocumento = $codigo_documento_adjunto[$cont];
                        $obj_documento->descripcion = strtoupper($descripcion_documento_adjunto[$cont]);
                        $obj_documento->fechaCarga = date("Y-m-d H:i");
                        $obj_documento->idtipo_documento = $listaTipoDoc[$cont];
                        $obj_documento->tipo_creacion = "A";
                        $obj_documento->estado = "B";
                        $obj_documento->save();

                        array_push($listaNombreDoc, $nombreDocumento);
                }

                $retornar = collect();
                $retornar->status = "success";
                $retornar->listaNombreDoc = $listaNombreDoc;
                return $retornar;

            } catch (\Throwable $th) {
                Log::error("TdTramiteController => adjuntarDocumentos  Mensaje => ".$th->getMessage());

                $retornar = collect();
                $retornar->status = "error";
                $retornar->listaNombreDoc = $listaNombreDoc;
                return $retornar;
            }
        }

    // funcion para enviar a los departamentos (registro en 'td_destino') retorna (true, false)
        public function enviarDepartamentos($depaPara, $depaCopia, $iddetalle_tramite ,$idtipo_tramite)
        {
            try {
                // primero registramos los enviatos como "PARA"
                foreach ($depaPara as $c => $iddepartamento) {
                    $obj_destino = new td_DestinoModel();
                    $obj_destino->iddepartamento = $iddepartamento;
                    $obj_destino->iddetalle_tramite = $iddetalle_tramite;
                    $obj_destino->estado = "B"; // tramite en borrador (no aparecera en la bandeja de entrada)
                    $obj_destino->idtipo_tramite = $idtipo_tramite;
                    $obj_destino->tipo_envio = "P"; // enviado para revisión
                    $obj_destino->save();
                }            

                // luego registramos los enviados como "COPIA"
                foreach ($depaCopia as $c => $iddepartamento) {
                    $obj_destino = new td_DestinoModel();
                    $obj_destino->iddepartamento = $iddepartamento;
                    $obj_destino->iddetalle_tramite = $iddetalle_tramite;
                    $obj_destino->estado = "B"; // tramite en borrador (no aparecera en la bandeja de entrada)
                    $obj_destino->idtipo_tramite = $idtipo_tramite;
                    $obj_destino->tipo_envio = "C"; // enviado para revisión
                    $obj_destino->save();
                }

                $retornar = collect();
                $retornar->status = "success";
                return $retornar;

            } catch (\Throwable $th) {
                Log::error("TdTramiteController => enviarDepartamentos Mensaje => ".$th->getMessage());
                $retornar = collect();
                $retornar->status = "error";
                return $retornar;
            }
        }

    // funcion para eliminar los documentos registrados en caso de error
        public function eliminarDocumentosRegistrados($listaDocEliminar, $detalleTramite, $tramite)
        {

            try {
                // eliminamos documentos en base de datos (editados y adjuntos)
                $documentosElimiar = td_DocumentoModel::where("iddetalle_tramite", $detalleTramite->iddetalle_tramite);
                $documentosElimiar->delete();

                // eliminamos documentos en servidor sftp
                foreach ($listaDocEliminar as $doc => $docDel){
                    // eliminamos el doc de texto
                    $exists = \Storage::disk('disksServidorSFTPborradores')->exists($docDel.".txt");
                    if ($exists){
                        Storage::disk("disksServidorSFTPborradores")->delete($docDel.".txt");
                    }

                    // eliminamos el doc pdf
                    $exists = \Storage::disk('disksServidorSFTPborradores')->exists($docDel.".pdf");
                    if ($exists){
                        Storage::disk("disksServidorSFTPborradores")->delete($docDel.".pdf");
                    }
                    
                }

                $detalleTramite->delete();

                if(!is_null($tramite)){
                    $tramite->delete(); 
                }     
                    
            } catch (\Throwable $th) {
                Log::error("TdTramiteController => eliminarDocumentosRegistrados => Mensaje:".$th->getMessage());
            }

        }

    // función para retorar departamento destino y verificar que se cumplan los documentos
        public function verificarDatosFLujoTramite($idnodo_flujo, $lista_idtipo_documento_creado, $lista_idtipo_documento_adjunto){
            try{

                #obtenemos el flujo que se esta registrar
                $flujo = td_FlujoModel::with('flujo_hijo','tipo_documento_flujo','departamento')
                    ->where('idflujo', $idnodo_flujo)
                    ->first();

                $listArrPara = []; #lista de id departamento para enviar como 'PARA'
                $listArrCopia = []; #lista de id departamento para enviar como 'COPIA'

                #recorremos los nodos hijos del flujo para obtener depa destino
                    foreach ($flujo->flujo_hijo as $f => $flujo_hijo){
                        if($flujo_hijo->tipo_envio == "P"){
                            
                            // veririficamos si es un flujo departamental y no esta finalizado
                            if($flujo_hijo->tipo_flujo=="D" && $flujo_hijo->estado_finalizar != 1){
                                $verificarNodo = td_FlujoModel::with('flujo_hijo')->where('idflujo', $flujo_hijo->idflujo)->first();
                                if(sizeof($verificarNodo->flujo_hijo)==0){
                                    array_push($listArrCopia, $flujo_hijo->iddepartamento);
                                    continue; // pasamos a la siguiente instancia
                                }
                            }

                            array_push($listArrPara, $flujo_hijo->iddepartamento);          
                            
                        }else if($flujo_hijo->tipo_envio == "C"){
                            array_push($listArrCopia, $flujo_hijo->iddepartamento);
                        }
                    }
                
                #verificamos que se envien todos los tipo de documento defindos en el flujo
                    $listaIdTipoDocuVerificar = []; 
                    foreach ($lista_idtipo_documento_creado as $td => $idtipo_documento) { #idtipodocumento creados
                        $idtipo_documento = decrypt($idtipo_documento);
                        $listaIdTipoDocuVerificar["$idtipo_documento"] = true;
                    }

                    foreach ($lista_idtipo_documento_adjunto as $td => $idtipo_documento) { #idtipodocumento adjunto
                        $idtipo_documento = decrypt($idtipo_documento);
                        $listaIdTipoDocuVerificar["$idtipo_documento"] = true;
                    }
                    
                    $status = true;
                    foreach ($flujo->tipo_documento_flujo as $td => $tipo_documento_flujo){
                        if(!isset($listaIdTipoDocuVerificar[$tipo_documento_flujo->idtipo_documento])){
                            $status=false; // falta un tipo de documento
                            break;
                        }
                    }

                $retornar = collect();
                $retornar->listArrPara = $listArrPara;
                $retornar->listArrCopia = $listArrCopia;
                $retornar->status = $status;
                return $retornar;

            } catch (\Throwable $th) {
                Log::error("Error en TdTramiteController => verificarDatosFLujoTramite => Mensaje => ".$th->getMessage());
                $retornar = collect();
                $retornar->status = false;
                return $retornar;
            }
        }
        
    // función para registra un trámite 
        public function store(Request $request)
        {
            try {
                
                DB::beginTransaction();
                // dd($request);

                $mensajeError = ""; // para almacenar el error en caso de que ocurra
                $listaNombreDocDel = []; // para almacenar el nombre de los documentos (solo para eliminarlos) 
                $iddepartamentoLogueado = departamentoLogueado()["iddepartamento"];

                // ------------- verificamos que se envien departamentos para ----------------------
                    if (!isset($request->input_depaEnviarPara)) { // si no se envian departamentos como para (si esta definido el flujo por defecto va a enviar)
                        $mensajeError = "Falta agregar departamentos destino";
                        goto RETORNARERROR;
                    }           

                    $departamentoLogueado = DepartamentoModel::where('iddepartamento', $iddepartamentoLogueado)->first();
                
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
                    $verificarFlujo = $this->verificarFlujoTipoTramite($request->gf_select_tipo_tramite);

                    $listArrPara = []; // lista de departamentos PARA
                    $listArrCopia = []; // lista de departamentos COPIA
                    $idflujo = null; // id del flujo al que pertenece el detalle a registrar (es necesario que sea nulo)

                    if($verificarFlujo->original['flujo']){ // tiene un flujo definido

                        $flujoRegistrar = $verificarFlujo->original['primerNodoFlujoTramite'];
                        $idflujo = $flujoRegistrar->idflujo; #para registrar en el detalle

                        #verificar que se envia idtipodocumentos creados
                            $listaIdTipoDocCreado = [];
                            if(isset($request->input_id_tipo_documento)){ $listaIdTipoDocCreado= $request->input_id_tipo_documento;}

                        #verificar que se envia idtipodocumentos adjuntos
                            $listaIdTipoDocAdjunto = [];
                            if(isset($request->input_id_tipo_documento_adjunto)){ $listaIdTipoDocAdjunto = $request->input_id_tipo_documento_adjunto;}

                            $datosFLujoTramite = $this->verificarDatosFLujoTramite($idflujo, $listaIdTipoDocCreado, $listaIdTipoDocAdjunto); #departamento destino definido en el flujo
                    
                        // obtener departamentos a enviar y verificar documentos requeridos
                        if($datosFLujoTramite->status==true){ #todo ready
                            $listArrPara = $datosFLujoTramite->listArrPara;
                            $listArrCopia = $datosFLujoTramite->listArrCopia;
                        }else{
                            $mensajeError = "Faltan documentos por agregar";
                            goto RETORNARERROR;
                        }              

                    }else{
                        $mensajeError = "Flujo de trámite no difinido";
                        goto RETORNARERROR;
                    }

                // ------------- registramos en table 'td_tramite' ------------------------  
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

                    // obtenemos la secuencia del tramite y generamos el codigo del trámite
                    $idprioridad_tramite = decrypt($request->gf_select_prioridad);
                    $prioridad_tramite = td_PrioridadTramiteModel::find($idprioridad_tramite);
                    $anio = date("Y");
                    $sec_tram = td_SecuenciasTramiteModel::where("idprioridad_tramite", $idprioridad_tramite)
                        ->where("anio",$anio) // obtenemos la secuencia del año actual
                        ->first();

                    if(is_null($sec_tram)){ // no existe (asi que lo creamos nuevo)
                        $sec_tram = new td_SecuenciasTramiteModel();
                        $sec_tram->idprioridad_tramite = $idprioridad_tramite;
                        $sec_tram->anio = $anio;
                        $sec_tram->numero = 0;
                        $sec_tram->save();
                    }

                    $sec_tram->numero = $sec_tram->numero+1;
                    $sec_tram->save();
                    // generamos el codigo del tramite
                    $codTramite = 'GADMC-'.$anio.'-'.$sec_tram->numero.'-'.$prioridad_tramite->codigo;

                    // determinamos la procedencia del trámite
                    $procedencia = "INT";
                    if(isset($request->gf_select_procedencia)){
                        $procedencia = decrypt($request->gf_select_procedencia);
                    }

                    $tramite = new td_TramiteModel();
                    $tramite->codTramite = $codTramite; // no se para que es
                    $tramite->asunto = strtoupper($request->gt_asunto);
                    $tramite->observacion = strtoupper($request->gt_observaciones);
                    $tramite->idprioridad_tramite = $idprioridad_tramite;
                    $tramite->fechaCreacion = date("Y-m-d H:i:s");
                    $tramite->procedencia = $procedencia;
                    $tramite->estadoTramite = "P";
                    $tramite->finalizado = 0;
                    $tramite->idtipo_tramite = decrypt($request->gf_select_tipo_tramite);
                    $tramite->iddepartamento_genera = departamentoLogueado()["iddepartamento"];
                    $tramite->idus001_genera = auth()->user()->idus001;
                    $tramite->save();            

                // ------------- registramos en table 'td_detalle_tramite' ----------------

                    $detalleTramite = new td_DetalleTramiteModel();
                    $detalleTramite->fecha = date("Y-m-d H:i:s");
                    $detalleTramite->asunto =  strtoupper($request->gt_asunto);
                    $detalleTramite->observacion = strtoupper($request->gt_observaciones);
                    $detalleTramite->estado = "B";
                    $detalleTramite->aprobado = 0;
                    $detalleTramite->idtramite = $tramite->idtramite;
                    $detalleTramite->iddepartamento_origen = $iddepartamentoLogueado;
                    $detalleTramite->idus001Envia = auth()->user()->idus001;
                    $detalleTramite->nivelAtencion=1;
                    $detalleTramite->idflujo = $idflujo;
                    $detalleTramite->iddetalle_tramite_padre = null;
                    $detalleTramite->iddestino_atendido = null;
                    $detalleTramite->save();

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

                        $crearDocumento = $this->crearDocumentoEditado(
                            $request->input_contenido_documento, 
                            $detalleTramite->iddetalle_tramite, 
                            $request->input_descripcion_documento, 
                            $listaTipoDocumento,
                            $request->gt_asunto,
                            $listArrPara,
                            $listArrCopia,
                            null,
                            $listaAnexos
                        );

                        array_merge($listaNombreDocDel, $crearDocumento->listaNombreDoc); // almacenamos el nombre de los documentos creados

                        if($crearDocumento->status=="error"){ // si ocurre un error al crear los documentos
                            // eliminamos los registros anteriores

                            $detalleTramite->delete();
                            $tramite->delete();

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

                        $adjuntarDocumentos = $this->adjuntarDocumentos(
                            $request->file_documento_adjunto,
                            $listaTipoDocumentoAdj,
                            $request->input_codigo_documento_adjunto,
                            $request->input_descripcion_documento_adjunto,
                            $detalleTramite->iddetalle_tramite
                        ); 

                        array_merge($listaNombreDocDel, $adjuntarDocumentos->listaNombreDoc); // almacenamos los nombre de los documentos adjuntos

                        if($adjuntarDocumentos->status=="error"){
                            // eliminamos los documentos generados
                            $this->eliminarDocumentosRegistrados($listaNombreDocDel, $detalleTramite, $tramite);

                            $mensajeError = "No se pudo registrar los documentos adjuntos.";
                            goto RETORNARERROR;
                        }
                    
                    }       
                    
                // ------------- registrar envio de tramite a departamento --------------------------
                
                    $envioDepartamento = $this->enviarDepartamentos(
                        $listArrPara, 
                        $listArrCopia, 
                        $detalleTramite->iddetalle_tramite,
                        decrypt($request->gf_select_tipo_tramite)
                    );

                    if($envioDepartamento->status == "error"){
                        // eliminamos los departamentos destino se si se pudieron registrar
                        $destino_eliminar = td_DestinoModel::where("iddetalle_tramite", $detalleTramite->iddetalle_tramite);
                        $destino_eliminar->delete();

                        // eliminamos los documentos generados
                        $this->eliminarDocumentosRegistrados($listaNombreDocDel, $detalleTramite, $tramite);

                        $mensajeError = "No se pudo registrar los departamentos destino.";
                        goto RETORNARERROR;
                    }
                
                // ------------- si se realiza con exito --------------------------------------------
                    
                    DB::commit();
                    return response()->json([
                        "error" => false,
                        "resultado" => [
                            "status" => "success",
                            "mensaje" => "Trámite registrdo con éxito",
                            "codTramite" => $codTramite,
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
                Log::error("TdTramiteController => store | Mensaje => ".$th->getMessage());
                
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

    // FUNCIÓN PARA MOSTRAR EL DETALLE DEL TRÁMITE
        public function detalleTramite($iddetalle_tramite)
        {

            try{

                $iddetalle_tramite = decrypt($iddetalle_tramite);
                
                //buscamos el detalle tramite actual
                $detalle_tramite = td_DetalleTramiteModel::with('tramite', 'destino', 'departamento_origen','documento')
                    ->where('iddetalle_tramite', $iddetalle_tramite)
                    ->first();

                $idtramite = $detalle_tramite->tramite->idtramite;

                // obtenemos todos los detalles del trámites incluidos el de los trámites asociados
                $listaDetallesTramite = $this->getAllDetalleTramiteAsociados(encrypt($iddetalle_tramite));
                $listaDetallesTramite = $listaDetallesTramite->original["listaDetalles"];    

                $tramite = td_TramiteModel::with('detalle_tramite', 'departamento_genera', 'tipo_tramite', 'gestion_archivo')
                    ->where('idtramite', $idtramite)
                    ->first();
        
                return response()->json([
                    "error"=>false,
                    "tramite" => $tramite,
                    "detalle_tramite" => $detalle_tramite,
                    "listaDetallesTramite" => $listaDetallesTramite
                ]);

            }catch(\Throwable $th){
                Log::error("TdTramiteController => detalleTramite Mensaje => ".$th->getMessage());
                return response()->json([
                    "error"=>true,
                    "mensaje" => "No se pudo obtener el detalle del trámite"
                ]);
            }

        }

        // funciones para obtener el historial de asociaciones de tramites

            //esta funcion retorna un listado de detalles tramite asociados al enviado por parametro
            public function getAllDetalleTramiteAsociados($iddetalle_tramite){
                try{

                    $iddetalle_tramite = decrypt($iddetalle_tramite);
                    $iddetalle_tramite_primero = $this->buscarAsociadoPadre($iddetalle_tramite);
                    $listaDetalles = $this->obtenerListaDetalles($iddetalle_tramite_primero, []);

                    return response()->json([
                        "error" => false,
                        "listaDetalles" => $listaDetalles
                    ]); 

                }catch(\Throwable $th){
                    Log::error("TdTramiteController => getAllDetalleTramiteAsociados => Mensaje => ".$th->getMessage());
                    return response()->json([
                        "error" => true,
                        "listaDetalles" => []
                    ]); 
                }
            }

            // esta funcion tiene como objetivo solo buscar el primer detalle asociado
            public function buscarAsociadoPadre($iddetalle_tramite){
                $detalle_tramite = td_DetalleTramiteModel::with('tramite')->where('iddetalle_tramite', $iddetalle_tramite)->first();
                if(!is_null($detalle_tramite->iddetalle_tramite_padre)){
                    return $this->buscarAsociadoPadre($detalle_tramite->iddetalle_tramite_padre);
                }else{
                    return $iddetalle_tramite;
                }
            }

            // esta funcion busca a toda su descendencia de asociados de un detalle
            public function obtenerListaDetalles($detalle_tramite, $listaDetalles){

                if(is_numeric($detalle_tramite)){ // solo se ejecuta en la primer iteración
                    $detalle_tramite = td_DetalleTramiteModel::with(['tramite', 'destino', 'departamento_origen', 'documento'=>function($query_documento){
                        $query_documento->orderBy("tipo_creacion", "DESC");
                    }])
                    ->where('iddetalle_tramite', $detalle_tramite)
                    ->first();
                }
                
                array_push($listaDetalles, $detalle_tramite);

                //buscamos a los hijos de detalle tramite en instancia
                $listaHijos = td_DetalleTramiteModel::with(['tramite', 'destino', 'departamento_origen', 'documento'=>function($query_documento){
                        $query_documento->orderBy("tipo_creacion", "DESC");
                    }])
                    ->where('iddetalle_tramite_padre', $detalle_tramite->iddetalle_tramite)
                    ->orderBy('nivelAtencion','ASC')
                    ->get();

                foreach($listaHijos as $dh => $detalle_hijo){
                    $listaDetalles = $this->obtenerListaDetalles($detalle_hijo, $listaDetalles);
                }

                return $listaDetalles;
                
            }

    // FUNCIÓN PARA DESCARGAR TODOS LOS DOCUMENTOS DE UN TRÁMITE
        public function descargarDocumentos($idtramite){
            try {
                //obtenemos toda la información del trámite
                $idtramite = decrypt($idtramite);
                $tramite = td_TramiteModel::with('detalle_tramite', 'departamento_genera')
                    ->where('idtramite', $idtramite)
                    ->first();
                    
                if(is_null($tramite)){
                    return view("error");
                }
                    
                
                $listaDocumentos = [];
                foreach ($tramite->detalle_tramite as $dt => $detalle_tramite) {
                    foreach ($detalle_tramite->documento as $d => $documento) {
                        $exists = \Storage::disk('disksServidorSFTPborradores')->exists($documento->rutaDocumento.".".$documento->extension);
                        if($exists){ // el documento si existe
                            $nombreDocumento = $documento->rutaDocumento.".".$documento->extension;
                            $getDocumento = \Storage::disk('disksServidorSFTPborradores')->get($nombreDocumento); // bajamos el documento del servidor sftp     
                            \Storage::disk('DocumentosTramite')->put($nombreDocumento,$getDocumento); // guargamos el documentos de forma temporal
                            array_push($listaDocumentos, $nombreDocumento); // registramos el nombre del documento
                        }
                    }
                }      


                $nombreZip = "DOCUMENTOS-".$tramite->codTramite;

                $zip = new ZipArchive(); // Creamos un instancia de la clase ZipArchive        
                $zip->open("$nombreZip.zip",ZipArchive::CREATE); // Creamos y abrimos un archivo zip temporal         
                //Añadimos los archivo dentro del directorio que hemos creado 
                foreach ($listaDocumentos as $ld => $nombreDocumento){
                    $zip->addFile("DocumentosTramite/".$nombreDocumento);
                }
                $zip->close(); // Una vez añadido los archivos deseados cerramos el zip.

                // Creamos las cabezeras que forzaran la descarga del archivo como archivo zip.
                header("Content-type: application/octet-stream");
                header("Content-disposition: attachment; filename=$nombreZip.zip");
                
                readfile("$nombreZip.zip");// leemos el archivo creado

                // Por último eliminamos el archivo temporal creado
                unlink("$nombreZip.zip"); //Destruye el archivo zip temporal
                \Storage::disk('DocumentosTramite')->delete($listaDocumentos); // Destruye los documentos temporales
                    
            } catch (\Throwable $th) {
                Log::error("TdTramiteController => descargarDocumentos => Mensaje => ".$th->getMessage());
                return view("error");
            }
        }

    // FUNCIÓN PARA BUSCAR UN TRÁMITE
        public function buscarTramite(){
            return view("tramitesDepartamentales.detalleTramite.buscar.buscarTramite");
        }

        public function buscarTramiteFiltrar($busqueda){

            try {

                $iddepartamentoLogueado = departamentoLogueado()["iddepartamento"];
                // --------- comprobar caracteres especiales ------------------
                    $dataValidar = [
                        "busqueda" => $busqueda
                    ];
                    $reglatexto = 'required|string|regex:/^[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ\s+\-.]+$/';
                    $validator = Validator::make($dataValidar, [
                        "busqueda" => $reglatexto
                    ]);
            
                    if ($validator->fails()) {
                        $mensajeError = "No se permite ingresar caracteres especiales";
                        goto RETORNARERROR;
                    }

                // --------- realizamos la busqueda ---------------------------

                    $listaDetallesTramite = td_DetalleTramiteModel::with('tramite','destino', 'departamento_origen', 'detalle_tramite_padre')
                        ->where('iddepartamento_origen', $iddepartamentoLogueado)
                        ->where('estado', '<>', "F")
                        ->where(function($query_detTram) use ($busqueda){
                            $query_detTram->where('asunto', 'like', "%$busqueda%") #buscamos por asunto
                                ->orWhere('observacion', 'like', "%$busqueda%") #buscamos por observación
                                ->orWhereHas('destino', function($query_destino) use ($busqueda){
                                    $query_destino->whereHas('departamento', function($query_depa) use ($busqueda){ 
                                        $query_depa->where('nombre', 'like', "%$busqueda%")->orWhere('abreviacion', 'like', "%$busqueda%");
                                    });
                                });
                        })
                        ->get();

                // --------- final del todo -----------------------------------

                return response()->json([
                    "error" => false,
                    "resultado" => [
                        "status" => "success",
                        "listaDetallesTramite" => $listaDetallesTramite
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

            } catch (\Throwable $th) {
                Log::error("TdTramiteController => buscarTramiteFiltrar => Mensaje => ".$th->getMessage());
                return response()->json([
                    "error" => true,
                    "resultado" => [
                        "status" => "error",
                        "mensaje" => "No se pudo filtrar el trámite"
                    ]
                ]); 
            }



        }

    //FUNCIÓN PARA IMPRIMIR EL COMPROBANTE DE UN TRÁMITE
        public function comprobanteTramite($idtramite_encrypt){
            try {

                $idtramite = decrypt($idtramite_encrypt);
                
                $tramite = td_TramiteModel::where('idtramite', $idtramite)->first();

                $listaDetallesTramite = td_DetalleTramiteModel::with('tramite', 'detalle_tramite_padre', 'departamento_origen', 'destino','documento')
                    ->where('idtramite', $idtramite)
                    ->orderBy('nivelAtencion', 'ASC')
                    ->get();

                // dd($listaDetallesTramite);

                //OBTENERMOS EL USUARIO QUE GENERA
                if(auth()->guest()){ $usuario = "No identificado"; }
                else{ $usuario = auth()->user()->name; }
                    
                
                //CONVERTIR LA FECHA EN LETRAS
                setlocale(LC_ALL,"es_ES@euro","es_ES","esp"); //IDIOMA ESPAÑOL
                $fecha= date('Y-m-j');
                $hora = date('H:i:s');
                $fecha = strftime("%d de %B de %Y", strtotime($fecha));

                //CREACION DEL PDF PASANDO LA DATA A LA VISTA
                $pdf = PDF::loadView('tramitesDepartamentales.detalleTramite.buscar.comprobanteTramite',['tramite'=>$tramite, 'listaDetallesTramite'=>$listaDetallesTramite, 'fecha'=>$fecha, 'hora'=>$hora, 'usuario'=>$usuario]);
                $pdf->setPaper("A4", "portrait");
                return $pdf->stream();                    

            }catch (\Throwable $th) {
                Log::error("TdTramiteController => comprobanteTramite => Mensaje => ". $th->getMessage());
                return view("error");
            }
        }


}
