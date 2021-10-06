<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GestionModel;

class GestionController extends Controller
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
            'nombre_gestion'=>$request->get('nombre_gestion'),
            'ruta_gestion'=>$request->get('ruta_gestion'),
            'icono_gestione'=>$request->get('icono_gestione'),
            'gestion_selec_menu'=>$request->get('gestion_selec_menu'),
        );

        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoGestion'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };
        //guardamos el menu en la base de datos
        $gestion= new GestionModel();
        $gestion->nombregestion=$request->get('nombre_gestion');
        $gestion->ruta=$request->get('ruta_gestion');
        $gestion->icono=$request->get('icono_gestione');
        $gestion->idmenu=$request->get('gestion_selec_menu');
        if($gestion->save()){
            return back()->with(['mensajePInfoGestion'=>'Registro exitoso','estadoP'=>'success']);
        }else{
            return back()->with(['mensajePInfoGestion'=>'No se pudo realizar el registro','estadoP'=>'error']);
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
        $gestion=GestionModel::find($id);
        return response()->json($gestion);
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
            'id'=>$id,
            'nombre_gestion'=>$request->get('nombre_gestion'),
            'ruta_gestion'=>$request->get('ruta_gestion'),
            'icono_gestione'=>$request->get('icono_gestione'),
            'gestion_selec_menu'=>$request->get('gestion_selec_menu'),
        );

        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoGestion'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };
        //guardamos el menu en la base de datos
        $gestion= GestionModel::find(decrypt($id));
        $gestion->nombregestion=$request->get('nombre_gestion');
        $gestion->ruta=$request->get('ruta_gestion');
        $gestion->icono=$request->get('icono_gestione');
        $gestion->idmenu=$request->get('gestion_selec_menu');
        if($gestion->save()){
            return back()->with(['mensajePInfoGestion'=>'Registro Actualizado','estadoP'=>'success']);
        }else{
            return back()->with(['mensajePInfoGestion'=>'No se pudo actualizar el registro','estadoP'=>'error']);
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
            return back()->with(['mensajePInfoGestion'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };
        //guardamos el menu en la base de datos
        $gestion= GestionModel::find(decrypt($id));

        try {
            $gestion->delete();
            return back()->with(['mensajePInfoGestion'=>'El registro fué eliminado','estadoP'=>'success']);
        } catch (\Throwable $th) {
            return back()->with(['mensajePInfoGestion'=>'No se pudo eliminar el registro ya que se encuentra relacionado','estadoP'=>'error']);
        }

    }
}
