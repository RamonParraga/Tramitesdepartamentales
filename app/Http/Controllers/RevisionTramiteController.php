<?php

namespace App\Http\Controllers;
use App\td_DetalleTramiteModel;
use App\td_DocumentoModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\td_us001_tipofpModel;
use App\BSrE_PDF_Signer_Cli;
use App\ParametrosGeneralesModel;
use DB;
use Log;

use Illuminate\Http\Request;

class RevisionTramiteController extends Controller
{

    //funcion para verificar si el el jefe del departamento
    public function verificarJefeDepartamento($iddepartamento){

        //primero si no esta logueado
        if(Auth::guest()){ return false; }

        if($iddepartamento==0){ return false; }
        $idus001 = auth()->user()->idus001; // id del usuario logueado

        // buscamos el jefe de ese departamento
        $jefeDepLogueado = td_us001_tipofpModel::with('us001', 'departamento') // obtenemos el jefe de ese departamento
            ->where('iddepartamento',$iddepartamento)
            ->where('jefe_departamento','1')
            ->first();

        if(is_null($jefeDepLogueado)){ return false;}
        if($jefeDepLogueado->us001->idus001!=$idus001){return false;}

        //todo esta bien (el usuario es el jefe del departamento)
        return true;

    }


    // funcion para verificar si estan firmados los documentos
    public function verificarDocumentoFirmado($iddetalle_tramite){

        try {


            $mensajeError = "";

            $iddetalle_tramite = decrypt($iddetalle_tramite);
            $detalleTramite = td_DetalleTramiteModel::where('iddetalle_tramite', $iddetalle_tramite)->first();
            $documento = td_DocumentoModel::where('iddetalle_tramite', $iddetalle_tramite)
                ->where("tipo_creacion","E") // documentos creados por el editor de texto
                ->where("firmado",0) // documentos que no esten firmados
                ->first();
            
            if(is_null($detalleTramite)){
                $mensajeError = "Detalle del trámite no encontrados"; goto RETORNARERROR;
            }

            //verificamos que sea el jefe del departamento
            if($this->verificarJefeDepartamento($detalleTramite->iddepartamento_origen)==false){ // solo puede firmar el jefe del departamento que se envia el trámite
                $mensajeError = "Usted no es el jefe del departamento"; goto RETORNARERROR;
            }


            if(is_null($documento)){ // todo esta firmado
                return response()->json([
                    "error" => false,
                    "firma" => "listo"
                ]);
            }else{
                return response()->json([
                    "error" => false,
                    "firma" => "pendiente"
                ]);
            }
            
            RETORNARERROR:
                return response()->json([
                    "error" => true,
                    "mensaje" => $mensajeError,
                    "status" => "error"
                ]);

        }catch (\Throwable $th){
            Log::error("RevisionTramite => verificarDocumentoFirmado => Mensaje:".$th->getMessage());
            return response()->json([
                "error" => true,
                "mensaje" => "No se pudo verificar las firmas del trámite",
                "status" => "error"
            ]);
        }


    }


    // funcio para subir un documento firmado
    public function subirDocumentoFirmado(Request $request, $iddetalle_tramite_encrypt){

        try {
            
            $iddetalle_tramite = decrypt($iddetalle_tramite_encrypt);
            $documento = td_DocumentoModel::where('iddetalle_tramite', $iddetalle_tramite)->where('tipo_creacion','E')->first();

            if(is_null($documento)){ // error al encontrar el documento
                goto RETORNARERROR;
            }else{
                Storage::disk('disksServidorSFTPborradores')->put($documento->rutaDocumento.".pdf", \File::get($request->input_subirDocumento)); // guardamos el documentos
                $exists = \Storage::disk('disksServidorSFTPborradores')->exists($documento->rutaDocumento.".pdf");
                if($exists){

                    $documento->firmado=1; // decimos que el documento ya esta firmado
                    $documento->save();
                    
                    return response()->json([
                        "error" => false,
                        "resultado" =>[
                            "mensaje" => "El documento fué subido con exito",
                            "status" => "success"
                        ]
                    ]);
                }
            }

            RETORNARERROR:
                return response()->json([
                    "error" => true,
                    "resultado" =>[
                        "mensaje" => "No se pudo subir el documento. No se pudo obtrener la información del trámite",
                        "status" => "error"
                    ]
                ]);            
        }catch (\Throwable $th){
            Log::error("RevisionTramiteController => subirDocumentoFirmado => Mensaje:".$th->getMessage());
            return response()->json([
                "error" => true,
                "resultado" =>[
                    "mensaje" => "No se pudo subir el documento",
                    "status" => "error"
                ]
            ]);   
        }
        
    }


