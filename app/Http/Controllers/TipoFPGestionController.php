<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoFPGestionModel;

class TipoFPGestionController extends Controller
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
        $validaCE=array(
            'idtipoFP'=>$request->get('AGTFP_tipousuario'),
            'idgestion'=>$request->get('AGTFP_gestion')
        );

        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoAsignarGesion'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };

        $tipoFP_gestion = new TipoFPGestionModel();
        $tipoFP_gestion->idtipoFp=$request->get('AGTFP_tipousuario');
        $tipoFP_gestion->idgestion=$request->get('AGTFP_gestion');

        $buscarAsignacion = TipoFPGestionModel::where('idtipoFP',$tipoFP_gestion->idtipoFp)
                                                ->where('idgestion', $tipoFP_gestion->idgestion)
                                                ->first();
        if(!is_null($buscarAsignacion)){
            return back()->with(['mensajePInfoAsignarGesion'=>'La asignación ya existe','estadoP'=>'info']);
        }

        try {
            $tipoFP_gestion->save();
            return back()->with(['mensajePInfoAsignarGesion'=>'Registro Exitoso','estadoP'=>'success']);
        } catch (\Throwable $th) {
            return back()->with(['mensajePInfoAsignarGesion'=>'No se pudo realizar el registro','estadoP'=>'error']);
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
        $tipoFP_gestion = TipoFPGestionModel::find(decrypt($id));
        return response()->json($tipoFP_gestion);

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
        $validaCE=array(
            'id'=>decrypt($id),
            'idtipoFP'=>$request->get('AGTFP_tipousuario'),
            'idgestion'=>$request->get('AGTFP_gestion')
        );

        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoAsignarGesion'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };

        $tipoFP_gestion = TipoFPGestionModel::find(decrypt($id));
        $tipoFP_gestion->idtipoFp=$request->get('AGTFP_tipousuario');
        $tipoFP_gestion->idgestion=$request->get('AGTFP_gestion');

        $buscarAsignacion = TipoFPGestionModel::where('idtipoFP',$tipoFP_gestion->idtipoFp)
                                                ->where('idgestion', $tipoFP_gestion->idgestion)
                                                ->first();
        if(!is_null($buscarAsignacion)){
            return back()->with(['mensajePInfoAsignarGesion'=>'La asignación ya existe','estadoP'=>'info']);
        }

        try {
            $tipoFP_gestion->save();
            return back()->with(['mensajePInfoAsignarGesion'=>'Registro actualizado con exito','estadoP'=>'success']);
        } catch (\Throwable $th) {
            return back()->with(['mensajePInfoAsignarGesion'=>'No se pudo actualizar el registro','estadoP'=>'error']);
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
        $tipoFP_gestion = TipoFPGestionModel::find(decrypt($id));

        try {
            $tipoFP_gestion->delete();
            return back()->with(['mensajePInfoAsignarGesion'=>'Registro eliminado con exito','estadoP'=>'success']);
        } catch (\Throwable $th) {
            return back()->with(['mensajePInfoAsignarGesion'=>'No se pudo eliminar el registro porque esta relacionado','estadoP'=>'error']);
        }
    }
}
