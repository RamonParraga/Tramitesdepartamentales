<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoFPModel;

class TipoFPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // solo los datos que vamos a validar
        $validaCE=array(
            'nombre_tipoFP'=>$request->get('nombre_tipoFP'),
        );

        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoTipoFP'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };
        //guardamos el menu en la base de datos
        $tipoFP= new TipoFPModel();
        $tipoFP->descripcion=$request->get('nombre_tipoFP');
        if($tipoFP->save()){
            return back()->with(['mensajePInfoTipoFP'=>'Registro exitoso','estadoP'=>'success']);
        }else{
            return back()->with(['mensajePInfoTipoFP'=>'No se pudo realizar el registro','estadoP'=>'error']);
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
        $id=decrypt($id);
        $tipoFP=TipoFPModel::find($id);
        return response()->json($tipoFP);
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
        // solo los datos que vamos a validar
        $validaCE=array(
            'id'=>decrypt($id),
            'nombre_tipoFP'=>$request->get('nombre_tipoFP'),
        );

        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoTipoFP'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };
        //guardamos el menu en la base de datos
        $tipoFP= TipoFPModel::find(decrypt($id));
        $tipoFP->descripcion=$request->get('nombre_tipoFP');
        if($tipoFP->save()){
            return back()->with(['mensajePInfoTipoFP'=>'Registro actualizado con exito','estadoP'=>'success']);
        }else{
            return back()->with(['mensajePInfoTipoFP'=>'No se pudo actualizar el registro','estadoP'=>'error']);
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
        $validaCE=array(
            'id'=>decrypt($id)
        );

        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoTipoFP'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };
        //guardamos el menu en la base de datos
        $tipoFP= TipoFPModel::find(decrypt($id));

        try {
            $tipoFP->delete();
            return back()->with(['mensajePInfoTipoFP'=>'El registro fué eliminado','estadoP'=>'success']);
        } catch (\Throwable $th) {
            return back()->with(['mensajePInfoTipoFP'=>'No se pudo eliminar el registro ya que se encuentra relacionado','estadoP'=>'error']);
        }

    }
}
