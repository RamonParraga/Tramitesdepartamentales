<?php

namespace App\Http\Controllers;

use App\td_BodegaModel as BodegaModel;
use App\td_SectorModel as SectorModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
//use Symfony\Component\Finder\Finder;

class TdSectorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaSector = SectorModel::with('bodega')->get();
        //dd($listaBodega);
        //$listaBodega=BodegaModel::all();
         $listaBodega=BodegaModel::all();
        return view('tramitesDepartamentales.gestionSector.gestionSector',['listaSector' => $listaSector, 'listaBodega' => $listaBodega]);

        //return view('tramitesDepartamentales.gestionSector.gestionSector');
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
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required',
            'idbodega' => 'required'
        ]);

        if ($validator->fails())
            {
                return back()->with([
                'mensajeInfo'=>"No se pudo realizar el registro, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }
        
        $sector=new SectorModel();
        $sector->descripcion=$request->get('descripcion');
        $sector->id_bodega=$request->get('idbodega');

        $buscarSector = SectorModel::where('descripcion', $sector->descripcion)
            ->where('id_bodega', $sector->id_bodega)
            ->first();
            //dd($buscarBodega);

            if(!is_null($buscarSector)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar información repetida",
                'mensajeColor'=>"error"
            ]);
            }

        if($sector->save())
        {
            return back()->with([
                    'mensajeInfo'=>"Tipo de tramite guardado con exito",
                    'mensajeColor'=>"success"
                
                ]);

        }
        else
        {
            return back()->with([
                    'mensajeInfo'=>"No se pudo guardar la información",
                    'mensajeColor'=>"error"
                
                ]);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function show(td_TipoDocumentoModel $td_TipoDocumentoModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        //$sector = SectorModel::find(decrypt($id));
        // $id_sector=decrypt($id);
        // $sector = SectorModel::with('bodega')->where('id_sector',$id_sector);
        // return response()->json($sector);

        try {
            $idsector = decrypt($id);
            $sector = SectorModel::with('bodega')
                ->where('id_sector',$idsector)
                ->first();

            return response()->json([
                'error'=>false,
                'resultado'=>$sector
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
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required',
            'idbodega' => 'required'
        ]);

        if ($validator->fails())
            {
                return back()->with([
                'mensajeInfo'=>"No se pudo realizar el registro, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }

        $idsector = decrypt($id);
        $sector=SectorModel::find($idsector);
        $sector->descripcion=$request->get('descripcion');
        $sector->id_bodega=$request->get('idbodega');

        $buscarSector = SectorModel::where('descripcion', $sector->descripcion)
            ->where('id_bodega', $sector->id_bodega)
            ->first();
            //dd($buscarBodega);

            if(!is_null($buscarSector)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar información repetida",
                'mensajeColor'=>"error"
            ]);
            }
            
        if($sector->save())
        {
            return back()->with([
                    'mensajeInfo'=>"Tipo de tramite actualizadp con exito",
                    'mensajeColor'=>"success"
                
                ]);

        }
        else
        {
            return back()->with([
                    'mensajeInfo'=>"No se pudo actualizar la información",
                    'mensajeColor'=>"error"
                
                ]);

        }

             

       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\td_TipoDocumentoModel  $td_TipoDocumentoModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
  
            $sector = SectorModel::find(decrypt($id));
            //dd($sector);
            if($sector->delete())
            {
            return back()->with([
                'mensajeInfo'=>"La información fue eliminada con éxito",
                'mensajeColor'=>"success"
            ]);
            }
            else{
                 return back()->with([
                'mensajeInfo'=>"No se pudo eliminar la información",
                'mensajeColor'=>"danger"
            ]);
            }
        
    }
    
}
