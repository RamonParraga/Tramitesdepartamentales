<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\TipoUsuarioModel;
use App\Us001_tipoUsuarioModel;
use GuzzleHttp\Client;
use App\Http\Controllers\RestarurarContraseniaController;
use Mail;
use Log;

class RegistrarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    private $clientGad = null;

    public function __construct()
    {
        try {
            $this->clientGad = new Client([
                'base_uri' => env('URL_SERVICE_WSE'),
                'verify' => false,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }


    public function index()
    {
        return view('auth.register');
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

    public function getFuncionarioPublico($cedula){
        try {
            $datosFuncionario = $this->clientGad->request('GET', "api/datosuserF/$cedula",[
                'headers' => [
                    'Authorization' => env('URL_SERVICE_WSE_APIKEY')
                ] ,
                'connect_timeout' => 10,
                'timeout' => 10
            ]);
            $resultado=(array)json_decode($datosFuncionario->getBody()->getContents());
            return $resultado; 

        } catch (\Throwable $th) {
            return [];
        }

    }


    public function buscarFP(Request $request){
        //PIMERO VERIFICAMOS QUE NINGUN DATO TENGA CARACTERES ESPECIALES
        if(tieneCaracterEspecialRequest($request->request)){
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'No está permitido ingresar caracteres especiales en la información'
            ]); 
        }

        if($request->has('mensajePregunta')){ 
            // en el caso que exista esa variable quiere decir que
            // el usuario quiere registrarle otro tipo a usuario
             $idusuario = verificarCedulaRuc(decrypt($request->get('mensajeCedula')));
             $mensajeretorno=decrypt($request->get('mensajePregunta'));
             
             return $this->registrarleOtroTipo($idusuario, $mensajeretorno);
        }

        // validacion de la cedula o RUC y si ya esta registrado en la local
        $verificar=verificarCedulaRuc($request->get('cedula'));

        if($verificar=='V' ){ // si es correcta la cedula o ruc y no esta registrado en la bd local  
            
            $dataOld=$this->retornarDatosFuncionarioPublico($request->get('cedula'));
            
            if(sizeof($dataOld)==0){
                $dataOld=array('cedula'=>$request->get('cedula'));
                return back()->with([
                    'mensajeR'=>'danger',
                    'mensajeInfo'=>'Verifique que la informacion ingresada sea correcta',
                    'ErrorCedula'=>'La cédula no corresponde a un funcionario público', 
                    'dataOld'=>$dataOld
                ]);
            }
    
            return back()->with([
                'mensajeR'=>false,
                'FPencontrado'=>'true',
                'dataOld'=>$dataOld
            ]);

        }else if( $verificar=='I' ){ // si no es correcta la cedula o ruc y no esta registrado en la bd local
            $dataOld=array('cedula'=>$request->get('cedula'));
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'Verifique que la informacion ingresada sea correcta',
                'ErrorCedula'=>'La cédula o RUC no es valido', 
                'dataOld'=>$dataOld
            ]);
        }else{ // si la cedula o ruc ya estan registrados
            // EN CASO QUE EL USUAIRO EXISTA VERIFICAMOS LOS TIPOS QUE TIENE Y LO NOTIFICAMOS AL USUARIO   
            $idusuario = $verificar;
            $cedula= $request->get('cedula');
            $dataOld=array('cedula'=>$cedula);
            return $this->verificarTipoUsuarioExistente($idusuario, $cedula, $dataOld);
        }

        
    }

    public function retornarDatosFuncionarioPublico($cedula){

        try {
            // consultamos la api de los funcionario publicos
            $resultado = $this->getFuncionarioPublico($cedula);
       
            if(sizeof($resultado)==0){ // no existen en la base de dato oracle
                // no dejamos que el registro continue
                return [];
            }
            // si la cedula o RUC son correctas y el usuario esta en la base de datos oracle
            $dataOld= array();  // reiniciamos el dataOld
            $dataOld['name']= $resultado['nombre'];
            $dataOld['cedula']= $cedula;
            $dataOld['sexo']= $resultado['sexo'];
            $dataOld['direccion']= $resultado['direccion'];
            $dataOld['telefono']=$resultado['telefono'];
            $dataOld['celular']= "";
            $dataOld['email']= $resultado['email'];

            return $dataOld;  

        } catch (\Throwable $th) {
            return [];
        }


    }

    public function verificarTipoUsuarioExistente($idusuario, $cedula, $dataOld){

        $mensajeretorno='';
        $registrar=0;

        //VERIFICAMOS SI EL USUARIO TIENE UN TOKEN PENDIENTE O SI ESTA DADO DE BAJA -------------------------
            $usuario = User::find($idusuario);
            if(!is_null($usuario)){
                if(!is_null($usuario->email_token) || $usuario->estado=="E"){
                    // Retornar los datos del funcionario publico

                    $dataOld['name'] = $usuario->name;
                    $dataOld['cedula'] = $usuario->cedula;
                    $dataOld['sexo'] = $usuario->sexo;
                    $dataOld['direccion'] = $usuario->direccion;
                    $dataOld['telefono'] = $usuario->telefono;
                    $dataOld['celular'] = $usuario->celular;
                    $dataOld['email'] = $usuario->email;
                    
                    return back()->with([
                        'mensajeR'=>false,
                        'FPencontrado'=>'true',
                        'dataOld'=>$dataOld
                    ]);

                }
            }
        // ----------------------------------------------------------------------------

        if(thisUserEsTipo('FP',$idusuario)==false && thisUserEsTipo('C',$idusuario)==false){
            // preguntamos si creamos pero ambos
            $mensajeretorno="¿Desea registrar el usuario como Funcionario Público y Contribuyente..?";
            $registrar=0;
        }else if(thisUserEsTipo('FP',$idusuario)==false){
            // pregutamos si creamos funcionario publico
            $mensajeretorno="El usuario está registrado como Contribuyente. ¿Desea registrarlo también como un usuario de tipo Funcionario Público..?";
            $registrar=1;
        }else if(thisUserEsTipo('C',$idusuario)==false){
            // pregutamos si creamos contribuyente
            $mensajeretorno="El usuario está registrado como Funcionario Público. ¿Desea registrarlo también como un usuario de tipo Contribuyente..?";
            $registrar=2;
        }else{ // si el usuario ya tiene ambos tipos registrados
            return back()->with([
                'mensajeR'=>'info',
                'mensajeInfo'=>'El usuario ya está registrado como Funcionario Público y Contribuyente.',
            ]);
        }

        return back()->with([
            'mensajeR'=>'info',
            'mensajePregunta'=>encrypt($registrar),
            'mensajeCedula'=>encrypt($cedula),
            'mensajeInfo'=>$mensajeretorno,
            'dataOld'=>$dataOld
        ]);
    }

    public function registrarleOtroTipo($idusuario, $mensajeretorno){
        if($mensajeretorno==0){ // crear FP y C
            $idtipoFP=TipoUsuarioModel::where('tipo','FP')->first()->idtipoUsuario;
            $idtipoC=TipoUsuarioModel::where('tipo','C')->first()->idtipoUsuario;
    
            $objUs001tipoUsuario=new Us001_tipoUsuarioModel();
            $objUs001tipoUsuario->idus001=$idusuario;
            $objUs001tipoUsuario->idtipoUsuario=$idtipoFP; // agregamos al usurio el tipo Funcionario publico
            $objUs001tipoUsuario->save();

            $objUs001tipoUsuario=new Us001_tipoUsuarioModel();
            $objUs001tipoUsuario->idus001=$idusuario;
            $objUs001tipoUsuario->idtipoUsuario=$idtipoC; // agregamos al usurio el tipo Contribuyente
            $objUs001tipoUsuario->save();
        }else if($mensajeretorno==1){ // crear solo FP
            $idtipoFP=TipoUsuarioModel::where('tipo','FP')->first()->idtipoUsuario;
            $objUs001tipoUsuario=new Us001_tipoUsuarioModel();
            $objUs001tipoUsuario->idus001=$idusuario;
            $objUs001tipoUsuario->idtipoUsuario=$idtipoFP; // agregamos al usurio el tipo Funcionario publico
            $objUs001tipoUsuario->save();
        }else{ // crear solo C
            $idtipoC=TipoUsuarioModel::where('tipo','C')->first()->idtipoUsuario;
            $objUs001tipoUsuario=new Us001_tipoUsuarioModel();
            $objUs001tipoUsuario->idus001=$idusuario;
            $objUs001tipoUsuario->idtipoUsuario=$idtipoC; // agregamos al usurio el tipo Funcionario publico
            $objUs001tipoUsuario->save();
        }

        return back()->with([
            'mensajeR'=>'success',
            'mensajeInfo'=>'Nuevo rol del usuario creado correctamente',
        ]);
    }

    public function store(Request $request)
    {

        // no validamos caracteres especiales en estos campos
        // guardamos en un variable los datos que vienen en el request
        $dataOld= array();  // reiniciamos el dataOld
        $dataOld['cedencrypt']= $request->get('cedencrypt');
        $dataOld['name']= $request->get('name');
        $dataOld['telefono']=$request->get('telefono');
        $dataOld['cedula']= $request->get('cedula');
        $dataOld['sexo']= $request->get('sexo');
        $dataOld['celular']= $request->get('celular');
        $dataOld['email']= $request->get('email');
        $dataOld['direccion']= $request->get('direccion');

        // solo los datos que vamos a validar
        $validaCP=array(
            'email'=>$request->get('email'),
            'celular'=>$request->get('celular')
        );

        // desencriptamos la cedula
        $cedula_d=decrypt($request->get('cedencrypt')); 
        //PIMERO VERIFICAMOS QUE NINGUN DATO TENGA CARACTERES ESPECIALES
        if(tieneCaracterEspecialRequest($validaCP) || tieneCaracterEspecial($cedula_d)){
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'No está permitido ingresar caracteres especiales en la información',
                'dataOld'=>$dataOld,
                'FPencontrado'=>'true',
            ]); 
        }

                $user = User::where('cedula','=',$cedula_d)->first(); //obtenemos el usuario
                $regla_email = 'required|string|email|max:255|unique:us001'; // regla por defecto

                if(!is_null($user)){ // si se esta actualizando un usuario
                    if($user->email == $request->email){
                        $regla_email = 'required|string|email|max:255'; // regla para que permita dejar el mismo correo ya que se esta actualizando
                    }
                }

                //VALIDAR QUE LOS DATOS NO SEAN NULOS
                //SE ESTABLECE UNA REGLA CON LA POSICION DEL ARREGLO
                $reglas = [
                    'email' => $regla_email,
                    'celular' => 'required|max:10'
                ];
        
                $validar=Validator::make($validaCP, $reglas)->validate();// SE REALIZA LA VALIDACION PASANDO EL ARREGLO Y LAS REGLAS

        // verificamos si se esta realizando una actualización de un usuario confirmado (con token activo)
        if(!is_null($user)){
            $user->email=$request->email;
            $user->celular=$request->get('celular');
            $user->estado=null;
            $user->password=bcrypt(str_random(8)); // damos una clave aleatoria
            $user->save(); // actualizamos el correo y el celular
            // enviamos el mensaje de restauraciçon al nuevo correo electronico
            $obj_restaurarCont = new RestarurarContraseniaController(); // instanciamos el un objeto de controlador de RestarurarContraseniaController
            $obj_restaurarCont->enviarCorreoConfirmar($user->email); // enviar correo

            return back()->with([
                'mensajeR'=>'success',
                'mensajeInfo'=>'Usuario ingresado. Para completar el registro se envió un mensaje de confirmación al correo electrónico del usuario.'
            ]);   
        }

        $usuarioRegistrar=null; // inicializamos la variable para el usuario que se va a registrar
        // validamos la cedula en el caso que la cedula sea modificada
        
        // validacion de la cedula o RUC y si ya esta registrado en la local
        $verificar=verificarCedulaRuc($cedula_d);
    
        if($verificar=='V' ){ // si es correcta la cedula o ruc y no esta registrado en la bd local
            //validamos que la cedula cuente en los registro de la base de datos oracle
            
            $resultado = $this->getFuncionarioPublico($cedula_d);
            
            // $client = new Client(['base_uri' => getHostServe(1)]);
            // $resultado = $client->request('GET', getHostServe(1)."/datosuserF/".$request->get('cedula'));
            // $resultado=(array)json_decode($resultado->getBody()->getContents());
            
            if(sizeof($resultado)==0){ // no existen en la base de dato oracle
                // no dejamos que el registro continue
                return back()->with([
                    'mensajeR'=>'danger',
                    'mensajeInfo'=>'La información no pertenece a un funcionario público.'
                ]);
            }else{ // si la cedula o RUC son correctas y el usuario esta en la base de datos oracle
                $usuarioRegistrar=$resultado; // obtenemos el usuario encontrado en la bd oracle
                goto CONTINUAREGISTRO;
            }

        }else if( $verificar=='I' ){ // si no es correcta la cedula o ruc y no esta registrado en la bd local
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'Verifique que la informacion ingresada sea correcta'
            ]);
        }else{ // si la cedula o ruc ya estan registrados en la bd local
            // EN CASO QUE EL USUAIRO EXISTA VERIFICAMOS LOS TIPOS QUE TIENE Y LO NOTIFICAMOS AL USUARIO   
            $idusuario = $verificar;
            $dataOld=array('cedula'=>$cedula_d);
            return $this->verificarTipoUsuarioExistente($idusuario, $cedula_d, $dataOld);
        }

        CONTINUAREGISTRO:
        // si pasa la condicion el usuario esta en la base de datos oracle y se puede registrar

        //procedemos a registrarlo en la base de datos local

        $user=new User();
        if(substr($cedula_d,-3)=='001'){
            $user->tipo="R"; // es un ruc
        }else if(validarCedula($cedula_d)){
            $user->tipo="C"; // es una cedula
        }else{
            $user->tipo="P"; // es un pasaporte
        }
            $user->ciu=$usuarioRegistrar['ciu'];
            $user->name=$usuarioRegistrar['nombre'];
            $user->cedula=$usuarioRegistrar['cedula'];
            $user->sexo=$usuarioRegistrar['sexo'];
            $user->direccion=$usuarioRegistrar['direccion'];
            $user->telefono=$usuarioRegistrar['telefono'];
            $user->email=$request->email;
            $user->celular=$request->get('celular');
            $user->password=bcrypt(str_random(8)); // damos una clave aleatoria
        
        if($user->save()){
            // CREAMOS EL USUARIO CON SUS DOS TIPOS QUE SON FUNCIONARION PUBLICO Y CONTRIBUYENTE
            // como e un usuario nuevo obtenemos los id de los tipos de usuarios FP y C
            $idtipoFP=TipoUsuarioModel::where('tipo','FP')->first()->idtipoUsuario;
            $idtipoC=TipoUsuarioModel::where('tipo','C')->first()->idtipoUsuario;

            $objUs001tipoUsuario=new Us001_tipoUsuarioModel();
            $objUs001tipoUsuario->idus001=$user->idus001;
            $objUs001tipoUsuario->idtipoUsuario=$idtipoFP; // agregamos al usurio el tipo Funcionario publico
            $objUs001tipoUsuario->save();

            $objUs001tipoUsuario=new Us001_tipoUsuarioModel();
            $objUs001tipoUsuario->idus001=$user->idus001;
            $objUs001tipoUsuario->idtipoUsuario=$idtipoC; // agregamos al usurio el tipo Contribuyente
            $objUs001tipoUsuario->save();

            // ENVIAMOS EL CORREO PARA CAMBIAR LA CONTRASEÑA
            $obj_restaurarCont = new RestarurarContraseniaController();
            $obj_restaurarCont->enviarCorreoConfirmar($user->email);
            
            return back()->with([
                'mensajeR'=>'success',
                'mensajeInfo'=>'Usuario ingresado. Para completar el registro se envió un mensaje de confirmación al correo electrónico del usuario.'
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
    public function destroy($idus001){

        try{
            
            $idus001 = decrypt($idus001);
            $usuario = User::where('idus001', $idus001)->first();
            if(is_null($usuario)){ // usuario no encontrado en la base de datos 
                return back()->with(['mensajePInfoAsignarTipoFP'=>'Usuario no encontrado','estadoP'=>'default']);
            }

            $usuario->estado = "E";
            $usuario->save();
            return back()->with(['mensajePInfoAsignarTipoFP'=>'El usuario fue dado de baja con éxito','estadoP'=>'success']);         
            
        }catch(\Throwable $th){
            return back()->with(['mensajePInfoAsignarTipoFP'=>'Se produjo un error al intentar dar de baja el usuario','estadoP'=>'error']);      
        }
    }
}
