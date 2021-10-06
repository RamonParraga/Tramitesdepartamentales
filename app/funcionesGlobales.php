<?php

    //origen de servidores de api
    function getHostServe($server){
        if($server==1){ // servidor de base de datos oracle
            //return 'http://servicesora.chone.gob.ec';
            return 'http://localhost/appServices/public';
        }else if($server==2){ // servidor de base de datos ????
            return '';
        }
    }

    //ruta donde guardar los documentos
    function getRutaDucumento(){
        return "C:/xampp/htdocs/appOnlineFP/public/certificadosDoc/";
        //return "ftp://localhost/appOnlineFP/public/$carpeta";
    }

    // para permitir los formatos de archivo a subir
    function permitirFormato($extension){
        $retorno=false;
        $arrFormatos=array('pdf','PDF','sql','txt');
        foreach ($arrFormatos as $value) {
            if($extension==$value): $retorno=true; continue; endif;
        }
        return $retorno;
    }

    function verificarCedulaRuc($pcedulaRuc){
        // esta funcion si esta registado el ruc o cedula retorna el id del usuario
        // si no es el caso retorna true si esta correcta la cedula o ruc
        // si no lo esta retorna falso

        if($pcedulaRuc=="" || strlen ($pcedulaRuc)<10 ){return 'I';}

        // primero verificamos que la cedula no exista en los registros
        $cedula = substr($pcedulaRuc, 0,10);
        $existe=App\User::where('cedula','like',"%$cedula%")->first();
        if(!is_null($existe)){
            return $existe->idus001;
        }

        // si no esta registrado procedemos a validar la cedula y el RUC
        // I:invalido
        // V:valido
        
        $estadoValidado='I';
        if(validarCedula($pcedulaRuc)){
            $estadoValidado='V';
        }

        if(validarRucPersonaNatural($pcedulaRuc)){
            $estadoValidado='V';
        }

        if(validarRucSociedadPrivada($pcedulaRuc)){
            $estadoValidado='V';
        }

        if(validarRucSociedadPublica($pcedulaRuc)){
            $estadoValidado='V';
        } 
        return $estadoValidado;
        
    }


    // para verificar si un request tiene caracteres epeciales
    // retorna verdadero si almenos uno tiene CE
    function tieneCaracterEspecialRequest($request){
        $retorno=false; // por defecto asumimos queno tiene caracteres especiales
        foreach ($request as $key => $parametro) {
            if($key=='_token'):continue;endif; // para no validar el token de laravel
            $resultado=tieneCaracterEspecial($parametro);
            if($resultado==1):return $retorno=true;endif; // si es 1 es porque se han encontrado CE
        }
        return $retorno;
    }

    // para verificar si un campo tiene caracteres epeciales
    // retorna verdadero si  tiene CE   $resultado=preg_match("/[$%&|\/\<>#&=?¿'`*!¡\[\]{}()".'"'."]/",$texto);
    function tieneCaracterEspecial($texto){
        $resultado=preg_match("/[$%&|<>#&'`*!¡\[\]{}()".'"'."]/",$texto);
        if($resultado==1):return true;else:return false;endif; // si es 1 es porque se han encontrado CE   
    }


    // para validar que la clave sea segura
    function validarClave($clave){
        $resultado=preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/',$clave);
        if($resultado==1):return true; else: return false; endif;
    }

    //para validar que solo sean numeros
    function validarSoloNumero($numero){
        return true;
        // preguntamos si tiene algun signo a 
        
        // codigo en desarrollo
        // la idea es recorrer cada uno de los "numeros" y evaluar si es numero
        // si no lo es retornamos falso
    }

    function tipoUsuario($usid=0){
        
        if(!auth()->guest()){
            if($usid==0){
                $usid=auth()->user()->idus001;
            }
            $tipousuario=DB::table('us001')
                    ->join('us001_tipoUsuario','us001_tipoUsuario.idus001','=','us001.idus001')
                    ->join('tipoUsuario','tipoUsuario.idtipoUsuario','=','us001_tipoUsuario.idtipoUsuario')
                    ->where('us001.idus001',$usid)
                    ->select('tipoUsuario.*')
                    ->get();

            return response()->json($tipousuario);
        }else{
             return null;
        }   
    }

    function userEsTipo($ptipo=''){
        $retorno=false;
        if(!auth()->guest()){
            if(!is_null(tipoUsuario(auth()->user()->idus001))){
                foreach (tipoUsuario()->original as $key => $item_tipo) {
                    if($item_tipo->tipo==$ptipo){
                        $retorno=true;
                    }
                }
            }
        }
        return $retorno;
    }

    function thisUserEsTipo($ptipo='',$usid){
        $retorno=false;
        if(!auth()->guest()){
            if(!is_null(tipoUsuario($usid))){
                foreach (tipoUsuario($usid)->original as $key => $item_tipo) {
                    if($item_tipo->tipo==$ptipo){
                        $retorno=true;
                    }
                }
            }
        }
        return $retorno;
    }

    function usuarioTieneVariosRoles(){

        $retorno = Collect();
        
        if(auth()->guest()){ // si no hay usuarios logueados no retornamos nada en el menu
            $retorno->status = false;
            return $retorno;
        }

        //obtenemos los tipos de usuarios que tiene asignado
        $listatipoFPasignados = App\td_us001_tipofpModel::with('tipofp','departamento')
            ->where('idus001',auth()->user()->idus001)
            ->get(); // obtenemos todos los idtipoFP asignados al usuario logueado

        if(sizeof($listatipoFPasignados)>1){
            $retorno->status = true;
            $retorno->listatipoFPasignados = $listatipoFPasignados;
            return $retorno;
        }else{
            $retorno->status = false;
            return $retorno;
        }

    }


    function departamentoLogueado(){
        try {
            if(auth()->guest()){ // si no hay usuarios logueados no retornamos nada en el menu
                goto MENU1;
            }else if(auth()->user()->idtipoFP ==0 || userEsTipo('ADFP')){
                goto MENU1;         
            }else{
                $objUs001_tipofp = App\td_us001_tipofpModel::with('departamento','tipofp')
                    ->where('idus001',auth()->user()->idus001)
                    ->where('idtipoFP',auth()->user()->idtipoFP)
                    ->first();
    
                return [
                    'iddepartamento'=>(string)$objUs001_tipofp->departamento->iddepartamento,
                    'departamento' => (string)$objUs001_tipofp->departamento->nombre,
                    'objdepartamento' => $objUs001_tipofp->departamento,
                    'tipoFP' =>(string)$objUs001_tipofp->tipofp->descripcion,
                    'jefe_departamento' => $objUs001_tipofp->jefe_departamento,
                    'secre_departamento' => $objUs001_tipofp->secre_departamento
                ];
             
            }
        } catch (\Throwable $th) {
            goto MENU1;
        }

        MENU1:
        return [
            'iddepartamento' => 0,
            'departamento' => "Sin departamento",
            'tipoFP' =>"Sin Tipo",
            'jefe_departamento' => 0,
            'secre_departamento' => 0
        ];

    }



    function jefeDetartamentoLogueado(){

        if(Auth::guest()){
            return false;
        }else{

            // buscamos el jefe de ese departamento
            $depLogueado = departamentoLogueado(); // obtenemos el departameto en el que esta logueado el usuario que va a acrear el tramite
            $jefeDepartamento = App\td_us001_tipofpModel::with('us001') // obtenemos el jefe de ese departamento
                    ->where('iddepartamento',$depLogueado['iddepartamento'])
                    ->where('jefe_departamento','1')
                    ->first();

            if(is_null($jefeDepartamento)){ // si no se encuentra nada no permitimos que ingrese en el modulo
                return false;
            }else{
               return $jefeDepartamento->us001;
            }            
        }

    }


    function listarMenuSession(){
        // FP= funcionario publico

        $consultaMenu = array(); // iniciamos la variable de retorno como un arreglo vacio
        if(auth()->guest()){ // si no hay usuarios logueados no retornamos nada en el menu
            goto FINALM;
        }

        $consultaMenu= App\MenuModel::with(['gestion'=>function($query_gestion){
                $query_gestion->orderBy('orden', 'ASC');
            }])
            ->orderBy('orden','ASC')->get(); // obtenemos todo el menu de opciones

        if(userEsTipo('ADFP')){goto FINALM;} // preguntamos si es un usuario FP administrador en ese caso dejamos que pueda ver todos los menus

    
            $listatipoFPasignados = App\td_us001_tipofpModel::where('idus001',auth()->user()->idus001)->get(); // obtenemos todos los idtipoFP asignados al usuario logueado

            //validamos si no a seleccionado un tipo de usuario
            if(sizeof($listatipoFPasignados)==0){
                // si no tiene ningun tipo asignado retornamos el menu como vacio
                $consultaMenu = array();
                goto FINALM;
            }else if(sizeof($listatipoFPasignados)==1 && auth()->user()->idipoFP==0){
                
                $usuarioLogueado = App\User::find(auth()->user()->idus001); // buscamos el usuario logueado
                $usuarioLogueado->idtipoFP=$listatipoFPasignados[0]->idtipoFP; //actualizamos el idtipoFP con el id del unico tipoFP que tiene asignado
                $usuarioLogueado->save();  
            }else if(sizeof($listatipoFPasignados)>1 && auth()->user()->idtipoFP==0){ // si tiene mas de un tipo de usuaio asignado y no a seleccionado uno para iniciar sesion
                return array(); // no retornamos nada porque el usuario no a seleccionado un tipofp
            }


            

        $idtipoFP=auth()->user()->idtipoFP; // si no FP administrador obtenemos el tipo de usuario
        $tipoFPGestion = App\TipoFPGestionModel::where('idtipoFP',$idtipoFP)->get(); // obtenemos todas las gestiones que tiene asignadas dicho tipo de FP

        foreach ($consultaMenu as $m => $menu){ // recorremos cada uno de los menus
            foreach ($menu->gestion as $g => $gestion) { // recorremos cada una de las gestiones de cada menu
                $gestionasignada=false; // falso si la gestion actual no esta asignada al usuario logueado
                foreach ($tipoFPGestion as $tg => $tipoFP) { // recorremos las gestiones asignadas al usuario y la comparamos con la gestion del menu
                    if($gestion->idgestion==$tipoFP->idgestion){
                        $gestionasignada=true;
                        break;
                    }
                }
                if(!$gestionasignada){ // si es falso quiere decir que la gestion no esta asignada al usuario
                    unset($menu->gestion[$g]); // eliminamos la gestion del menu
                }
            }
            // verificamos si el menu ahún contiene gestiones
            if(sizeof($menu->gestion)<=0){ // si no tiene ninguna gestion eliminamos el menu
                unset($consultaMenu[$m]);
            }
        }

        FINALM:
        //dd($consultaMenu);
        return $consultaMenu; // retornamos el menu solo con las gestiones que le pertenecen al usuario

    }






    // ============================= FUNCIONES PARA GESTION DE TRAMITES DEPARTAMENTALES =====================

        // funcion que crea el codigo html de un documento
        function getEstructuraDocumento($contenido, $borrador){

            // obtenemso solo el nombre de las imagenes cabecera y pie de pagina
            $formato=App\td_FormatoModel::first();
            $nomCabecera = $resultado = substr( $formato->cabecera, 11); // quitamos el "/tdFormato/" de la consulta
            $nomPie = $resultado = substr($formato->pie, 11); // quitamos el "/tdFormato/" de la consulta
            
            $page_margin_top = $formato->page_margin_top; // margen superor de la pagina
            $page_margin_right = $formato->page_margin_right; // margen derecho de la pagina
            $page_margin_bottom = $formato->page_margin_bottom; // margen inferior de la pagina
            $page_margin_left = $formato->page_margin_left; // margen izquierdo de la pagina

            
            $footer_bottom = $formato->footer_bottom; // borde inferior del footer
            $footer_height = $formato->footer_height; // alto del footer

            $header_top = $formato->header_top; // top del header
            $header_height = $formato->header_height; // alto del header

            $main_left = $formato->main_left; // borde derecho del cuerpo del documento
            $main_right = $formato->main_right; // borde izquierdo del cuerpo del documento

            // combertimos a base64 las imagenes para poder cargarlas
            $cabecera = base64_encode(Storage::disk('tdFormato')->get($nomCabecera));
            $pie = base64_encode(Storage::disk('tdFormato')->get($nomPie));

            // generamos el pdf 
            $pdf = PDF::loadView('tramitesDepartamentales.generarDocumento.cuerpoDocumento',[
                'page_margin_top' => $page_margin_top,
                'page_margin_right' => $page_margin_right,
                'page_margin_bottom' => $page_margin_bottom,
                'page_margin_left' => $page_margin_left,
                'header_top' => $header_top,
                'header_height' => $header_height,
                'footer_bottom' => $footer_bottom,
                'footer_height' => $footer_height,
                'main_right' => $main_right,
                'main_left' => $main_left,
                'cabecera' => $cabecera,
                'pie' => $pie,
                'contenido' => $contenido,
                'borrador' => $borrador
            ]);

            $pdf->setPaper("A4", "portrait");
            return $pdf;

        }


        // función para generar el cuerpo del documento (COD DOCUMENTO, PARA, ASUNTO, DE, COPIA)
        function getInfoDocumento($contenido, $asunto, $listArrPara, $listArrCopia, $idtipoDocumento, $numReferencia, $listaAnexos, $firma_electronica){

            // generamos la fecha del documento
                setlocale(LC_ALL,"es_ES@euro","es_ES","esp"); //IDIOMA ESPAÑOL
                $fecha= date('Y-m-j');
                $fecha = strftime("Chone, %d de %B de %Y", strtotime($fecha));
            
            // obtenemos el año actual
                $anio = date("Y");
            
            // id del departamento logueado
                $iddepartamentoLogueado = departamentoLogueado()['iddepartamento'];
            
            // obtenemos la estructura del documento por el tipo de documento. departamento y por el año
                $estrDoc = App\td_EstructuraDocumentoModel::with('tipo_documento', 'departamento')
                    ->where('idtipo_documento', $idtipoDocumento)
                    ->where('iddepartamento', $iddepartamentoLogueado)
                    ->where('anio', $anio)
                    ->first();

                if(is_null($estrDoc)){ // cremos una nueva estructura de documento
                            
                    $estrDoc = new App\td_EstructuraDocumentoModel();
                    $estrDoc->anio = $anio;
                    $estrDoc->secuencia = 0;
                    $estrDoc->iddepartamento = $iddepartamentoLogueado;
                    $estrDoc->idtipo_documento = $idtipoDocumento;
                    $estrDoc->estado = 1;
                }

                $estrDoc->secuencia = $estrDoc->secuencia+1; // incrementamos la secuencio del tipo de documento en el departamento
                $estrDoc->save();

            // generamos el codigo del documento
                $codigoDocumento = $estrDoc->tipo_documento->estructura."-".$estrDoc->departamento->abreviacion."-".$anio."-".$estrDoc->secuencia."-".$estrDoc->tipo_documento->abreviacion;

            // CREAMOS LA ESTRUCTURA DEL DOCUMENTO (PARA) ----------------------------------------------
                $contentPara="";
                foreach ($listArrPara as $p => $iddepartamentoPara){
                    
                    // buscamos el jefe de ese departamento
                    $jefeDepartamento = App\td_us001_tipofpModel::with('us001','departamento') // obtenemos el jefe de ese departamento
                        ->where('iddepartamento',$iddepartamentoPara)
                        ->where('jefe_departamento','1')
                        ->first();

                    $contentPara = $contentPara.'
                        <div class="cont_pc">
                            <span>'.$jefeDepartamento->us001->name.'</span><br>
                            <span class="titulo">'.$jefeDepartamento->departamento->nombre.'</span>
                        </div>
                    ';       
                }

            // CREAMOS LA ESTRUCTURA DEL DOCUMENTO (COPIA) ----------------------------------------------
                $contentCopia="";
                $estiloCopia = "display:none;"; // para ocultar la table si no hay copias
                foreach ($listArrCopia as $p => $iddepartamentoCopia){

                    $estiloCopia = ""; // para que no oculte la tabla de copias
                    // buscamos el jefe de ese departamento
                    $jefeDepartamento = App\td_us001_tipofpModel::with('us001') // obtenemos el jefe de ese departamento
                        ->where('iddepartamento',$iddepartamentoCopia)
                        ->where('jefe_departamento','1')
                        ->first();

                    $contentCopia = $contentCopia.'
                        <div class="cont_pc">
                            <span>'.$jefeDepartamento->us001->name.'</span><br>
                            <span class="titulo">'.$jefeDepartamento->departamento->nombre.'</span>
                        </div>
                    ';       
                }

            // OBTENEMOS LOS DATOS DEL DEPARTAMENTO LOGUEADO --------------------------------------------
                // buscamos el jefe de ese departamento
                $depLogueado = departamentoLogueado(); // obtenemos el departameto en el que esta logueado el usuario que va a acrear el tramite
                $jefeDepLogueado = App\td_us001_tipofpModel::with('us001', 'departamento') // obtenemos el jefe de ese departamento
                    ->where('iddepartamento',$depLogueado['iddepartamento'])
                    ->where('jefe_departamento','1')
                    ->first();

            // GENERAMOS EL CODIGO PARA MOSTRAR MENSAJE DE FIRMA ELECTRONICA ------------------------
            $text_firma_electronica = "<br>";
            if($firma_electronica == true){
                $text_firma_electronica = '<p style="margin-bottom:5px;"><i style="color:blue; font-weight: 700;">Documento firmado electrónicamente</i></p>';
            }

            //GENERAMOS EL CODIGOS PARA EL NUMERO DE REFERENCIA ---------------------------------------------------------
                $content_num_referencia = "";
                if(!is_null($numReferencia) && $numReferencia!=""){
                    $content_num_referencia = "
                        <table style='font-size: 13px;'>
                            <tr>
                                <td class='titulo'>REFERENCIA: </td>
                                <td>$numReferencia</td>
                            </tr>
                        </table>
                    ";
                }

            // CREAMOS LOS CODIGO DE ANEXOS -------------------------------------------------------------

                $liAnexos = "";
                $content_anexos = "";
                foreach ($listaAnexos as $anx => $codAnexo) {
                    $liAnexos = $liAnexos."<li>$codAnexo</li>";
                }

                if($liAnexos != ""){
                    $content_anexos = "
                        <br>
                        <table style='font-size: 13px;'>
                            <tr>
                                <td class='titulo'>ANEXOS: </td>
                                <td style='padding-top: 0;'><ul style='margin-top: 0; padding-top: 0; padding-left: 12px;'>$liAnexos</ul></td>
                            </tr>
                        </table>               
                    ";
                }


            // CREAMOS EL CONTENIDO DEL DOCUMENTO -------------------------------------------------------
                $texto_documento = '
                    <style type="text/css">
                        .titulo{ font-weight: bold; vertical-align: baseline; }
                        .cont_pc{ margin-bottom: 10px; }
                        .codigo_doc{ text-align: right; line-height: 25px; }
                    </style>

                    <div class="codigo_doc">
                        <span class="titulo">'.$estrDoc->tipo_documento->descripcion.' Nro. '.$codigoDocumento.'</span> <br>
                        <span class="titulo">'.$fecha.'</span>
                        <br> <br>
                    </div>

                    <table>
                        <tr>
                            <td class="titulo">PARA: </td>
                            <td>'.$contentPara.'</td>
                        </tr>
                
                        <tr>
                            <td class="titulo" style="padding-right: 20px">ASUNTO: </td>
                            <td style="text-transform: uppercase">'.$asunto.'</td>
                        </tr>
                    </table>
                    <br>
                    '.$contenido.'
                    <br>
                    Atentamente,
                    <br><br>'.$text_firma_electronica.'                                  
                    <br>
                        <div>
                            <span>'.$jefeDepLogueado->us001->name.'</span> <br>
                            <span class="titulo">'.$jefeDepLogueado->departamento->nombre.'</span>
                        </div>
                    <br>
                    <table style="font-size: 13px; '.$estiloCopia.'">
                        <tr>
                            <td class="titulo">COPIA: </td>
                            <td>'.$contentCopia.'</td>
                        </tr>
                    </table>
                    
                    '.$content_num_referencia.$content_anexos.'
                ';
            // ---------------------------------------------------------------------
            $retornar = collect();
            $retornar->texto_documento_completo = $texto_documento;
            $retornar->codigo_documento = $codigoDocumento;

            return $retornar;
        }

    // ============================= /FUNCIONES PARA GESTION DE TRAMITES DEPARTAMENTALES =====================



    // funcion que retorna todo lo de la tabla de parametros generales
    function parametros_generales(){
        $parGenFormato = App\ParametrosGeneralesModel::all();
        return $parGenFormato;
    }

    
?>