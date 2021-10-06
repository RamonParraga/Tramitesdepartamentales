<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DepartamentoModel;
use App\PeriodoModel;
use Log;

class DepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // obtenemos todos los departamentos que pertenescan a un periodo con estado de A (activo)
        $listaDepartamentos = DepartamentoModel::with('periodo','jefe_departamento')->get();
        
        //obtenemos todos los periodos para cargar en los combos
        $listaPeridos = PeriodoModel::orderBy("estado","asc")->get();
        
        return view('tramitesDepartamentales.gestionDepartamentos.gestionDepartamentos')
            ->with([
                'listaDepartamentos'=>$listaDepartamentos,
                'listaPeridos'=>$listaPeridos
            ]);
    }

    public function filtrarDepartamentosPorPeriodo($idperiodo){

        // obtenemos todos los departamentos que pertenescan a un periodo seleccionado por el usuario
        $listaDepartamentos = DepartamentoModel::with('periodo', 'jefe_departamento')->whereHas('periodo',function($query) use ($idperiodo){
            $query->where('idperiodo','=',$idperiodo);
        })->get();

        return response()->json($listaDepartamentos);
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
        try {
            $iddepartamento_padre=null;
            $idperiodo=$request->gd_select_periodo;

            // si no se selecciona un departamento se pregunta si ya existe un departamento padre registrado en se periodo
            if(is_null($request->gd_select_departamento_padre)){
                
                // buscamos un departamento padre general con el periodo ingresado
                $departamentoPadre=DepartamentoModel::where('idperiodo',$idperiodo)
                                                    ->whereNull('iddepartamento_padre')
                                                    ->first();
                if(!is_null($departamentoPadre)){ // si ya extiste un departamento con departamento padre nulo (departamento padre global)

                    // le decimos al usuario que no puede registrar otro departamento padre global
                    return back()->with([
                        'mensajeInfo'=>"Ya existe un departamento padre general. Por favor seleccione un departamento",
                        'mensajeColor'=>"error"
                    ]);
                }
            }else{
                $iddepartamento_padre=$request->gd_select_departamento_padre;
            }

            // verificamos si se selecciona el check de permitir tramites externos en el departamento
            $tramite_externo = 0; // tramite externo no permitido en el departamento
            if(isset($request->check_tramite_externo)){
                $tramite_externo = 1; // tramite externo permitido en el departamento
            }
        
            $obj_departamento = new DepartamentoModel();
            $obj_departamento->nombre=$request->gd_nombre;
            $obj_departamento->codCabildo=$request->gd_codcabildo;
            $obj_departamento->abreviacion=$request->gd_abreviacion;
            $obj_departamento->nivel=$request->gd_nivel;
            $obj_departamento->correo=$request->gd_correo;
            $obj_departamento->iddepartamento_padre=$iddepartamento_padre;
            $obj_departamento->idperiodo=$idperiodo;
            $obj_departamento->tramite_externo = $tramite_externo;
        
            $obj_departamento->save();

            return back()->with([
                'mensajeInfo'=>"Departamento guardodo con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            Log::erro($th->getMessage());
            return back()->with([
                'mensajeInfo'=>"No se pudo guardar el departamento",
                'mensajeColor'=>"error"
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
        $departamento = DepartamentoModel::find($id);  // buscamos el departamento       
        return response()->json($departamento);
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
        try {

            $iddepartamento_padre=null;
            $idperiodo=$request->gd_select_periodo;

            // si no se selecciona un departamento se pregunta si ya existe un departamento padre registrado en se periodo
            if(is_null($request->gd_select_departamento_padre)){
                
                // buscamos un departamento padre general con el periodo ingresado
                $departamentoPadre=DepartamentoModel::where('idperiodo',$idperiodo)
                                                    ->whereNull('iddepartamento_padre')
                                                    ->first();
                if(!is_null($departamentoPadre)){ // si ya extiste un departamento con departamento padre nulo (departamento padre global)
                    if($departamentoPadre->iddepartamento==$id){
                        goto REGISTRAR;
                    }
                    // le decimos al usuario que no puede registrar otro departamento padre global
                    return back()->with([
                        'mensajeInfo'=>"Ya existe un departamento padre general. Por favor seleccione un departamento",
                        'mensajeColor'=>"error"
                    ]);
                }
            }else{
                $iddepartamento_padre=$request->gd_select_departamento_padre;
            }

            REGISTRAR:

            
            // verificamos si se selecciona el check de permitir tramites externos en el departamento
            $tramite_externo = 0; // tramite externo no permitido en el departamento
            if(isset($request->check_tramite_externo)){
                $tramite_externo = 1; // tramite externo permitido en el departamento
            }

            $obj_departamento = DepartamentoModel::find($id);  // buscamos el departamento
            $obj_departamento->nombre=$request->gd_nombre;
            $obj_departamento->codCabildo=$request->gd_codcabildo;
            $obj_departamento->abreviacion=$request->gd_abreviacion;
            $obj_departamento->nivel=$request->gd_nivel;
            $obj_departamento->correo=$request->gd_correo;
            $obj_departamento->iddepartamento_padre=$iddepartamento_padre;
            $obj_departamento->idperiodo=$idperiodo;
            $obj_departamento->tramite_externo = $tramite_externo;
            $obj_departamento->save();

            return back()->with([
                'mensajeInfo'=>"Departamento guardodo con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return back()->with([
                'mensajeInfo'=>"No se pudo guardar el departamento",
                'mensajeColor'=>"error"
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
        try {
            $obj_departamento = DepartamentoModel::find($id);  // buscamos el departamento
            $obj_departamento->delete();

            return back()->with([
                'mensajeInfo'=>"Departamento eliminado con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th) {
            return back()->with([
                'mensajeInfo'=>"No se pudo eliminar el departamento",
                'mensajeColor'=>"error"
            ]);
        }

    }
}
