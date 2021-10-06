<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Usuario_TipoUsuario;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use GuzzleHttp\Client;
use Redirect;
use Illuminate\Support\Facades\Crypt;
use Hash;
use PDF;
use Illuminate\Auth\Events\Registered;
use App\Jobs\SendVerificationEmail;
use App\DepartamentoModel;
use App\td_us001_tipofpModel;
use App\TipoFPModel;

class Regis_UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      use RegistersUsers;


    public function index()
    {
        return view('auth.buscar_Per');
    }

    //PARA CONSULTAR EN LA BASE DE DATOS ORACLE SI EXISTE EL USUARIO Y EXTRAER LOS DATOS
    public function ConsultaDatosGen($cedula){
        
        $client = new Client([
          'base_uri' => 'http://localhost/appServices/public',
        ]);
        // $client = new Client([
        //   'base_uri' => 'http://servicesora.chone.gob.ec',
        // ]);
        $response = $client->request('GET', "datosuser/{$cedula}");
        return json_decode((string) $response->getBody(), true);
    }


    // public function validarUsuario($cedula){
    //     $cedula = substr($cedula, 0,10);
    //     $usuario =User::where("cedula","like",'%'.$cedula.'%')->first();


    //     if(is_null($usuario)){
    //         $resultado=$this->verificarcedulaRuc($cedula);
    //         return $resultado;
    //     }else{
    //         $resultado="existe";
    //         return $resultado;  //DEVUELVE EL USUARIO EN CASO DE EXISTIS CASO CONTRARIO DEVUELVE NULO
    //     }
       

    // }

    //COMPROBAR SI EXISTE EL USUARIO EN LA BASE DE DATOS LOCAL
    public function existeUsuario($cedula){
            $cedulaSNR = substr($cedula, 0,10);
            $usuario =User::where("cedula","like",'%'.$cedulaSNR.'%')->first();
            return $usuario;

    }

    //VERIFICAR SI LA CEDULA O RUC ES VALIDA
    public function verificarcedulaRuc($cedula){
            // si no esta registrado procedemos a validar la cedula y el RUC
            // I:invalido
            // V:valido
            $estadoValidado='I';
            if(validarCedula($cedula)){
                $estadoValidado='V';
            }

            if(validarRucPersonaNatural($cedula)){
                $estadoValidado='V';
            }

            if(validarRucSociedadPrivada($cedula)){
                $estadoValidado='V';
            }

            if(validarRucSociedadPublica($cedula)){
                $estadoValidado='V';
            }
            
            return $estadoValidado;

    }

    public function validarsociedadconRuc($cedula){
            // I:invalido
            // V:valido
            $estadoValidado='I';
           if(validarRucSociedadPrivada($cedula)){
                $estadoValidado='V';
            }

            if(validarRucSociedadPublica($cedula)){
                $estadoValidado='V';
            }
              return $estadoValidado;

    }


    public function validarpernatural($cedula){
            // I:invalido
            // V:valido
            $estadoValidado='I';
            if(validarCedula($cedula)){
                $estadoValidado='V';
            }

            if(validarRucPersonaNatural($cedula)){
                $estadoValidado='V';
            }
              return $estadoValidado;
    }


    function tieneCaracterEspecialRequest($request,$novalidar){
        $retorno=false; // por defecto asumimos queno tiene caracteres especiales
        foreach ($request->request as $key => $parametro) {
            if($key=='_token' || $key==$novalidar):continue;endif; // para no validar el token de laravel
            $resultado=preg_match('/[$%&|<>#&=()]/',$parametro);
            if($resultado==1):return $retorno=true;endif; // si es 1 es porque se han encontrado CE
        }
        return $retorno;
    }


    //FUNCION QUE SE EJECUTA AL BUSCAR EL USUARIO CON LA CEDULA PARA CERIFICAR SI SE ENCUENTRA REGISTRADO
    public function IngresoRegistro(request $request)
    {
        if($this->tieneCaracterEspecialRequest($request,'')==false){
            if ($this->verificarcedulaRuc($request['cedulaB'])=='V') { //LLAMADA A LA FUNCION PARA VALIDAR CEDULA
                $usuario=$this->existeUsuario($request['cedulaB']);    // LLAMADA A LA FUNCION PARA VER SI EXISTE EL USUARIO
                if($usuario!=null){
                    return back()->with(['mensajemodal'=>"1"]); // EN CASO DE NO EXISTER RETORNAMOS A LA VISTA CON UN MENSAJE
                }
                $resultado= $this->ConsultaDatosGen($request['cedulaB']); // SI EL USUARIO NO EXISTE EN LA BD LOCAL SE BUSCA EN LA BD ORACLE PARA EXTRAER LOS DATOS
            
                if($resultado!=null){  // SI EXISTE SE EXTRAE EL CIU Y SE LO PASA A LA VISTA DE REGISTRO ECNCRYPTADO
                    $ciu= Crypt::encrypt($resultado['ciu']);
                    //dd("holo meco");
                    return view('auth.register')->with(["datoUser"=>$resultado, 'cedActual'=>$request['cedulaB'],'ciu'=>$ciu, 'estadomuestra'=>1]);
                }else{
                   //     dd("holo meitao");
                    return view('auth.register')->with(['cedActual'=>$request['cedulaB'],'estadomuestra'=>1]); // SI NO EXISTE SIMPLEMENTE SE CONTINUA EL REGISTRO CON UN USUARIO NUEVO 

                }
            }else{ // SI LA CEDULA ES INVALIDA RETORNAMOS UN MENSAJE 
                    return redirect('registro')->with([
                        'mensajeR'=>'danger',
                        'mensajeInfo'=>'verifique que la cédula sea la correcta',
                        'ErrorCedula'=>'La cédula o RUC no es válido,', 
                    ]);

            }
        }else{
            return back()->with(['mensajeR'=>'danger',
                        'mensajeInfo'=>'verifique que la cédula sea la correcta',
                        'ErrorCedula'=>'La cédula o RUC no es válido,', ]); // EN CASO DE NO EXISTER RETORNAMOS A LA VISTA CON UN MENSAJE
        }
    }

    //VALIDAR EL CAMPO CELULAR QUE SOLO ADMITA COMOMINIMO 9 CARACTERES Y COMO MÁXIMO 10
    public function validarcelular($celular){  
        $data=array();   //CREACION DE UN ARREGLO
        $data[0]=$celular; //SE ASIGNA EL VALOR DEL CAMPO AL ARREGLO EN LA POSICIÓN 0 DEL ARREGLO
        //SE ESTABLECE UNA REGLA CON LA POSICION DEL ARREGLO
        $rules = [
            '0' => 'required|min:9|max:10',  
        ];
        $validation = Validator::make($data, $rules); // SE REALIZA LA VALIDACION PASANDO EL ARREGLO Y LAS REGLAS
        if( $validation->fails()){ // SI NO CUMPLE LAS REGLAS 
         return 1;
        }
    }

    public function validarCorreoUnico($correo){  

        $usuario =User::where("email","=",$correo)->first();
        if($usuario==null){
            return 0;
        }else{
            return 1;
        }
        
      
    }
    
    // VALIDAD CLAVE QUE AMBAS COINCIDAN
    public function validarclave($clave1,$clave2){  
        if( $clave1 != $clave2){
             return 2; // SON DIFERENTES
        }else{
            return 1; //SON IGUALES
        }
    }

    //VALIDAR CLAVE 1 DEL USUARIO QUE ACEPTE TALES REGLAS
    public function validarclave1($clave1){
        $data=array(); //CREACION DE UN ARREGLO
        $data[0]=$clave1; //SE ASIGNA EL VALOR DEL CAMPO AL ARREGLO EN LA POSICIÓN 0 DEL ARREGLO
        //SE ESTABLECE UNA REGLA CON LA POSICION DEL ARREGLO
        $rules = [
            '0' => 'required|string|min:6',
        ];
        $validation = Validator::make($data, $rules);// SE REALIZA LA VALIDACION PASANDO EL ARREGLO Y LAS REGLAS
        if( $validation->fails()){  // SI NO CUMPLE LAS REGLAS          
             return 1;
        }
    }

    //VALIDAR CLAVE 2 DE CONFIRMACION QUE ACEPTE TALES REGLA
    public function validarclave2($clave2){
        $data=array(); //CREACION DE UN ARREGLO
        $data[0]=$clave2; //SE ASIGNA EL VALOR DEL CAMPO AL ARREGLO EN LA POSICIÓN 0 DEL ARREGLO
        //SE ESTABLECE UNA REGLA CON LA POSICION DEL ARREGLO
        $rules = [
            '0' => 'required|string|min:6',
        ];
        $validation = Validator::make($data, $rules);// SE REALIZA LA VALIDACION PASANDO EL ARREGLO Y LAS REGLAS
        if( $validation->fails()){  // SI NO CUMPLE LAS REGLAS          
             return 1;
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

    //PROCESO PARA GUARDAR LOS DATOS A LA BD LOCAL
    public function store(Request $request)
    {
        // Guardamos en un avariable los datos que vienen en el request
        $dataOld= array();  // reiniciamos el dataOld
        $dataOld['name']= $request->get('name');
        $dataOld['cedula']= $request->get('cedula');
        $dataOld['sexo']= $request->get('sexo');
        $dataOld['direccion']= $request->get('direccion');
        $dataOld['celular']= $request->get('celular');
        $dataOld['email']= $request->get('email');
        $dataOld['ciu']= $request->get('ciuregi');
        $usuario=$this->existeUsuario($request['cedula']); 
        if($this->tieneCaracterEspecialRequest($request,'direccion')==false){
            if(is_null($usuario)){ //SI ES NULO NO EXISTE EL USUARIO POR LO TANTO LO REGISTRA
                if ($this->verificarcedulaRuc($request['cedula'])=='V') {
                    if ($this->validarclave($request['password'],$request['password_confirmation'])==1) {
                       //VALIDAR QUE SE SELECCIONE EL TIPO DE PERSONA CORRECTO
                        //SI SELECCIONA JURIDICA Y EN REALIDAD ES UNA NATURAL NO PODRA REGISTRARSE
                        if(($request['tipopersona'] == "Juridica" && $this->validarsociedadconRuc($request['cedula'])=='I')){
                             return redirect('register')->with([
                            'mensajeTipo'=>'danger',
                            'mensajeInfoTipo'=>'UD. es una persona natural por favor escoja la opción correcta.',
                            'dataOld'=>$dataOld,
                            'estadomuestra'=>1,
                            ])->withInput();
                        }
                        //SI SELECCIONA NATURAL Y EN REALIDAD ES UNA JURIDICA NO PODRA REGISTRARSE
                        if(($request['tipopersona'] == "Natural" && $this->validarpernatural($request['cedula'])=='I')){
                            return redirect('register')->with([
                            'mensajeTipo'=>'danger',
                            'mensajeInfoTipo'=>'UD. es una persona juridica por favor escoja la opción correcta.',
                            'dataOld'=>$dataOld,
                            'estadomuestra'=>1,
                            ])->withInput();
                        }
                        $user=new User();
                        $user->name=$request['name'];
                        $user->cedula=$request['cedula'];
                        //SI ES UNA PERSONA SE ALMACENA EL SEXO
                        if($request['tipopersona'] == "Natural"){
                            $user->sexo=$request['sexo'];
                            $user->tipopersona=$request['tipopersona'];
                        }else{
                            $user->tipopersona=$request['tipopersona'];
                            $user->sexo=null;
                        }
                        //PARA GUARDAR QUE TIPO DE DOCUMENTO ES
                        if(substr($request['cedula'],-3)=="001"){
                            $user->tipo="R"; // EL TIPO DE DOCUMENTO ES UN RUC

                        }elseif(validarCedula($request['cedula'])==true){
                            $user->tipo="C"; // EL TIPO DE DOCUMENTO ES UNA CEDULA
                        }else{
                            $user->tipo="P"; //EL TIPO DE DOCUMENTO ES UN PASAPORTE
                        }
                        $user->direccion=$request['direccion'];
                        $user->celular=$request['celular'];
                        $user->telefono=$request['CelularTl'];
                        if($this->validarCorreoUnico($request['email'])==1){
                            return redirect('register')->with([
                                'mensajeInfoCorreo'=>'Este correo ya ha sido registrado.',
                                'dataOld'=>$dataOld,
                                'estadomuestra'=>1,
                            ])->withInput();
                        }else{
                            $user->email=$request['email'];
                            $user->email_token= base64_encode($request['email']);
                        }

                        $user->password=bcrypt($request['password']);

                        if($request['ciuregi']==null){  // SI EL CIU ES NULL 
                            $user->ciu=$request['ciuregi']; // SE GUARDA UN VALOR NULO
                        }else{
                            $user->ciu=Crypt::decrypt($request['ciuregi']);   // SI NO ES NULO SE DESENCRIPTA Y SE GUARDA
                        }
                        $user->save(); // GUARDANDO EL USUARIO
                        //PARA GUARDAR LA RELACION DE UN USUARIO Y EL ROL 
                        $userTipoUser= new Usuario_TipoUsuario();
                        $userTipoUser->idus001=$user->idus001;
                        $userTipoUser->idtipoUsuario=2;
                        $userTipoUser->estado=0;
                        $userTipoUser->save();
                        return $this->register($user);
                        //return redirect('login')->with(['mensajeexitoso'=>"Registro exitoso por favor digite su cédula y contraseña para iniciar sesión"]);
                    }else{
                        return redirect('register')->with([
                        'mensajeR'=>'danger',
                        'mensajeInfo'=>'Las contraseñas no coinciden',
                        'dataOld'=>$dataOld,
                        'estadomuestra'=>1,
                    ])->withInput();

                    }
                }else if( $this->verificarcedulaRuc($request['cedula'])=='I' ){ // si no es correcta la cedula o ruc
                    return redirect('register')->with([
                        'mensajeR'=>'danger',
                        'mensajeInfo'=>'Verifique que la informacion ingresada sea la correcta',
                        'ErrorCedula'=>'La Cédula o RUC no es válido', 
                        'dataOld'=>$dataOld,
                        'estadomuestra'=>1,
                    ])->withInput();
                }
            }else{
                return redirect('login')->with(['mensajeexitoso'=>"Ud ya se encuentra registrado por favor digite su cédula y contraseña para iniciar sesión. En caso de ser funcionario público favor de acercarse a las oficinas de tecnología del GADM CHONE"]);
            }
        }else{
            return redirect('register')->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>'Verifique que la informacion ingresada sea la correcta',
                'dataOld'=>$dataOld,
                'estadomuestra'=>1,
            ])->withInput();

        }

    }
    // PARA REALIZAR EL CAMBIO DE CONTRASEÑA 
    public function cambiarcontrasena(Request $request){
        $usuario= auth()->User();
            if (Hash::check($request['passwordActaul'], $usuario->password))
            {
                if($request['passwordCambio']==$request['password_confirmationCambio']){
                    if($this->validarclavecambio($request['passwordCambio'])==false){
                          return back()->with(['estado'=>'1','validaclave'=>'La contraseña debe tener al menos 8 caracteres, incluir letras mayúsculas, minúsculas y numeros']);
                    }else{
                        if (Hash::check($request['passwordCambio'], $usuario->password))
                        {
                            return back()->with(['estado'=>'1','mensajeigualActual'=>'La nueva contraseña no puede ser igual a la anterior']);
                        }else{
                            $usuario->password=bcrypt($request['passwordCambio']);
                            $usuario->save();
                            return back()->with(['estado'=>'1','mensajeCambio'=>'Cambio de contraseña exitoso']);
                        }
                    }
                }else{
                      return back()->with(['estado'=>'1','errorcoincide'=>'La nueva contraseña no coincide']);
                } 
            }else{
                return back()->with(['estado'=>'1','errorclaveactual'=>'La contraseña actual no es la correcta']);
            }
    }

    function validarclavecambio($clave){
        $resultado=preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/',$clave);
        if($resultado==1):return true; else: return false; endif;
    }
    

     public function register($request)
    {
        
         $pdf = PDF::loadView('email.documentoEmail',['consulta'=>$request]);
        file_put_contents('documentoConfirmacion/Doc_Acuerdo_responsabilidad'.$request->idus001.'.pdf', $pdf->output());

        //$this->validator($request->all())->validate();
         // event(new Registered($user = $this->create($request->all())));
          dispatch(new SendVerificationEmail($request,0));
        return view('verification');
    }

    public function verify($token)
    {
        $user = User::where('email_token',$token)->first();
        $user->verified = 1;
        if($user->save()){
          return view('auth.login')->with(['validar'=>1]);
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

    public function asignarTipoFPFuncionario(Request $request){

        // obtenemos los datos 
        $input_idusuario = decrypt($request->get('ATFP_idusuario'));
        $cmb_idtipoFP = $request->get('ATFP_tipoFP');
        $cmb_iddepartamento = $request->get('ATFP_departamento');

        // validamos por si hay datos vacios 
        // se valida por si se llegara a editar el required del html  
        if(is_null($input_idusuario) || is_null($cmb_idtipoFP) || is_null($cmb_iddepartamento)){
            return back()->with(['mensajePInfoAsignarTipoFP'=>'Existen datos vacios','estadoP'=>'error']);
        }

     
        // solo los datos que vamos a validar
        $validaCE=array(
            'idusuario'=>$input_idusuario,
            'idtipoFP'=>$cmb_idtipoFP,
            'iddepartamento' => $cmb_iddepartamento
        );


        if(tieneCaracterEspecialRequest($validaCE)){
            return back()->with(['mensajePInfoAsignarTipoFP'=>'No puede ingresar caracteres especiales','estadoP'=>'error']);
        };


        
        // comprobar si el tipofp a registrar ya esta asignado
        $buscaTipoAsignado = td_us001_tipofpModel::where('idtipoFP',$cmb_idtipoFP)
            ->where('idus001',$input_idusuario)
            ->first();

        if(!is_null($buscaTipoAsignado)){ // ya esta registrado el tipo de usuario
            return back()->with(['mensajePInfoAsignarTipoFP'=>'El tipo de usuario ya esta registrado','estadoP'=>'error']);
        }
            
        // VALIDAMOS LAS FECHAS =========================
        // definimos el formato de fechas que admite la base de datos (año-mes-dia)
        $fecha_inicio=date("Y-m-d", strtotime($request->fecha_inicio));
        $fecha_fin=date("Y-m-d", strtotime($request->fecha_fin));

        // comparamos que la fecha de inicio no sea mayor o igual a la fecha de fin
        if($fecha_inicio >= $fecha_fin){
            return back()->with(['mensajePInfoAsignarTipoFP'=>'El rango de fechas ingresados no es correcto','estadoP'=>'error']);
        }

        //verificamos si es jefe o no
        $rol_interno = decrypt($request->cmb_rol_interno);
        $jefe_departamento = 0;
        $secre_departamento = 0;

        if($rol_interno == "S" || $rol_interno=="J"){ // si es secretaria o jefe
            switch ($rol_interno) {
                case 'S': // se seleccionó secretaria
                    $existeSecretaria = td_us001_tipofpModel::where('iddepartamento',$cmb_iddepartamento)->where('secre_departamento',1)->first(); // buscamos una secretarioa en el departamento seleccionado
                    if(!is_null($existeSecretaria)){ // el departamento ya tiene una secretaria registrada
                        return back()->with(['mensajePInfoAsignarTipoFP'=>'El departamento ya tiene asignado un secretario(a).','estadoP'=>'warning']);  
                    }
                    $secre_departamento=1;
                break;
                case 'J': // se seleccionó jefe
                    $existeJefe = td_us001_tipofpModel::where('iddepartamento',$cmb_iddepartamento)->where('jefe_departamento',1)->first(); // buscamos un jefe en el departamento seleccionado
                    if(!is_null($existeJefe)){ // el departamento ya tiene un jefe registrado
                        return back()->with(['mensajePInfoAsignarTipoFP'=>'El departamento ya tiene asignado un jefe.','estadoP'=>'warning']);  
                    }
                    $jefe_departamento=1;
                break;
            }
        }

        
        // REGISTRAMOS LA ASIGNACIÓN ======================================
        // una vez validados los datos registramos la asignacion en la base de datos
        $us001_tipofp = new td_us001_tipofpModel();
        $us001_tipofp->fecha_inicio = $fecha_inicio;
        $us001_tipofp->fecha_fin = $fecha_fin;
        $us001_tipofp->estado = 0;
        $us001_tipofp->idtipoFP = $cmb_idtipoFP;
        $us001_tipofp->idus001 = $input_idusuario;
        $us001_tipofp->iddepartamento = $cmb_iddepartamento;
        $us001_tipofp->secre_departamento = $secre_departamento;
        $us001_tipofp->jefe_departamento = $jefe_departamento;

        
        if($us001_tipofp->save()){
            return back()->with(['mensajePInfoAsignarTipoFP'=>'Actualización exitosa','estadoP'=>'success']);
        }else{
            return back()->with(['mensajePInfoAsignarTipoFP'=>'No se pudo realizar la actualización','estadoP'=>'error']);
        }


    }



    public function asignarTipoFPFuncionarioMostrar($id){
        $funcionario = User::find(decrypt($id));

        // LISTAMOS TODOS LOS TIPOS FP
        $listaTipoFP = TipoFPModel::all();

        // listamos los tipos asignados al usuario
        $tipoFP_asignados = td_us001_tipofpModel::with('tipofp','departamento')->where('idus001',decrypt($id))->get();
        // quitamos de la listaTipoFP los tipos que ya estan asignados
        foreach ($listaTipoFP as $tfp => $tipoFP) {
            foreach ($tipoFP_asignados as $fa => $FPasignado){
                // preguntamos si el tipoFP es igual a cualquiera de los asignados
                if($tipoFP->idtipoFP == $FPasignado->idtipoFP){
                    // en el caso de ya estar asignados lo quitamos de la listaTipoFP
                    unset($listaTipoFP[$tfp]);
                    break; // salimos del bucle
                }
            }
        }

        return response()->json([
            'funcionario'=>$funcionario,
            'listaTipoFP'=>$listaTipoFP,
            'tipoFP_asignados'=>$tipoFP_asignados,
            'token'=>(string)csrf_field() // retornamos el token para colocar en formulario para eliminar
        ]);
    }

    public function eliminarTipoFPFuncionario($idus001_tipofp){
        try {
            $us001_tipofpEliminar = td_us001_tipofpModel::find($idus001_tipofp);
                $us001 = User::find($us001_tipofpEliminar->idus001); // buscamos el usuario que tiene asignado el tipo
                $us001->idtipoFP = 0; // ponemos el tipoFP en cero
                $us001->save();
            $us001_tipofpEliminar->delete();
            return back()->with(['mensajePInfoAsignarTipoFP'=>'Registro eliminado exitosamente','estadoP'=>'success']);  
        } catch (\Throwable $th) {
            return back()->with(['mensajePInfoAsignarTipoFP'=>'No se pudo eliminar el registro','estadoP'=>'error']);  
        }        
    }
}
