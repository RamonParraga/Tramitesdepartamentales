<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Mail;
use session;
use DateTime;
use Log;
use App\Password_Reset_Model;
use App\Jobs\SendVerificationEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
class RestarurarContraseniaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('guest');
    }

    // RETORNO DE VISTA PARA SOLICITAR UN CAMBIO DE CONTRASEÑA
    public function index()
    {
        return view('auth.passwords.reset');

    }

    // funcion para solicitar la restauración de la contraseña por medio del correo electronico
    public function solicitarRestauracionClave(Request $request){

        // try {

            //verificamos que existan caracteres especiales
            if(tieneCaracterEspecial($request->email)){
                return back()->with(['email'=>'No está permitido ingresar caracteres especiales en la información',]);
            }
            // definimos los mensajes para la validación
            $messages = [
                'email.required' => 'Campo no valido, por favor verificar',
                'email.email'=>'El correo electrónico no es un correo válido',
                'g-recaptcha-response.required' => 'Por favor verifique que no es un robot',
            ];
            
            
            $this->validate($request, [
                'email' => 'required|email',
                'g-recaptcha-response' => 'required|recaptcha'
            ], $messages);

            $user = User::where('email',$request->email)->first(); // buscamos el usuairo por el correo electronico

            // verificamos si el correo electronico esta registrado en la base de datos
            if(is_null($user)){
                return back()->with(['error_email'=>"El correo no se encuentra en nuestros registrados"]);
            }

            // creamos el token y retornamos a la vista anterior
            $envioCorreo=$this->enviarCorreoConfirmar($request->email);
            if($envioCorreo==true){
                $mesajeRetorno = "Se envió un mensaje al correo $request->email para realizar la restauración de la contraseña";
                $status = "success";
            }else{
                $mesajeRetorno = "Error el enviar el correo electronico, por favor intentelo de nuevo mas tarde";
                $status = "danger";
            }
            
            return back()->with([
                'mesajeEnvio'=>$mesajeRetorno,
                'status'=>$status
            ]);
            
        // } catch (\Throwable $th) {
        //     ERRORENVIO:
        //     Log::error("Error intentar restaurar un contraseña: ".$th->getMessage());
        //     return back()->with([
        //         'mensajeR'=>'danger',
        //         'mensajeInfo'=>"No se pudo resolver la petición",
        //     ]); 
        // }

    }

    // funcion que retorna una vista para realizar el cambio de contraseña
    public function cambiarContrasenia($emailToken){
        // verificamos que el token no tenga caracteres especiales
        if(tieneCaracterEspecial($emailToken)){
            goto RETORNARINICIO;
        }

        // buscamos un usuario por el token recivido
        $user = User::where('email_token',$emailToken)->first();
        
        if(is_null($user)){
            goto RETORNARINICIO;
        }else{
            //validamo que el token no haya expirado
            $fechaFinToken = strtotime('+24 hour', strtotime($user->fecha_email_token)); // el token solo se puede usar 24 horas
            $fecha_actula = strtotime(date('Y-m-d H:i:s'));
            if($fecha_actula>$fechaFinToken){ // en este caso ya extipó el token
                $user->email_token = null;
                $user->fecha_email_token = null;
                $user->save(); // limpiamos el token del email
                goto RETORNARINICIO;
            }            
        }
          

        // si pasa todas las validaciones retornamos la vista para cambiar la contraseña
        return view('auth.passwords.cambiarContrasenia')->with([
            'email_token'=>$emailToken
        ]);
        
        RETORNARINICIO:
        return view('error');

    }

    // funcion que recive la nueva contraseña y la cambia
    public function restaurarContrasenia(Request $request){
        // try{

            $emailToken = decrypt($request->email_token);

            // verificamos que el request no tenga caracteres especiales
            // solo los datos que vamos a validar
            $validaCP=array(
                'emailToken'=>$emailToken,
                'password'=>$request->password,
                'password_confirmation'=> $request->password_confirmation
            );
            
            //PIMERO VERIFICAMOS QUE NINGUN DATO TENGA CARACTERES ESPECIALES
            if(tieneCaracterEspecialRequest($validaCP)){
                $mensajeError="No está permitido ingresar caracteres especiales en la información";
                goto RETORNARINICIO;
            }
    

            // buscamos un usuario por el token recivido
            $user = User::where('email_token',$emailToken)->first();;

            if(is_null($user)){
                $mensajeError = "Error el token no es correcto";
                goto RETORNARINICIO;
            }

            //VALIDAR QUE LOS DATOS NO SEAN NULOS
            $datavalidar=array(
                'password'=>$request->password,
                'password_confirmation'=> $request->password_confirmation
            );
            //SE ESTABLECE UNA REGLA CON LA POSICION DEL ARREGLO
            $reglas = [
                'password' => ['required','regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/'],
                'password_confirmation' => 'required|same:password'
            ];

            Validator::make($datavalidar, $reglas)->validate();// SE REALIZA LA VALIDACION PASANDO EL ARREGLO Y LAS REGLAS

            // SI PASA TODAS LAS VALIDACIONES SE ACTIALIZA LA CLAVE
            $user->email_token = null;
            $user->fecha_email_token = null;
            $user->password = bcrypt($request->password);
            $user->save();

            // retornamos a la vista el mensaje de exito
            return view('auth.passwords.cambiarContrasenia')->with([
                'cambioExitoso'=>true
            ]);

            RETORNARINICIO:
            return back()->with([
                'mensajeR'=>'danger',
                'mensajeInfo'=>$mensajeError,
            ]); 
        // } catch (\Throwable $th) {
        //     Log::error($th->getMessage());
        //     $mensajeError = "No se pudo completar la operación";
        //     goto RETORNARINICIO;
        // }        
    }


    // función para enviar al correo el link para cambiar la contraseña
    public function enviarCorreoConfirmar($email){
    
        // buscamos el usuario por el correo
        $user = User::where('email',$email)->first();
        if(is_null($user)){ // si no se encuentra el usuario
            goto ERRORENVIOCORREO;
        }

        // creamos un token para el email
        $user->email_token = str_random(20);
        $user->fecha_email_token = date('Y-m-d H:i:s');
        $user->save();

        Mail::send('auth.emailEnlaceCambioClave', ['usuario'=>$user], function ($m) use ($user) {
            $m->to($user->email)
            ->subject('RESTAURACIÓN DE CONTRASEÑA');
        });

        // Log::info(Mail::failures());

        if(count(Mail::failures()) > 0){
            ERRORENVIOCORREO:
            Log::error($email.' Problema al enviar el correo');
            return false;
        }else{
            return true;
        }
    }


    public function validarclavecambio($clave){
        $resultado=preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/',$clave);
        if($resultado==1):return true; else: return false; endif;
    }

    public function vistaRecuperar($token)
    {   

        $contraseñaToken =Password_Reset_Model::where("token","=", $token)->first(); 
      
        if($contraseñaToken!=null){
            $Fecha_Token =  strtotime ($contraseñaToken->created_at); //FECHA Y HORA EN QUE SE GENERO EL TOKEN 
            // //FECHA Y HORA ACTUAL
            $date= date('Y-m-j H:i:s'); 
            $Fecha_Actual = strtotime ($date); 

            $Fecha_Token= date('Y-m-j H:i:s', $Fecha_Token); 
            $Fecha_Token=strtotime ( '+8 hour' , strtotime ($Fecha_Token) ) ;
            //$Fecha_limite = date ( 'Y-m-j H:i:s' , $Fecha_limite);
            if($Fecha_Token >=  $Fecha_Actual)
            {
                $usuario =User::where("idus001","=", $contraseñaToken->idus001)->first(); 
                if($usuario!=null){
                  return view('auth.RecuperacionContrasenia')->with(['usuario'=>$usuario->name,'token'=>Crypt::encrypt($usuario->idus001)]);
                }else{
                    abort(404);
                }
            }else{
                abort(404);
            }
        }else{
            abort(404);

        }
    }

    public function cambiarContraseniaperdida(Request $request){

            $usuario =User::where("idus001","=", Crypt::decrypt($request->token))->first(); 
            if($this->tieneCaracterEspecialRequest($request,'token')==false){
                if($request['passwordrecupera']==$request['password-confirmRecupera']){
                    if($this->validarclavecambio($request['password-confirmRecupera'])==false){
                          return back()->with(['estado'=>'1', 'mensajeclase'=>'danger','mensajeCambio'=>'La contraseña debe tener al menos 8 caracteres, incluir letras mayúsculas, minúsculas y números']);
                    }else{
                        $usuario->password=bcrypt($request['passwordrecupera']);
                        $usuario->save();
                        $passReset =Password_Reset_Model::where("idus001","=", $usuario->idus001)->first();
                        $passReset->delete();
                        return redirect('login')->with(['estado'=>'1','mensajeCambio'=>'Cambio de contraseña exitoso, por favor inicie sesión.']);
                    }
                }else{
                      return back()->with(['estado'=>'1','mensajeclase'=>'danger','mensajeCambio'=>'La nueva contraseña no coincide']);
                }
            }else{
                 return back()->with(['estado'=>'1', 'mensajeclase'=>'danger','mensajeCambio'=>'No se admiten caracteres especiales']);

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
    function tieneCaracterEspecialRequest($request,$token){
        $retorno=false; // por defecto asumimos queno tiene caracteres especiales
        foreach ($request->request as $key => $parametro) {
            if($key=='_token' || $key==$token):continue;endif; // para no validar el token de laravel
            $resultado=preg_match('/[$%&|<>#&=()]/',$parametro);
            if($resultado==1):return $retorno=true;endif; // si es 1 es porque se han encontrado CE
        }
        return $retorno;
    }


    public function recaptchaValidar($recapcha){
        $data=array(); //CREACION DE UN ARREGLO
        $data[0]=$recapcha; //SE ASIGNA EL VALOR DEL CAMPO AL ARREGLO EN LA POSICIÓN 0 DEL ARREGLO
        //SE ESTABLECE UNA REGLA CON LA POSICION DEL ARREGLO
        $rules = [
            '0' => 'required|recaptcha'
        ];
        $validation = Validator::make($data, $rules);// SE REALIZA LA VALIDACION PASANDO EL ARREGLO Y LAS REGLAS
        if( $validation->fails()){  // SI NO CUMPLE LAS REGLAS          
             return 0; //NO SELECCIONADO
        }else{
            return 1; //SELECCIONADO
        }
    }

    public function restaurarContraseña(Request $request){ 
      // if($this->recaptchaValidar($request['g-recaptcha-response'])==1 && $request['cedulaRes'] != ''){
            $host= $_SERVER["HTTP_HOST"];
            if($this->tieneCaracterEspecialRequest($request,'')==false){
                $usuario =User::where("email","=",$request['cedulaRes'])->first();
                if($usuario!=null){                    
                        if (($request['cedulaRes'] == $usuario->email))
                        {
                                $reset =Password_Reset_Model::where("idus001","=",$usuario->idus001)->first();
                                if($reset!=null){
                                    $reset->delete();
                                    $passReset = new Password_Reset_Model();
                                    $passReset->email=$usuario->email;
                                    $passReset->token=base64_encode($usuario->idus001.date('Y-m-j H:i:s'));
                                    //$passReset->token=base64_encode(Crypt::encrypt($usuario->email));
                                    $passReset->idus001=$usuario->idus001;
                                    $passReset->save(); 
                                    dispatch(new SendVerificationEmail($passReset,1));      
                                    return back()->with(['estado'=>'1','mensajeInfoExito'=>$usuario->email]);
                                 }else{
                                    $passReset = new Password_Reset_Model();
                                    $passReset->email=$usuario->email;
                                    $passReset->token=base64_encode($usuario->idus001.date('Y-m-j H:i:s'));
                                    $passReset->idus001=$usuario->idus001;
                                    $passReset->save();
                                    // Mail::send('email.CambioContraseña',$request->all(), function($msj){
                                    //     $msj->subject('RECUPERACIÓN DE CONTRASEÑA');
                                    //     $msj->to('leonardosabando03@hotmail.com');
                                    // });

                                   dispatch(new SendVerificationEmail($passReset,1));   
                                    // Mail::raw('Para recuperar la contraseña de su cuenta por favor presione en el siguiente enlace:  '.'http://'.$host.'/vistaRecuperar/'.$passReset->token, function ($msj) {
                                    //     $msj->subject('RECUPERACIÓN DE CONTRASEÑA');
                                    //    $msj->to('leonardosabando03@hotmail.com');
                                    // });

                                   //mail('leonardosabando03@hotmail.com', 'prueba', 'hfjhgjhgjgjhgjgjhgjghj');
                                    return back()->with(['estado'=>'1','mensajeInfoExito'=>$usuario->email]);
                                 }
                        }
                }else{
                        return back()->with(['mensajeInfonoExiste'=>'Usuario no se encuentra registrado']);
                }
            }else{
                return back()->with(['mensajeInfo'=>'Verifique que el correo sea válido']); 
            }
        // }else{
        //     return back()->with(['mensajeInfo'=>'Por favor complete todos los campos']); 

        // }
    }
    

    public function store(Request $request)
    {
       
       
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
