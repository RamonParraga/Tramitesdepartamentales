<?php

namespace App\Http\Controllers;

use App\td_TipoDocumentoModel as TipoDocumento;
use Illuminate\Http\Request;

class TdTipoDocumentoModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $listatipodocumento= TipoDocumento::all()->where('estado', 1);
        //dd($listatipodocumento);
        return view('tramitesDepartamentales.gestionTipoDocumento.gestionTipoDocumento',['listatipodocumento' => $listatipodocumento]);
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
        //
        try{    
            $TipoDocumento= new TipoDocumento();
            $TipoDocumento->descripcion=$request->descripcion;
            $TipoDocumento->abreviacion=$request->abreviacion;
            $TipoDocumento->estructura=$request->estructura;
            $TipoDocumento->secuencia=$request->secuencia; 
            $TipoDocumento->prioridad=$request->prioridad; 
            $TipoDocumento->save();
            return back()->with([
                'mensajeInfo'=>"Tipo de documento guardodo con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se puede guardar el tipo de documento",
                'mensajeColor'=>"error"
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function show(td_TipoDocumentoModel $td_TipoDocumentoModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $tipodocumento = TipoDocumento::find(decrypt($id));
        return response()->json($tipodocumento);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, $id)
    {
        //
        
        try{
            $tipodocumento = TipoDocumento::find(decrypt($id));
            $tipodocumento->descripcion=$request->descripcion;
            $tipodocumento->abreviacion=$request->abreviacion;
            $tipodocumento->estructura=$request->estructura;
            $tipodocumento->secuencia=$request->secuencia;
            $tipodocumento->prioridad=$request->prioridad; 
            $tipodocumento->update();
            return back()->with([
                'mensajeInfo'=>"Tipo de documento editado con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se puedo editar el tipo de documento",
                'mensajeColor'=>"error"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       try{
            $tipodocumento = TipoDocumento::find(decrypt($id));
            $tipodocumento->estado=0;
            $tipodocumento->update();
            return back()->with([
                'mensajeInfo'=>"Tipo de documento eliminado con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se pudo eliminar el tipo de documento",
                'mensajeColor'=>"error"
            ]);
        }
    }
}
