<?php

namespace App\Http\Controllers;

use App\td_PrioridadTramiteModel as PrioridadTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 

class TdPrioridadTramiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $listaprioridadtramite= PrioridadTramite::all();
        //dd($listaprioridadtramite);
        //return view('tramitesDepartamentales.gestionTipoDocumento.gestionTipoDocumento',['listatipodocumento' => $listatipodocumento]);
        return view('tramitesDepartamentales.gestionPrioridadTramite.gestionPrioridadTramite',['listaprioridadtramite' => $listaprioridadtramite]);
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
            
             $validator = Validator::make($request->all(), [
            
            'descripcion' => 'required',
            'codigo' => 'required'
            
            ]);

            if ($validator->fails())
            {
            // return back()->with(['mensajePInfoSolicitud'=>'No se pudo realizar el registro, complete todos los datos del formulario','estadoP'=>'danger']);
                return back()->with([
                'mensajeInfo'=>"No se pudo realizar el registro, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }


            $PrioridadTramite= new PrioridadTramite();
            $PrioridadTramite->descripcion=$request->descripcion;
            $PrioridadTramite->codigo=$request->codigo;

            $buscarPrioridad = PrioridadTramite::where('descripcion', $PrioridadTramite->descripcion)
                                                ->where('codigo',  $PrioridadTramite->codigo)
                                                ->first();
        if(!is_null($buscarPrioridad)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar informaci??n repetida",
                'mensajeColor'=>"info"
            ]);
        }
            
            $PrioridadTramite->save();
            return back()->with([
                'mensajeInfo'=>"Tipo de prioridad tr??mite guardada con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se puedo guardar el tipo de prioridad de tr??mite",
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
        $prioridadtramite = PrioridadTramite::find(decrypt($id));
        return response()->json($prioridadtramite);


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
             $validator = Validator::make($request->all(), [
            
            'descripcion' => 'required',
            'codigo' => 'required'
            
            ]);

            if ($validator->fails())
            {
            // return back()->with(['mensajePInfoSolicitud'=>'No se pudo realizar el registro, complete todos los datos del formulario','estadoP'=>'danger']);
                return back()->with([
                'mensajeInfo'=>"No se pudo realizar la actualizaci??n, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }
            

            $prioridadtramite = PrioridadTramite::find(decrypt($id));
            $prioridadtramite->descripcion=$request->descripcion;
            $prioridadtramite->codigo=$request->codigo;

            $buscarPrioridad = PrioridadTramite::where('descripcion', $prioridadtramite->descripcion)
                                                ->where('codigo',  $prioridadtramite->codigo)
                                                ->first();
            if(!is_null($buscarPrioridad)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar informaci??n repetida",
                'mensajeColor'=>"info"
            ]);
            }
            

            if($prioridadtramite->save())
            {
            return back()->with([
                'mensajeInfo'=>"Prioridad tr??mite actualizada con exito",
                'mensajeColor'=>"success"
            ]);
            }
            else{
                return back()->with([
                'mensajeInfo'=>"No se pudo actualiza la informaci??n",
                'mensajeColor'=>"danger"
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
  
            $prioridadtramite = PrioridadTramite::find(decrypt($id));
            //dd($prioridadtramite);
            if($prioridadtramite->delete())
            {
            return back()->with([
                'mensajeInfo'=>"La informaci??n fue eliminada con ??xito",
                'mensajeColor'=>"success"
            ]);
            }
            else{
                 return back()->with([
                'mensajeInfo'=>"No se pudo eliminar la informaci??n",
                'mensajeColor'=>"danger"
            ]);
            }
        
    }
    
}
