<?php

namespace App\Http\Controllers;

use App\td_SecuenciasTramiteModel;
use App\td_PrioridadTramiteModel;
use Illuminate\Http\Request;

class TdSecuenciasTramiteModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listasecuenciatramite = td_SecuenciasTramiteModel::with('td_prioridad')
            ->orderBy("anio","DESC")
            ->get();
        $listaPrioridad = td_PrioridadTramiteModel::all();
        
        return view('tramitesDepartamentales.gestionSecuenciasTramite.gestionSecuenciasTramite', [
            'listasecuenciatramite' => $listasecuenciatramite,
            'listaPrioridad' => $listaPrioridad
        ]);
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

            // verificamos que no se repita 
            $Secuenciatramite= td_SecuenciasTramiteModel::where('anio',$request->anio)
                ->where('idprioridad_tramite',$request->prioridad)
                ->first();
            if(!is_null($Secuenciatramite)){
                return back()->with([
                    'mensajeInfo'=>"Registro ya existente",
                    'mensajeColor'=>"default"
                ]);
            }

            $Secuenciatramite= new td_SecuenciasTramiteModel();
            $Secuenciatramite->anio=$request->anio;
            $Secuenciatramite->numero=$request->numero;
            $Secuenciatramite->idprioridad_tramite=$request->prioridad;
            //dd($request->prioridad);
            if (is_null($Secuenciatramite->anio) or is_null($Secuenciatramite->numero)){
                return back()->with([
                    'mensajeInfo'=>"No se puede guardar el registro, porfavor llene todos los campos",
                    'mensajeColor'=>"error"
                ]);
            }

            $Secuenciatramite->save();
            return back()->with([
                'mensajeInfo'=>"Registro guardado Exitosamente.!",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se pudo guardar el registro",
                'mensajeColor'=>"error"
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\td_SecuenciatramiteModel  $td_SecuenciatramiteModel
     * @return \Illuminate\Http\Response
     */
    public function show(td_SecuenciatramiteModel $td_SecuenciatramiteModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\td_SecuenciatramiteModel  $td_SecuenciatramiteModel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Secuenciatramite = td_SecuenciasTramiteModel::find(decrypt($id));
        return response()->json($Secuenciatramite);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\td_SecuenciatramiteModel  $td_SecuenciatramiteModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try{
            $Secuenciatramite= td_SecuenciasTramiteModel::find(decrypt($id));;
            $Secuenciatramite->anio= $request->anio;
            $Secuenciatramite->numero= $request->numero;
            $Secuenciatramite->idprioridad_tramite= $request->prioridad;

            $Secuenciatramite->update();

            return back()->with([
                'mensajeInfo'=>"Registro modifcado exitosamente.!",
                'mensajeColor'=>"success"
            ]);
            
            } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se puedo editar el Registro",
                'mensajeColor'=>"error"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\td_SecuenciatramiteModel  $td_SecuenciatramiteModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $Secuenciatramite = td_SecuenciasTramiteModel::find(decrypt($id));
            //dd($prioridadtramite);
            if($Secuenciatramite->delete())
            {
            return back()->with([
                'mensajeInfo'=>"La información fué eliminada con éxito",
                'mensajeColor'=>"success"
            ]);
            }
            else{
                 return back()->with([
                'mensajeInfo'=>"Error: No se pudo eliminar",
                'mensajeColor'=>"danger"
            ]);
            }
    }
}