    // funcion para aprobar un detalle de tramite (se envia a los departamento destino)
    public function aprobarDetalleTramite($iddetalle_tramite_encrypt){

        try{

            $iddetalle_tramite = decrypt($iddetalle_tramite_encrypt);
            $detalleTramite = td_DetalleTramiteModel::where('iddetalle_tramite', $iddetalle_tramite)->first();

            if(is_null($detalleTramite)){ // error al encontrar el documento
                goto RETORNARERROR;
            }else{

                if($this->verificarJefeDepartamento($detalleTramite->iddepartamento_origen)==false){ // solo puede firmar el jefe del departamento que se envia el trámite
                    return view('error'); // no esta logueado o no es jefe del departamento
                }

                $detalleTramite->aprobado="1";
                $detalleTramite->fechaApr=date("Y-m-d H:i");
                $detalleTramite->save();
                // actualizamos el estado de los destino
                    DB::table('td_destino')
                    ->where('iddetalle_tramite', $iddetalle_tramite)
                    ->update(['estado' => 'P']);

                return response()->json([
                    "error" => false,
                    "resultado" =>[
                        "mensaje" => "Trámite aprobado con exito",
                        "status" => "success"
                    ]
                ]);
            }

            RETORNARERROR:
                return response()->json([
                    "error" => true,
                    "resultado" =>[
                        "mensaje" => "No se pudo aprobar el trámite",
                        "status" => "error"
                    ]
                ]);

        }catch(\Throwable $th){
            Log::error("RevisionTramiteController => aprobarDetalleTramote => Mensaje".$th->getMessage());
            return response()->json([
                "error" => true,
                "resultado" =>[
                    "mensaje" => "No se pudo aprobar el trámite",
                    "status" => "error"
                ]
            ]);
        }


    }


    // funcion para devolver el tramite a la secretaria para que lo corrija
    public function enviaraRevisionDetalleTramite(Request $request, $iddetalle_tramite_encrypt){
        try{

            $iddetalle_tramite = decrypt($iddetalle_tramite_encrypt);
            $detalleTramite = td_DetalleTramiteModel::where('iddetalle_tramite', $iddetalle_tramite)->first();

            if(is_null($detalleTramite)){ // error al encontrar el documento
                goto RETORNARERROR;
            }else{
                $detalleTramite->aprobado = 0;
                $detalleTramite->estado = "R";
                $detalleTramite->detRevision = strtoupper($request->textarea_detalle_revision); 
                $detalleTramite->save();
                return response()->json([
                    "error" => false,
                    "resultado" =>[
                        "mensaje" => "Tramite enviado para ser revisado",
                        "status" => "success"
                    ]
                ]);
            }

            RETORNARERROR:
            return response()->json([
                "error" => true,
                "resultado" =>[
                    "mensaje" => "No se pudo registrar la revisión del trámite",
                    "status" => "error"
                ]
            ]);    

        }catch(\Throwable $th){

            Log::error("RevisionTramiteController => enviaraRevisionDetalleTramite => Mensaje:".$th->getMessage());
            return response()->json([
                "error" => true,
                "resultado" =>[
                    "mensaje" => "No se pudo registrar la revisión del trámite",
                    "status" => "error"
                ]
            ]);

        }

    }

}



