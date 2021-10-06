<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\td_EstructuraDocumentoModel;
use App\td_TipoDocumentoModel;
use Illuminate\Http\Request;
use App\DepartamentoModel;
use Log;

class TdEstructuraDocumentoModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $tipodocumento=td_TipoDocumentoModel::where('prioridad',1)->get();
        

        $departamento = DB::table('td_departamento')
        ->join('td_periodo', 'td_departamento.idperiodo', '=', 'td_periodo.idperiodo')
        ->select('td_departamento.*')
        ->where('td_periodo.estado','=','A')
        ->get();

        $estructuradocumento = DB::table('td_estructura_documento')
        ->join('td_departamento', 'td_estructura_documento.iddepartamento', '=', 'td_departamento.iddepartamento')
        ->join('td_tipo_documento', 'td_estructura_documento.idtipo_documento', '=', 'td_tipo_documento.idtipo_documento')
        ->select(
            'td_estructura_documento.idestructura_documento',
            'td_estructura_documento.idtipo_documento',
            'td_estructura_documento.anio',
            'td_tipo_documento.estructura as gad',
            'td_departamento.nombre as nombredepartamento',
            'td_departamento.abreviacion as abreviaciondepartamento',
            'td_tipo_documento.abreviacion as abreviaciontipodocumento',
            'td_tipo_documento.descripcion as descripciontipodocumento',
            'td_tipo_documento.secuencia as secuencia_tipodocumento',
            'td_estructura_documento.secuencia as secuencia_estructuradocumento'
        )
        ->where('td_estructura_documento.estado', '=', 1)
        ->get();

        return view('tramitesDepartamentales.gestionEstructuraDocumento.gestionEstructuraDocumento',['departamento' => $departamento, 'tipodocumento' => $tipodocumento, 'estructuradocumento'=> $estructuradocumento]);
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
            $buscarEstructura=td_EstructuraDocumentoModel::where('iddepartamento',$request->iddepartamento)
            ->where('idtipo_documento',$request->idtipodocumento)->first();
            if($buscarEstructura!=null){
                return back()->with([
                    'mensajeInfo'=>"Estructura de documento ya se encuentra registrada.",
                    'mensajeColor'=>"error"
                ]);
            }
            $estructuradocumento= new td_EstructuraDocumentoModel();
            $estructuradocumento->anio= $request->anio;
            $estructuradocumento->secuencia= $request->secuencia;
            $estructuradocumento->idtipo_documento= $request->idtipodocumento;
            $estructuradocumento->iddepartamento= $request->iddepartamento;
            $estructuradocumento->estado=1;
            $estructuradocumento->save();
            // dd($estructuradocumento);
            return back()->with([
                'mensajeInfo'=>"Estructura de documento guardado con éxito",
                'mensajeColor'=>"success"
            ]);
    
        } catch (\Throwable $th){
            return back()->with([
                'mensajeInfo'=>"No se puedo guardar la Estructura de Documento",
                'mensajeColor'=>"error"
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\td_EstructuraDocumentoModel  $td_EstructuraDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function show(td_EstructuraDocumentoModel $td_EstructuraDocumentoModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\td_EstructuraDocumentoModel  $td_EstructuraDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $id=decrypt($id);
            $tdEstructura=td_EstructuraDocumentoModel::find($id);
            return response()->json([
                'error'=>false,
                'detalle'=>$tdEstructura
                
            ]);
        } catch (\Throwable $th){
            return response()->json([
                'error'=>true,
                'mensajeInfo'=>"No se puede editar la Estructura de Documento",
                'mensajeColor'=>"error"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\td_EstructuraDocumentoModel  $td_EstructuraDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
        try {
            $id=decrypt($id);
            $buscarEstructura=td_EstructuraDocumentoModel::where('iddepartamento',$request->iddepartamento)
                ->where('idtipo_documento',$request->idtipodocumento)
                ->first();

            // dd($buscarEstructura);
            if($buscarEstructura!=null){
                if($buscarEstructura->idestructura_documento!=$id){
                    return back()->with([
                        'mensajeInfo'=>"Estructura de documento ya se encuentra registrada.",
                        'mensajeColor'=>"error"
                    ]);
                }
            }

            $tdEstructura=td_EstructuraDocumentoModel::find($id);
            $tdEstructura->anio=$request->anio;
            $tdEstructura->secuencia=$request->secuencia;
            $tdEstructura->idtipo_documento=$request->idtipodocumento;
            $tdEstructura->iddepartamento=$request->iddepartamento;
            $tdEstructura->save();
            
            return back()->with([
                'error'=>false,
                'mensajeInfo'=>"Actualización exitosa",
                'mensajeColor'=>"success"
            ]);            
        }catch(\Throwable $th){
            Log::error("TdEstructuraDocumentoModelController => update => Mensaje:".$th->getMessage());
            return back()->with([
                'error'=>true,
                'mensajeInfo'=>"No se puede actualizar la estructura de documento",
                'mensajeColor'=>"error"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\td_EstructuraDocumentoModel  $td_EstructuraDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $id=decrypt($id);
            $tdEstructura=td_EstructuraDocumentoModel::find($id);
            $tdEstructura->delete();
            return back()->with([
                'error'=>false,
                'mensajeInfo'=>"Registro eliminado",
                'mensajeColor'=>"success"
            ]);
            
        } catch (\Throwable $th){
            return back()->with([
                'error'=>true,
                'mensajeInfo'=>"No se puede actualizar la estructura de documento",
                'mensajeColor'=>"error"
            ]);
        }
    }

    public function AnioNuevo(Request $request)
    {

        try{
            $anioNuevo = $request->anionuevo;
            
            // verificamos si el año ya está registrado
                $existeAnio = td_EstructuraDocumentoModel::where('anio', $anioNuevo)->first();
                if(!is_null($existeAnio)){
                    DB::table('td_estructura_documento')
                    ->where('anio', '=', $anioNuevo)
                    ->update(['estado'=>1]);
                    goto DESACTIVAROTROS;
                }

            $ultimoAnio = td_EstructuraDocumentoModel::where('estado', 1)->pluck('anio')->first(); // obtenemos el anio activo
            $estructuradocumento = td_EstructuraDocumentoModel::where('anio', $ultimoAnio)->get();

            foreach ($estructuradocumento as $estructuradoc){
                $nuevaEstructura = new td_EstructuraDocumentoModel();
                $nuevaEstructura->anio =  $anioNuevo;
                $nuevaEstructura->secuencia = 0;
                $nuevaEstructura->iddepartamento = $estructuradoc->iddepartamento;
                $nuevaEstructura->idtipo_documento = $estructuradoc->idtipo_documento;
                $nuevaEstructura->estado = 1;
                $nuevaEstructura->save();
            }

            DESACTIVAROTROS:
            DB::table('td_estructura_documento')
                ->where('anio', '<>', $anioNuevo)
                ->update(['estado'=>0]);

            $retorno=['mensaje'=>'Nuevo anio iniciado con exito','status'=>'success'];
            return back()->with($retorno);
    
        } catch (\Throwable $th){
            Log::error("Error TdEstructuraDocumentoModelController => AnioNuevo => Mensaje:".$th->getMessage());
            $retorno=['mensaje'=>"Error resetear el año.",'status'=>'error'];
            return back()->with($retorno);
        }

        


    }
}
