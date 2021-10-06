<?php

namespace App\Http\Controllers;

use App\td_BodegaModel as BodegaModel;
use App\DepartamentoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use Symfony\Component\Finder\Finder;

class TdBodegaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
//        $finder = new Finder();
        
    
// $finder = new Finder();
// $finder->files()->in(__DIR__);


// dd($finder);


        $listaBodega = BodegaModel::with('departamento')->get();
        //dd($listaBodega);
        $listaArea=DepartamentoModel::all();
        return view('tramitesDepartamentales.gestionBodega.gestionBodega',['listaBodega' => $listaBodega, 'listaArea' => $listaArea]);
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
            
            'nombre' => 'required',
            'ubicacion' => 'required'
            
            ]);

            if ($validator->fails())
            {
            // return back()->with(['mensajePInfoSolicitud'=>'No se pudo realizar el registro, complete todos los datos del formulario','estadoP'=>'danger']);
                return back()->with([
                'mensajeInfo'=>"No se pudo realizar el registro, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }

        $es_general=$request->check_general;
       // dd($es_general);
        if(!is_null($es_general)){

        if($es_general=='General'){

            $area='47'; 

            $Bodega= new BodegaModel();
            $Bodega->nombre=$request->nombre;
            $Bodega->ubicacion=$request->ubicacion;
            $Bodega->tipo='G';
            $Bodega->iddepartamento=$area;

         $buscarBodega = BodegaModel::where('nombre', $Bodega->nombre)
            ->where('ubicacion', $Bodega->ubicacion)
            ->where('tipo', $Bodega->tipo)
            ->first();
            //dd($buscarBodega);

            if(!is_null($buscarBodega)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar información repetida",
                'mensajeColor'=>"error"
            ]);
            }
            
            if($Bodega->save()){
            return back()->with([
                'mensajeInfo'=>"Registro de bodega guardada con exito",
                'mensajeColor'=>"success"
            ]);
            }
            else{
                return back()->with([
                'mensajeInfo'=>"No se pudo guardar la bodega",
                'mensajeColor'=>"error"
            ]);
            }
        }
    }
        $es_area=$request->check_area;
        //dd($es_area);
            if(!is_null($es_area)){

            if($es_area=='Area'){
            
            $validator = Validator::make($request->all(), [
            'cmb_area' => 'required'            
            ]);
            if ($validator->fails())
            {
               return back()->with([
                'mensajeInfo'=>"No se pudo realizar el registro, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }


            $Bodega =new BodegaModel();
            $Bodega->nombre=$request->nombre;
            $Bodega->ubicacion=$request->ubicacion;
            $Bodega->tipo='A';
            $Bodega->iddepartamento=$request->cmb_area;

            $buscarBodega = BodegaModel::where('nombre', $Bodega->nombre)
            ->where('ubicacion', $Bodega->ubicacion)
            ->where('tipo', $Bodega->tipo)
            ->first();

            if(!is_null($buscarBodega)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar información repetida",
                'mensajeColor'=>"error"
            ]);
            }

         
           if($Bodega->save()){
            return back()->with([
                'mensajeInfo'=>"Registro de bodega realizado con exito",
                'mensajeColor'=>"success"
            ]);
            }
            else{
                return back()->with([
                'mensajeInfo'=>"No se pudo guardar la bodega",
                'mensajeColor'=>"error"
            ]);
            }


            }

        }

          return back()->with([
                'mensajeInfo'=>"No se pudo guardar la bodega, complete todos los campos obligatorios",
                'mensajeColor'=>"error"
            ]);


        // } catch (\Throwable $th){
        //     return back()->with([
        //         'mensajeInfo'=>"No se pudo guardar la bodega",
        //         'mensajeColor'=>"error"
        //     ]);
        // }
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
        $bodega = BodegaModel::find(decrypt($id));
        return response()->json($bodega);


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
        //
             $validator = Validator::make($request->all(), [
            
            'nombre' => 'required',
            'ubicacion' => 'required'
            
            ]);

            if ($validator->fails())
            {
            // return back()->with(['mensajePInfoSolicitud'=>'No se pudo realizar el registro, complete todos los datos del formulario','estadoP'=>'danger']);
                return back()->with([
                'mensajeInfo'=>"No se pudo realizar la actualización, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }
            
            $es_general=$request->check_general;
       // dd($es_general);
            if(!is_null($es_general)){

            if($es_general=='General'){

            $area='47';
            $Bodega = BodegaModel::find(decrypt($id));
            $Bodega->nombre=$request->nombre;
            $Bodega->ubicacion=$request->ubicacion;
            $Bodega->tipo='G';
            $Bodega->iddepartamento=$area;

            $buscarBodega = BodegaModel::where('nombre', $Bodega->nombre)
            ->where('ubicacion', $Bodega->ubicacion)
            ->where('tipo', $Bodega->tipo)
            ->first();
            //dd($buscarBodega);

            if(!is_null($buscarBodega)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar información repetida",
                'mensajeColor'=>"error"
            ]);
            }

                                               
           if($Bodega->save()){
            return back()->with([
                'mensajeInfo'=>"Registro de bodega actualizada con exito",
                'mensajeColor'=>"success"
            ]);
            }
            else{
                return back()->with([
                'mensajeInfo'=>"No se pudo guardar la bodega",
                'mensajeColor'=>"error"
            ]);
            }
        }
    }
             $es_area=$request->check_area;
        //dd($es_area);
            if(!is_null($es_area)){

            if($es_area=='Area'){

            $validator = Validator::make($request->all(), [
            'cmb_area' => 'required'            
            ]);
            if ($validator->fails())
            {
               return back()->with([
                'mensajeInfo'=>"No se pudo realizar la actualización, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }

            $Bodega = BodegaModel::find(decrypt($id));
            $Bodega->nombre=$request->nombre;
            $Bodega->ubicacion=$request->ubicacion;
            $Bodega->tipo='A';
            $Bodega->iddepartamento=$request->cmb_area;

            $buscarBodega = BodegaModel::where('nombre', $Bodega->nombre)
            ->where('ubicacion', $Bodega->ubicacion)
            ->where('tipo', $Bodega->tipo)
            ->first();
            //dd($buscarBodega);

            if(!is_null($buscarBodega)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar información repetida",
                'mensajeColor'=>"error"
            ]);
            }
           if($Bodega->save()){
            return back()->with([
                'mensajeInfo'=>"Registro de bodega actualizada con exito",
                'mensajeColor'=>"success"
            ]);
            }
            else{
                return back()->with([
                'mensajeInfo'=>"No se pudo guardar la bodega",
                'mensajeColor'=>"error"
            ]);
            }


            }
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
  
            try{
            $bodega = BodegaModel::find(decrypt($id));
            //dd($prioridadtramite);
            if($bodega->delete())
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
        }catch(\Throwable $th) {
            //Log::error("Error get Request Id ".$th->getMessage());
            return back()->with([
                'mensajeInfo'=>"No se pudo eliminar la información",
                'mensajeColor'=>"danger"
            ]);
        }
    }
    
}
