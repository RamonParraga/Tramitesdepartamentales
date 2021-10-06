<?php

namespace App\Http\Controllers;

use App\td_SectorModel as SectorModel;
use App\td_SeccionModel as SeccionModel;
use App\td_BodegaModel as BodegaModel;
use App\td_TramiteModel as TramiteModel;


use App\td_GestionArchivoModel as GestionArchivoModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use DB;



class TdGestionRegistroArchivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
  
        //obtengo los idtramite de todos los guardados en la tabla gestion archivo
        $idtramites=GestionArchivoModel::pluck('idtramite');
        
        //obtengo el id del departamento logueado
        $departamento_logueado=departamentoLogueado()['iddepartamento'];
        
        //esta muestra la tabla de los tramites finalizados de acuerdo a la persona logueada para registrar en la gestion de archivo, excluyendo los ya guardados ($idtramite) 
        $listaTramite=TramiteModel::whereNotIn('idtramite', $idtramites)->where('iddepartamento_temina',$departamento_logueado)
            ->where('finalizado',1)
            ->get(); 
   
        //consulta que obtiene los id de los tramites por dep logueado
        $obteneridtramite=TramiteModel::where('iddepartamento_temina',$departamento_logueado)->pluck('idtramite');

        $listaGestionArchivo=[];
        if(!is_null($obteneridtramite)){
    
            $listaGestionArchivo=GestionArchivoModel::with('seccion','tramitedoc')
            ->whereIn('idtramite',$obteneridtramite)
            ->take(10)
            ->orderBy('fecha_gestion', 'desc')
            ->get();
            
        }

        $a='G';
        $obtener_idbodega_x_deparlogueado=BodegaModel::where('iddepartamento',$departamento_logueado)
        ->orwhere('tipo',$a)->pluck('id_bodega');
    
        $obtener_id_sector=SectorModel::whereIn('id_bodega',$obtener_idbodega_x_deparlogueado)->pluck('id_sector');
        $listaSeccion=SeccionModel::with('sector')->whereIn('id_sector',$obtener_id_sector)->get();

        return view('tramitesDepartamentales.gestionArchivo.gestionRegistroArchivo',[
            'listaSeccion' => $listaSeccion, 
            'listaTramite'=> $listaTramite, 
            'listaGestionArchivo'=> $listaGestionArchivo
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
     public function filtrar($inicio, $fin){

        //obtengo el id del departamento logueado
        $departamento_logueado=departamentoLogueado()['iddepartamento'];
        //consulta que obtiene los id de los tramites por dep logueado
        $obteneridtramite=TramiteModel::where('iddepartamento_temina',$departamento_logueado)->pluck('idtramite');
        $listaGestionArchivo=[];

        if(!is_null($obteneridtramite)){

            $listaGestionArchivo=GestionArchivoModel::with('seccion','tramitedoc')
                ->whereIn('idtramite',$obteneridtramite)
                ->whereBetween('fecha_gestion', [$inicio,$fin])
                ->orderBy('fecha_gestion', 'desc')
                ->get();
        
            return response()->json([
                    'error'=>false,
                    'resultado'=>$listaGestionArchivo
            ], 200);
       
        }

    }



     public function filtrarportexto($busqueda){

        //obtengo el id del departamento logueado
        $departamento_logueado=departamentoLogueado()['iddepartamento'];
        //consulta que obtiene los id de los tramites por dep logueado
        $obteneridtramite=TramiteModel::where('iddepartamento_temina',$departamento_logueado)->pluck('idtramite');
        //dd($obteneridtramite);
        $consultapordetalletramite=TramiteModel::where('asunto','LIKE','%'.$busqueda.'%')
            ->orwhere('observacion','LIKE','%'.$busqueda.'%')
            ->orwhere('codTramite','LIKE','%'.$busqueda.'%')
            ->whereIn('idtramite',$obteneridtramite)
            ->pluck('idtramite');

        $listaGestionArchivo=[];
        if(!is_null($obteneridtramite)){
            $listaGestionArchivo=GestionArchivoModel::with('seccion','tramitedoc')->whereIn('idtramite',$obteneridtramite)
            ->whereIn('idtramite', $consultapordetalletramite)->orderBy('fecha_gestion', 'desc')->get();
            
            return response()->json([
                    'error'=>false,
                    'resultado'=>$listaGestionArchivo
                ], 200);
      
        }

    }



     public function filtrarporlugar($lugar){

        //obtengo el id del departamento logueado
        $departamento_logueado=departamentoLogueado()['iddepartamento'];
        $obteneridtramite=TramiteModel::where('iddepartamento_temina',$departamento_logueado)->pluck('idtramite');
        $a='G';

        $obtener_idbodega_x_deparlogueado=BodegaModel::where('iddepartamento',$departamento_logueado)
            ->orwhere('tipo',$a)
            ->pluck('id_bodega');

        $obtener_id_sector=SectorModel::whereIn('id_bodega',$obtener_idbodega_x_deparlogueado)->pluck('id_sector');
        $listaSeccion=SeccionModel::with('sector')->where('id_sector',$lugar)->whereIn('id_sector',$obtener_id_sector)->pluck('id_sector');
    
        $listaGestionArchivo=[];
        if(!is_null($obteneridtramite)){

            $listaGestionArchivo=GestionArchivoModel::with('seccion','tramitedoc')->whereIn('idtramite',$obteneridtramite)
                ->whereIn('id_seccion', $listaSeccion)
                ->orderBy('id_gestion_archivo', 'desc')
                ->get();
        
            return response()->json([
                    'error'=>false,
                    'resultado'=>$listaGestionArchivo
            ], 200);
       
        }

    }

    


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

        $validator = Validator::make($request->all(), [
            'input_lugar_bod' => 'required',
            'carpeta' => 'required',
            'tramite' => 'required'
        
        ]);

        if ($validator->fails()){
            return back()->with([
                'mensajeInfo'=>"No se pudo realizar el registro, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
        }

        $bodega=$request->input_lugar_bod;
        $carpeta=$request->carpeta;
        $fecha_almacenamiento=Carbon::now()->format('Y-m-d');
   
        foreach ($request->tramite as $num  => $tramite) {
            $guardarGestion=new GestionArchivoModel();
            $guardarGestion->fecha_gestion=$fecha_almacenamiento;
            $guardarGestion->folder=$carpeta;
            $guardarGestion->idtramite=$tramite;
            $guardarGestion->id_seccion=$bodega;
            $guardarGestion->save();    
        }
        return redirect('archivo/listado')->with([
            'mensajeInfo'=>"Gestion de archivo guardado con exito",
            'mensajeColor'=>"success"                
        ]);

    }
        
    /**
     * Display the specified resource.
     *
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
           $idbodega = $id;
           $seccion = SectorModel::with('bodega')
                ->where('id_bodega',$idbodega)
                ->get();

            return response()->json([
                'error'=>false,
                'resultado'=>$seccion
            ], 200);

        } catch (\Throwable $th) {
            Log::error("Error get Request Id ".$th->getMessage());
            return response()->json([
                'error'=>true,
                'message'=>$th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        try {
            $idgestionarchivo = decrypt($id);

             $gestion_archivo=GestionArchivoModel::with('seccion','tramitedoc')
                ->where('id_gestion_archivo',$idgestionarchivo)
                ->first();

            return response()->json([
                'error'=>false,
                'resultado'=>$gestion_archivo
            ], 200);

        } catch (\Throwable $th) {
            Log::error("Error get Request Id ".$th->getMessage());
            return response()->json([
                'error'=>true,
                'message'=>$th->getMessage()
            ], 500);
        }
    
    


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, $id)
    {
        $id_gestion_archivo=decrypt($id);

        $validator = Validator::make($request->all(), [
            'carpeta' => 'required'        
        ]);

        if ($validator->fails()){
                return back()->with([
                'mensajeInfo'=>"No se pudo actualizar el registro, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
        }
    
        $bodega=$request->input_lugar_bod;
        $carpeta=$request->carpeta;
        $fecha_movimiento=Carbon::now()->format('Y-m-d');

        $guardarGestion=GestionArchivoModel::find($id_gestion_archivo);
        $guardarGestion->fecha_movimiento=$fecha_movimiento;
        $guardarGestion->folder=$carpeta;
        //$guardarGestion->idtramite=$tramite;
        $guardarGestion->id_seccion=$bodega;
        $guardarGestion->save();    
        
        return back()->with([
            'mensajeInfo'=>"Gestion de archivo actualizado con exito",
            'mensajeColor'=>"success"   
        ]);
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
  
            
        
    }
    
}
