<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\TipoUsuarioModel;
use App\Us001_tipoUsuarioModel;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class RegistrarExtranjeroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('gestionCuentas.registrarExtranjero');
    }

    public function verificarRegistroLocalOracleCIU($ciu){
        // verifica si el ciu ya esta registrado en la base de datos local
        // verifica si el ciu existe en la base de datos oracle
        // retornamos L si esta ya está registrado en la local (ya no es necesario registrar)
        // retornamos N si no existe el CIU en la bd oracle (no se permite realizar el registro)
        // retornamos (datos usuario base de datos oracle) (se puede hacer un nuevo registro)

        $retornar = 'N'; // asumimos por defecto que no esta registrado  en la local ni en la oracle
        if($ciu==""){return $retornar;}

        // verificamos el contribuyente en la base de datos oracle
        $client = new Client(['base_uri' => getHostServe(1)]);
        $resultado = $client->request('GET', getHostServe(1)."/datosuserPorCIU/".$ciu);
        $resultado=(array)json_decode($resultado->getBody()->getContents());
        
        if(sizeof($resultado)==0){ // si el resultado es cero
            $retornar="N"; // el ciu no está registrado en la bd oracle
        }else{
            // si no es cero quiere decir que esta registrado en la base de datos oracle
            // entonces validamos que no este registado el CIU y la CEDULA en la base de datos local
            $existeLocal=User::where('ciu','=',$ciu)->orWhere('cedula','=',$resultado['cedula'])->first();
            if(!is_null($existeLocal)){
                $retornar="L"; // ya está registado en la local
            }else{
                // por lo tanto retornamos los datos de contribuyente desde la bd oracle
                $retornar=$resultado;                    
            }

        }
 
        // retornamos sea cual sea el resultado de las val
        return $retornar;
    }

    public function buscarCIU(Request $request){

        //PIMERO VERIFICAMOS QUE NINGUN DATO TENGA CARACTERES ESPECIALES
        if(tieneCaracterEspecialRequest($request->request)){
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'No está permitido ingresar caracteres especiales en la información'
            ]); 
        }
        
        $ciu_d = ($request->get('ciu'));

        $verificarCiu=$this->verificarRegistroLocalOracleCIU($ciu_d);
        if($verificarCiu=="L"){ // ya esta registrado en la local
            return back()->with([
                'mensajeR'=>'info',
                'mensajeInfo'=>'El contribuyente ya fué registrado anteriormente'
            ]);
        }else if($verificarCiu=="N"){ // no esta registrado ni en la bd local ni en la bd oracle
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'No es posible registrar el contribuyente',
                'errorciu'=>'El código ciu está incorrecto'
            ]); 
        }else{ // vienen los datos del contribuyente desde la bd oracle
            // si la cedula o RUC son correctas y el usuario esta en la base de datos oracle
            $dataOld= array();  // reiniciamos el dataOld
            $dataOld['ciu']= $verificarCiu['ciu'];
            $dataOld['name']= $verificarCiu['nombre'];
            $dataOld['cedula']= $verificarCiu['cedula'];
            $dataOld['sexo']= $verificarCiu['sexo'];
            $dataOld['direccion']= $verificarCiu['direccion'];
            $dataOld['telefono']= $verificarCiu['telefono'];
            $dataOld['email']= $verificarCiu['email'];
            $dataOld['celular']="";
            return back()->with([
                'mensajeR'=>false,
                'C_encontrado'=>true,
                'dataOld'=>$dataOld
            ]);
        }
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
        $dataOld = array();
        $dataOld['celular']= $request->get('celular');

        // solo los datos que vamos a validar
        $validaCE=array(
            'password'=>$request->get('password'),
            'celular'=>$request->get('celular'),
            'password_confirmation'=>$request->get('password_confirmation')
        );
        
        //PIMERO VERIFICAMOS QUE NINGUN DATO TENGA CARACTERES ESPECIALES
        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'No está permitido ingresar caracteres especiales en la información',
                'dataOld'=>$dataOld,
            ]);
        }

        // obtenemos el ciu que esta encriptado
        $ciu_d = decrypt($request->get('ciuncrypt'));

        $verificarCiu=$this->verificarRegistroLocalOracleCIU($ciu_d);
        if($verificarCiu=="L"){ // ya esta registrado en la local
            return back()->with([
                'mensajeR'=>'info',
                'mensajeInfo'=>'El contribuyente ya fue registrado anteriormente'
            ]);
        }else if($verificarCiu=="N"){ // no esta registrado ni en la bd local ni en la bd oracle
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'No es posible registrar el contribuyente',
                'errorciu'=>'El código ciu está incorrecto'
            ]); 
        }



        // primero verificamos la cedula


        REGISTRARNUEVOCONTRIBUYENTE:
        //  REGISTRAMOS EL NUEVO CONTRIBIYENTE
        // guardamos en un avariable los datos que vienen en el request
        $dataOld= array();  // reiniciamos el dataOld
        $dataOld['ciuncrypt']=$request->get('ciuncrypt');
        $dataOld['ciu']=$request->get('ciu');
        $dataOld['name']= $request->get('name');
        $dataOld['cedula']= $request->get('cedula');
        $dataOld['sexo']= $request->get('sexo');
        $dataOld['direccion']= $request->get('direccion');
        $dataOld['telefono']= $request->get('telefono');
        $dataOld['email']= $request->get('email');
        $dataOld['celular']=$request->get('celular');


        // si pasa la condicion el usuario esta en la base de datos oracle
        // y procedemos a registrarlo en la base de datos local

        // $data=array();
        // // construimos la estructura de los datos para validar el resto de los datos
        // // en el caso que el usuario modifique los datos precargados
        // foreach ($request->request as $key => $value) {
        //     $data[$key]=$value;
        // }

        // Validator::make($data, [
        //     'ciu' => 'required',
        //     'name' => 'required|string|max:255',
        //     'cedula' => 'required|string|max:13|unique:us001',
        //     'sexo' => 'required|string|max:10',
        //     'direccion' => 'required|string|max:500',
        //     'celular' => 'required|string|min:9|max:10',
        //     'email' => 'required|string|email|max:255|unique:us001',
        //     'password' => 'required|string|min:8|confirmed',
        // ])->validate();


        // validamos que la clave sea segura
        if(!validarClave($request->get('password'))){
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'Verifique que la informacion ingresada sea la correcta',
                'ClaveInsegura'=>'La contraseña debe tener al menos 8 caracteres, incluir letras mayúsculas, minúsculas y numeros.', 
                'dataOld'=>$dataOld,
                'C_encontrado'=>'true',
            ]); 
        }

        
        Validator::make($validaCE, [
            'celular' => 'required|string|min:9|max:10',
            'password' => 'required|string|min:8|confirmed',
        ])->validate();

        $user=new User();
        $user->ciu=$verificarCiu['ciu'];
        $user->name=$verificarCiu['nombre'];
        $user->cedula=$verificarCiu['cedula'];
        $user->sexo=$verificarCiu['sexo'];
        $user->direccion=$verificarCiu['direccion'];
        $user->email=$verificarCiu['email'];
        $user->celular=$request->get('celular');
        $user->password=bcrypt($request->get('password'));

        if($user->save()){
            // CREAMOS EL USUARIO CON SUS DOS TIPOS QUE SON FUNCIONARION PUBLICO Y CONTRIBUYENTE
            $idtipoC=TipoUsuarioModel::where('tipo','C')->first()->idtipoUsuario;

            $objUs001tipoUsuario=new Us001_tipoUsuarioModel();
            $objUs001tipoUsuario->idus001=$user->idus001;
            $objUs001tipoUsuario->idtipoUsuario=$idtipoC; // agregamos al usurio el tipo Funcionario publico
            $objUs001tipoUsuario->save();
            
            return back()->with([
                'mensajeR'=>'success',
                'mensajeInfo'=>'El usuario se registró de forma exitosa'
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
        //
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
        //
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
