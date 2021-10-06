<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MenuModel;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listamenu=MenuModel::all();
        return response()->json($listamenu);

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
            'nombremenu'=>$request->get('nombre_menu'),
            'icono'=>$request->get('icon_menu'),
        );

        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoMenu'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };
        //guardamos el menu en la base de datos
        $menu= new MenuModel();
        $menu->nombremenu=$request->get('nombre_menu');
        $menu->icono=$request->get('icon_menu');
        if($menu->save()){
            return back()->with(['mensajePInfoMenu'=>'Registro exitoso','estadoP'=>'success']);
        }else{
            return back()->with(['mensajePInfoMenu'=>'No se pudo realizar el registro','estadoP'=>'error']);
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
        $menu=MenuModel::find($id);
        return response()->json($menu);
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
            'nombremenu'=>$request->get('nombre_menu'),
            'icono'=>$request->get('icon_menu'),
        );

        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoMenu'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };
        //guardamos el menu en la base de datos
        $menu= MenuModel::find(decrypt($id));
        $menu->nombremenu=$request->get('nombre_menu');
        $menu->icono=$request->get('icon_menu');
        if($menu->save()){
            return back()->with(['mensajePInfoMenu'=>'Actualización exitosa','estadoP'=>'success']);
        }else{
            return back()->with(['mensajePInfoMenu'=>'No se pudo realizar la actualización','estadoP'=>'error']);
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
            return back()->with(['mensajePInfoMenu'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };
        //guardamos el menu en la base de datos
        $menu= MenuModel::find(decrypt($id));
        try {
            $menu->delete();
            return back()->with(['mensajePInfoMenu'=>'El registro fué eliminado','estadoP'=>'success']);
        } catch (\Throwable $th) {
            return back()->with(['mensajePInfoMenu'=>'No se pudo eliminar el registro ya que se encuentra relacionado','estadoP'=>'error']);
        }

    }


}
