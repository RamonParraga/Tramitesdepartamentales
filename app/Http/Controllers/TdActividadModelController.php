<?php

namespace App\Http\Controllers;

use App\td_ActividadModel;
use Illuminate\Http\Request;
use App\DepartamentoModel;
use App\PeriodoModel;

class TdActividadModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // obtenemos todos los departamentos que pertenescan a un periodo con estado de A (activo)
        $listaDepartamentos = DepartamentoModel::with('periodo')->get();

        //obtenemos todos los periodos para cargar en los combos
        $listaPeridos = PeriodoModel::all();

       return view('tramitesDepartamentales.gestionDepartamentos.gestionActividad.gestionActividad')
            ->with([
                'listaDepartamentos'=>$listaDepartamentos,
                'listaPeridos'=>$listaPeridos
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
        //dd($request->request);
        try {

            // comprobamos que almenos recivamos una actividad
            if(!isset($request->input_actividad)){goto FALTANDATOS;}

            //verificamos que se seleccione el departamento
            if(is_null($request->gd_select_cmb_departamento)){goto FALTANDATOS;}

            // obtenemos el id del departamento al que agregaremos las actividades
            $iddepartamento = $request->gd_select_cmb_departamento;

            //recorremos cada una de las actividades. $input_actividad es un arreglo (input_actividad[])
            foreach ($request->input_actividad as $a => $actividad) {
                //por cada actividad creamos un nuevo obj y no ingresamos a la base de datos con su prespectivo departamento
                $obj_actividad = new td_ActividadModel();
                $obj_actividad->descripcion = $actividad;
                $obj_actividad->iddepartamento=$iddepartamento;
                $obj_actividad->estado_del=0;
                $obj_actividad->save(); 
            }
            return back()->with([
                'mensajeInfo'=>"Actividades registradas con exito.",
                'mensajeColor'=>"success"
            ]);
                    
            FALTANDATOS:
            //en caso de no recivir actividades lo notificamos
            return back()->with([
                'mensajeInfo'=>"Faltan datos por ingresar",
                'mensajeColor'=>"default"
            ]);
                      
        } catch (\Throwable $th) {
            //notificamos que se pridujo un error

            return back()->with([
                'mensajeInfo'=>"No se pudo registrar las actividades.",
                'mensajeColor'=>"error"
            ]);
        }
       
    }

    public function filtrarActividadPorDepartamento($iddepartamento){

        //obtenemos todas las actividades de un departamento por el id del mismo
        $obj_actividad = td_ActividadModel::with('departamento')->where('iddepartamento',$iddepartamento)->get();

        return response()->json($obj_actividad);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\td_ActividadModel  $td_ActividadModel
     * @return \Illuminate\Http\Response
     */
    public function show(td_ActividadModel $td_ActividadModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\td_ActividadModel  $td_ActividadModel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //buscamos una actividad especifica por el id de la misma
        $obj_actividad = td_ActividadModel::find($id);
        return response()->json($obj_actividad);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\td_ActividadModel  $td_ActividadModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // print("ACTUALIZANDO:".$id);
        // dd($request->request);
        try {

            //verificamos que la actividad no venga vacia
            if(is_null($request->ga_actividad)){
                return back()->with([
                    'mensajeInfo'=>"Faltan datos por ingresar.",
                    'mensajeColor'=>"default"
                ]);
            }

            //modificamos la actividad
            $obj_actividad = td_ActividadModel::find($id);
            $obj_actividad->descripcion = $request->ga_actividad;
            if($obj_actividad->save()){
                return back()->with([
                    'mensajeInfo'=>"Actividad guardada con exito.",
                    'mensajeColor'=>"success"
                ]);
            }
        } catch (\Throwable $th) {
            //notificamos que se pridujo un error
            return back()->with([
                'mensajeInfo'=>"No se pudo guardar la actividad.",
                'mensajeColor'=>"error"
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\td_ActividadModel  $td_ActividadModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //print("ELIMINANDO: ".$id);
        try {
            //buscamos y eliminamos la actividad
            $obj_actividad = td_ActividadModel::find($id);
            if($obj_actividad->delete()){
                return back()->with([
                    'mensajeInfo'=>"Actividad eliminada con exito.",
                    'mensajeColor'=>"success"
                ]);
            }
        } catch (\Throwable $th) {
            //notificamos que se pridujo un error
            return back()->with([
                'mensajeInfo'=>"No se pudo eliminar la actividad.",
                'mensajeColor'=>"error"
            ]);
        }
    }
}
