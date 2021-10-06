<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\DepartamentoModel;
use App\TipoTramiteModel as tTramite;
use Illuminate\Http\Request;

class TipoTramiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departamentos = DepartamentoModel::all();
        $tipotramite=  DB::table('td_tipo_tramite')
                                ->join('td_departamento', 'td_tipo_tramite.iddepartamento', '=', 'td_departamento.iddepartamento')
                                ->select('td_tipo_tramite.*', 'td_departamento.nombre as nombredepartamento')
                                ->get();
                                
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
        try{    
            $tipotramite= new tTramite();
            $tipotramite->tipo=$request->tipo;
            $tipotramite->descripcion=$request->descripcion;
            $tipotramite->ayuda=$request->ayuda;
            $tipotramite->iddepartamento=$request->iddepartamento;
            $tipotramite->save();
            return back()->with([
                'mensajeInfo'=>"Tipo de tramite guardodo con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se puedo guardar el tipo de tramite",
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
        $tipotramite = tTramite::find(decrypt($id));
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
        try{
            $tipotramite = tTramite::find(decrypt($id));
            $tipotramite->tipo=$request->tipo;
            $tipotramite->descripcion=$request->descripcion;
            $tipotramite->ayuda=$request->ayuda;
            $tipotramite->iddepartamento=$request->iddepartamento;
            $tipotramite->update();
            return back()->with([
                'mensajeInfo'=>"Tipo de tramite editado con exito",
                'mensajeColor'=>"success"
            ]);

        } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se puedo editar el tipo de tramite",
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
        //
    }
}
