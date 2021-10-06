<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\DepartamentoModel;
use App\td_TipoTramiteModel as tTramite;
use App\td_TipoTramiteDepartamentoModel;


class TdTipoTramiteModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function index()
    {
        $departamentos = DepartamentoModel::with("periodo")
            ->whereHas("periodo", function($query_periodo){
                $query_periodo->where("estado","A");
            })->get();
        //dd( $departamentos);   
        $tipotramite=tTramite::with('tipotramite_departamento')->where('estado', 1)->get();  
        return view('tramitesDepartamentales.gestionTipoTramite.gestionTipoTramite', ['departamentos' => $departamentos, 'tipotramite'=>$tipotramite]);
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
         $es_global=$request->input_tramite_global;
            if(!is_null($es_global)){

                if($es_global=='on'){
                    $tipotramiteglobal=new tTramite();
                    $tipotramiteglobal->tipo=$request->tipo;
                    $tipotramiteglobal->descripcion=$request->descripcion;
                    $tipotramiteglobal->ayuda=$request->ayuda;
                    //$tipotramiteglobal->iddepartamento=$actividad;
                    $tipotramiteglobal->estado=1;
                    $tipotramiteglobal->tramite_global=1;
                    $tipotramiteglobal->save();
                }
                    return back()->with([
                    'mensajeInfo'=>"Tipo de tramite guardado con exito",
                    'mensajeColor'=>"success"
                ]);

            }else{

                $tipotramite=new tTramite();
                $tipotramite->tipo=$request->tipo;
                $tipotramite->descripcion=$request->descripcion;
                $tipotramite->ayuda=$request->ayuda;
                //$tipotramite->iddepartamento=$actividad;
                $tipotramite->estado=1;
                $tipotramite->tramite_global=0;
                $tipotramite->save();

                if(!is_null($request->input_actividad)){
                    foreach ($request->input_actividad as $actividad) {
                        //por cada actividad creamos un nuevo obj y no ingresamos a la base de datos con su prespectivo departamento
                        $tipotramitedepartamento= new td_TipoTramiteDepartamentoModel();     
                        $tipotramitedepartamento->idtipo_tramite=$tipotramite->idtipo_tramite;
                        $tipotramitedepartamento->iddepartamento=$actividad;
                        $tipotramitedepartamento->save();
                    }                    
                }

            
                return back()->with([
                    'mensajeInfo'=>"Tipo de tramite guardado con exito",
                    'mensajeColor'=>"success"
                
                ]);
            }
            

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipotramite = tTramite::with('tipotramite_departamento')->find(decrypt($id));
        return response()->json($tipotramite);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $idtipotramite=decrypt($id);
        $es_global=$request->input_tramite_global;
    
        if(!is_null($es_global)){
            if($es_global=='on'){
                $tipotramiteglobal=tTramite::find($idtipotramite);
                $tipotramiteglobal->tipo=$request->tipo;
                $tipotramiteglobal->descripcion=$request->descripcion;
                $tipotramiteglobal->ayuda=$request->ayuda;
                //$tipotramiteglobal->iddepartamento=$actividad;
                $tipotramiteglobal->estado=1;
                $tipotramiteglobal->tramite_global=1;
                $tipotramiteglobal->save();
            }
            
            return back()->with([
                'mensajeInfo'=>"Tipo de tramite actualizado con exito",
                'mensajeColor'=>"success"            
            ]);

        }else{
        
            $tipo_tram_dep= td_TipoTramiteDepartamentoModel::where('idtipo_tramite', $idtipotramite);
            $tipo_tram_dep->delete();
                            
            $tipotramite=tTramite::find($idtipotramite);
            $tipotramite->tipo=$request->tipo;
            $tipotramite->descripcion=$request->descripcion;
            $tipotramite->ayuda=$request->ayuda;
            //$tipotramite->iddepartamento=$actividad;
            $tipotramite->estado=1;
            $tipotramite->tramite_global=0;
            $tipotramite->save();

            if(!is_null($request->input_actividad)){
                foreach ($request->input_actividad as $e => $actividad) {
                    $tipotramitedepartamento= new td_TipoTramiteDepartamentoModel();                    
                    $tipotramitedepartamento->idtipo_tramite=$tipotramite->idtipo_tramite;
                    $tipotramitedepartamento->iddepartamento=$actividad;                    
                    $tipotramitedepartamento->save();                        
                }                    
            }
        
            return back()->with([
                'mensajeInfo'=>"Tipo de tramite actualizado con exito",
                'mensajeColor'=>"success"
            
            ]);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try{
            $tipo_tram_dep= td_TipoTramiteDepartamentoModel::where('idtipo_tramite', decrypt($id));
            $tipo_tram_dep->delete();
            $tipotramite = tTramite::find(decrypt($id));
            $tipotramite->estado=0;
            $tipotramite->update();
            return back()->with([
                'mensajeInfo'=>"Tipo de tramite Eliminado con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se pudo Eliminar el tipo de tramite",
                'mensajeColor'=>"error"
            ]);
        }
    }   
}
