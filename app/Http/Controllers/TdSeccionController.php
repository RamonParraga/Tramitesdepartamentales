<?php

namespace App\Http\Controllers;

use App\td_SectorModel as SectorModel;
use App\td_SeccionModel as SeccionModel;
use App\td_BodegaModel as BodegaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use Symfony\Component\Finder\Finder;


class TdSeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    //     $finder = new Finder ();
    //     $finder->files()->in (public_path('FormatoRequisitos'));

    // foreach ( $finder as $file ) {
    // $absoluteFilePath = $file -> getRealPath ();
    // $fileNameWithExtension = $file -> getRelativePathname ();
     
    // var_dump($fileNameWithExtension); 

    // // ...
    //     }
        
         //$archivo=scandir(public_path('js'));
        // print_r($archivo);
        //$dir=public_path('FormatoRequisitos');
//         if (is_dir($dir)){
//   if ($dh = opendir($dir)){
//     while (($file = readdir($dh)) !== false){
//       echo "filename:" . $file . "<br>";
//     }
//     closedir($dh);
//   }
// }
//         if (is_dir($dir)){
//   if ($dh = opendir($dir)){
//     while (($file = readdir($dh)) !== false){
//       echo "filename:" . $file . "<br>";
//     }
//     closedir($dh);
//   }
// }

        $listaSeccion = SeccionModel::with('sector')->get();
       
        //$listaBodega=SectorModel::with('bodega')->get();

        $listaBodega=BodegaModel::all();

        // dd($listaBodega);
            return view('tramitesDepartamentales.gestionSeccion.gestionSeccion',['listaSeccion' => $listaSeccion, 'listaBodega' => $listaBodega]);

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
            'idsector' => 'required'
        ]);

        if ($validator->fails())
            {
                return back()->with([
                'mensajeInfo'=>"No se pudo realizar el registro, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }
        
        $seccion=new SeccionModel();
        $seccion->descripcion=$request->get('descripcion');
        $seccion->id_sector=$request->get('idsector');

        $buscarSeccion = SeccionModel::where('descripcion', $seccion->descripcion)
            ->where('id_sector', $seccion->id_sector)
            ->first();
            //dd($buscarBodega);

            if(!is_null($buscarSeccion)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar información repetida",
                'mensajeColor'=>"error"
            ]);
            }

        if($seccion->save())
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
    public function show($id)
    {
        ;
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
        //
        //$sector = SectorModel::find(decrypt($id));
        // $id_sector=decrypt($id);
        // $sector = SectorModel::with('bodega')->where('id_sector',$id_sector);
        // return response()->json($sector);

        try {
            $idseccion = decrypt($id);
            $seccion = SeccionModel::with('sector')
                ->where('id_seccion',$idseccion)
                ->first();

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
            'idsector' => 'required'
        ]);

        if ($validator->fails())
            {
                return back()->with([
                'mensajeInfo'=>"No se pudo realizar el registro, complete todos los datos del formulario",
                'mensajeColor'=>"error"
            ]);
            }

        $idseccion = decrypt($id);
        $seccion=SeccionModel::find($idseccion);
        $seccion->descripcion=$request->get('descripcion');
        $seccion->id_sector=$request->get('idsector');

        $buscarSeccion = SeccionModel::where('descripcion', $seccion->descripcion)
            ->where('id_sector', $seccion->id_sector)
            ->first();
            //dd($buscarBodega);

            if(!is_null($buscarSeccion)){
            return back()->with([
                'mensajeInfo'=>"No se puede ingresar información repetida",
                'mensajeColor'=>"error"
            ]);
            }
            
        if($seccion->save())
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
  
            $seccion = SeccionModel::find(decrypt($id));
            //dd($sector);
            if($seccion->delete())
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
