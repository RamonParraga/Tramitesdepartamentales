<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use App\td_FormatoModel; 
use File;
use PDF;
use Log;
use Illuminate\Http\Request;

class TdFormatoModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $formatoDocumento= td_FormatoModel::first();
        return view ('tramitesDepartamentales.gestionFormato.gestionFormato', ['formatoDocumento' => $formatoDocumento]); 
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

        try{    

            // buscamos el primer dato en la base de datos
            $formato = td_FormatoModel::first();
            if(is_null($formato)){
                // si no hay se crea un objeto nuevo
                $formato= new td_FormatoModel();
            }else{
                // si previamente hay un registro se elimina las imagenes viejas                
                if(sizeof($request->files)>=2){ // solo si esque se envian imagenes
                    $del_cabecera = str_replace("/tdFormato/", "", $formato->cabecera);
                    $del_pie = str_replace("/tdFormato/", "", $formato->pie);
                    Storage::disk('tdFormato')->delete($del_cabecera);
                    Storage::disk('tdFormato')->delete($del_pie);
                }
            }

            // si no se envian imagenes solo actualizamos el pdf
            if(sizeof($request->files)<2){
                goto GUARDAR_FORMATO; // saltamos el guardado de las imagenes header y footer
            }

            $fileCabecera = $request->file('cabecerapagina');
            $filePie = $request->file('piepagina');

            $nombreCabecera = time()."_cabecera_".$fileCabecera->getClientOriginalName();
            $nombrePie = time()."_pie_".$filePie->getClientOriginalName();
            
            Storage::disk('tdFormato')->put($nombreCabecera, File::get($fileCabecera));
            Storage::disk('tdFormato')->put($nombrePie, File::get($filePie));


            $formato->cabecera="/tdFormato/".$nombreCabecera;
            $formato->pie="/tdFormato/".$nombrePie;

            GUARDAR_FORMATO:
            
            $formato->page_margin_top = $request->p_margin_top;
            $formato->page_margin_right = $request->p_margin_right;
            $formato->page_margin_bottom = $request->p_margin_bottom;
            $formato->page_margin_left = $request->p_margin_left;

            $formato->header_top = $request->header_top;
            $formato->header_height = $request->header_height;

            $formato->footer_bottom = $request->footer_bottom;
            $formato->footer_height = $request->footer_height;

            $formato->main_left = $request->main_left;
            $formato->main_right = $request->main_right;

            $formato->save();
            // creamos la vista previa del documento con las nuevas imagenes
            
            $this->crearVistaPreviaFormato();

            return back()->with([
                'mensajeInfo'=>"Formato actualizado con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            Log::error('Error al guardar formato. Error: '.$th->getMessage());
            return back()->with([
                'mensajeInfo'=>"Error al guardar el Formato",
                'mensajeColor'=>"error"
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\td_FormatoModel  $td_FormatoModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\td_FormatoModel  $td_FormatoModel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\td_FormatoModel  $td_FormatoModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\td_FormatoModel  $td_FormatoModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function crearVistaPreviaFormato()
    {
        // creamos el codigo html del documento que se va a generar
        $contenido ="
            <h1 style='font-family:Helvetica,Futura,Arial,Verdana,sans-serif;'>
                <center style='margin: 100px 0 600px 0;'>AQUÍ EL CONTENIDO DEL DOCUMENTO</center>
                <style type='text/css'>
                    body{
                        background-color:#ffa0a0;
                        margin-color:red;
                    }
                    main{
                        background-color:#fff;
                    }
                    img{
                        border:solid 2px balck;
                    }
                </style>
            </h1>";

        // CREAR EL DOCUMENTO EL PDF ======================
            $borrador = false; // no agregar marca de agua
            $pdf = getEstructuraDocumento($contenido, $borrador);
            $documentoListo = $pdf->stream();
        // ===============================================================

        // GUARDAMOS EL DOCUMENTO CREADO EN EL DISCO TEMPORAL ===========
            $nombreDocTemporal = "visualizarFormatoDocumento.pdf"; // creamos un nombre temporal
            Storage::disk('tdFormato')->put(str_replace("", "","$nombreDocTemporal"), $documentoListo); // creamos el documento pdf           
            return 0;
        // =====================================================================
    }
}
