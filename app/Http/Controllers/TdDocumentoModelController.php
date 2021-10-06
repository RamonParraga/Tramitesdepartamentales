<?php

namespace App\Http\Controllers;

use App\td_DocumentoModel;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Storage;
use App\td_FormatoModel;

use App\DepartamentoModel;
use App\td_TipoDocumentoModel;
use App\td_EstructuraDocumentoModel;
use Log;

class TdDocumentoModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // cread un 
    }


    // public function  crearDocumentoTemporal3(){
        
    //     $html = view('TramitesDepartamentales.formatoDocumento.cuerpoDocumento')->render();
    //     $mpdf = new \Mpdf\Mpdf();
    //     $mpdf->WriteHTML($html);
    //     $mpdf->Output();
        
    // }

    //guarda un documento temporal (recive el html generado por el editor de texto)
    // public function crearDocumentoTemporal(Request $request){

    //     // creamos el codigo html del documento que se va a generar
    //     $html=getEstructuraDocumento("$request->contenidoDocumento");

    //     // LIBRERIA PARA CREAR EL DOCUMENTO EL PDF ======================
    //         $pdf = new PDF();
    //         $pdf = \App::make('dompdf.wrapper');
    //         $pdf->loadHTML($html);
    //         $pdf->setPaper("A4", "portrait");
    //         $documentoListo = $pdf->stream();
    //     // ===============================================================

    //     // GUARDAMOS EL DOCUMENTO CREADO EN EL DISCO TEMPORAL ===========
    //         $nombreDocTemporal = "DOC-TEMP".(auth()->user()->idus001).'-'.date('Ymd').'-'.time(); // creamos un nombre temporal
    //         Storage::disk('disksDocLocalTemporal')->put(str_replace("", "","$nombreDocTemporal.pdf"), $documentoListo); // creamos el documento pdf
    //         Storage::disk('disksDocLocalTemporal')->put(str_replace("", "","$nombreDocTemporal.txt"), $request->contenidoDocumento); // creamos el documento txt para editar dicho documento
    //         return response()->json([
    //             'error' => false,
    //             'codigo' => 200,
    //             'nombreDocTemporal'=>$nombreDocTemporal
    //         ]); // retornamos el nombre en disco del documento creado
    //     // =====================================================================

    // }

    // public function removerDocumentoTemporal(Request $request){

    //     try {

    //         foreach ($request->listanombresDocTemporal as $key => $nombreDocTemporal) {
    //             Storage::disk('disksDocLocalTemporal')->delete("$nombreDocTemporal.pdf");
    //             Storage::disk('disksDocLocalTemporal')->delete("$nombreDocTemporal.txt");
    //         }

    //         if(sizeof($request->listanombresDocTemporal)==1){

    //             $tipo_documento = td_TipoDocumentoModel::find(decrypt($request->idtipo_documento));
    //             $tipo_documento->idtipo_documento_encrypt = $request->idtipo_documento;
                
    //             return response()->json([
    //                 'error' => true,
    //                 'codigo' => 200,
    //                 'tipo_documento' => $tipo_documento
    //             ]);

    //         }else{
    //             return response()->json([
    //                 'error' => true,
    //                 'codigo' => 200
    //             ]);
    //         }

    //     } catch (\Throwable $th) {
    //         Log::error($th->getMessage());
    //         return response()->json([
    //             'error' => true,
    //             'codigo' => 500,
    //             'message' => $th->getMessage()
    //         ]); 
    //     }

    // }



    /**
     * Display the specified resource.
     *
     * @param  \App\td_DocumentoModel  $td_DocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function show(td_DocumentoModel $td_DocumentoModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\td_DocumentoModel  $td_DocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function edit(td_DocumentoModel $td_DocumentoModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\td_DocumentoModel  $td_DocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, td_DocumentoModel $td_DocumentoModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\td_DocumentoModel  $td_DocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(td_DocumentoModel $td_DocumentoModel)
    {
        //
    }
}
