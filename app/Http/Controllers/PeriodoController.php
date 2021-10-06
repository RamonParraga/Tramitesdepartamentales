<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PeriodoModel;

class PeriodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $obj_periodos=PeriodoModel::all();
        return response()->json($obj_periodos);
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

            // definimos el formato de fechas que admite la base de datos (año-mes-dia)
            $fecha_inicio=date("Y-m-d", strtotime($request->fecha_inicio));
            $fecha_fin=date("Y-m-d", strtotime($request->fecha_fin));
    
            // comparamos que la fecha de inicio no sea mayor o igual a la fecha de fin
            if($fecha_inicio >= $fecha_fin){
                $retorno=['mensaje'=>'El rango de fechas no es correcto','status'=>'error'];
                return response()->json($retorno);
            }
    
            // verificamos que se seleccione un estado del periodo que se desea ingresar
            if($request->estado==""){
                $retorno=['mensaje'=>'Seleccione un estado','status'=>'error'];
                return response()->json($retorno);
            }else{
                //verificamos que no existan periodos como activos
                if($request->estado=="A"){
                    //buscamos el periodo que tenga un estado activo
                    $buscarActivo = PeriodoModel::where('estado','A')->first();
                    if(!is_null($buscarActivo)){ // si no es nulo quiere decir que existe un periodo activo
                        $retorno=['mensaje'=>'Ya existe un periodo activo','status'=>'error'];
                        return response()->json($retorno);
                    }
                }
            }
    
            //cremos un nuevo periodo
            $obj_periodo = new PeriodoModel();
            $obj_periodo->fecha_inicio=$fecha_inicio;
            $obj_periodo->fecha_fin=$fecha_fin;
            $obj_periodo->estado = $request->estado;
    
            if($obj_periodo->save()){
                $retorno=['mensaje'=>'Periodo guardado con exito','status'=>'success'];
                return response()->json($retorno);
            };
            
        } catch (\Throwable $th) {
            $retorno=['mensaje'=>"Error no se pudo ejecutar la instrucción.",'status'=>'error'];
            return response()->json($retorno);
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
        $obj_periodo = PeriodoModel::find($id);

        //cambiamos el formato de la fecha
        $obj_periodo->fecha_inicio=date("m/d/Y", strtotime($obj_periodo->fecha_inicio));
        $obj_periodo->fecha_fin=date("m/d/Y", strtotime($obj_periodo->fecha_fin));

        return response()->json($obj_periodo);
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

            // definimos el formato de fechas que admite la base de datos (año-mes-dia)
            $fecha_inicio=date("Y-m-d", strtotime($request->fecha_inicio));
            $fecha_fin=date("Y-m-d", strtotime($request->fecha_fin));
    
            // comparamos que la fecha de inicio no sea mayor o igual a la fecha de fin
            if($fecha_inicio >= $fecha_fin){
                $retorno=['mensaje'=>'El rango de fechas no es correcto','status'=>'error'];
                return response()->json($retorno);
            }
    
            // verificamos que se seleccione un estado del periodo que se desea ingresar
            if($request->estado==""){
                $retorno=['mensaje'=>'Seleccione un estado','status'=>'error'];
                return response()->json($retorno);
            }else{
                //verificamos que no existan periodos como activos
                if($request->estado=="A"){
                    //buscamos el periodo que tenga un estado activo
                    $buscarActivo = PeriodoModel::where('estado','A')->first();
                    if(!is_null($buscarActivo)){ // si no es nulo quiere decir que existe un periodo activo
                        if($buscarActivo->idperiodo != $id){ // preguntamos si el periodo que esta activo no es el que estamos editando
                            $retorno=['mensaje'=>'Ya existe un periodo activo','status'=>'error'];
                            return response()->json($retorno);
                        }
                    }
                }
            }
    
            //cremos un nuevo periodo
            $obj_periodo = PeriodoModel::find($id);;
            $obj_periodo->fecha_inicio=$fecha_inicio;
            $obj_periodo->fecha_fin=$fecha_fin;
            $obj_periodo->estado = $request->estado;
    
            if($obj_periodo->save()){
                $retorno=['mensaje'=>'Periodo actualizado con exito','status'=>'success'];
                return response()->json($retorno);
            };
            
        } catch (\Throwable $th) {
            $retorno=['mensaje'=>"Error no se pudo ejecutar la instrucción.",'status'=>'error'];
            return response()->json($retorno);
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
            $obj_periodo = PeriodoModel::find($id);
            $obj_periodo->delete();
            $retorno=['mensaje'=>'Periodo eliminado con exito','status'=>'success'];
            return response()->json($retorno);
        } catch (\Throwable $th) {
            $retorno=['mensaje'=>'No se puede eliminar el periodo','status'=>'error'];
            return response()->json($retorno);
        }

    }
}
