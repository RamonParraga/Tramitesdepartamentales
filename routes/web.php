<?php

Route::get('/', function () {
    return view('home');
})->name('home');

Auth::routes();

// para validar que tipo de usuario despues de loguearce
Route::get('/validarTipoUsuario','ValidarController@verificarUsuario');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/loginTipoFP','ValidarController@loginTipoFP')->name('loginTipoFP');
Route::post('/seleccionarTipoFP','ValidarController@seleccionarTipoFP')->name('seleccionarTipoFP');

//=============== RUTAS PARA EL REGISTRO DE UN FUNCIONARIO ==============
Route::resource('/registrarUsuario','RegistrarController')->middleware('auth');
Route::post('/buscarFP','RegistrarController@buscarFP')->middleware('auth');
//=======================================================================

//=============== RUTAS PARA CREAR CUENTAS DE EXTRANJERO ================
Route::resource('/registrarExtranjero','RegistrarExtranjeroController')->middleware('validar.ruta.por.tipoFP');
Route::post('/buscarCIU','RegistrarExtranjeroController@buscarCIU')->middleware('validar.ruta.por.tipoFP');
//=======================================================================

//=============== RUTAS PARA VR PERFIL DE USUARIO Y CAMBIAR CONTRASEÑA ================
//RUTA PARA CAMBIAR LA CONTRASEÑA
Route::post('/cambiocontrasena', 'Regis_UserController@cambiarcontrasena')->name('cambiocontrasena');
Route::post('/cambiarContraseniaperdida', 'RestarurarContraseniaController@cambiarContraseniaperdida')->name('cambiarContraseniaperdida');

// ruta para mostrar formulacio para solicitar la restauracion de contraseña por el correo electronico
Route::get('/restaurarCont','RestarurarContraseniaController@index')->name('restaurarCont');
// ruta para verificar el correo y enviar el email de restauración
Route::post('solicitarRestauracionClave', 'RestarurarContraseniaController@solicitarRestauracionClave')->name('solicitarRestauracionClave');
// ruta para retornar la vista que permite cambia la clave a un usuario deslogueado con el email_token
Route::get('cambiarContrasenia/{emailToken}', 'RestarurarContraseniaController@cambiarContrasenia');
// ruta que cambia la contraseña
Route::post('restaurarContrasenia', 'RestarurarContraseniaController@restaurarContrasenia')->name('restaurarContrasenia');;
//=======================================================================


//=============== RUTAS PARA LA ADMINISTRACION DE PERMISOS DE RUTAS ================
Route::get('/gestionPermisos','GestionPermisosController@index')->name('gestionPermisos')->middleware('validar.ruta.por.tipoFP','administrador');
Route::resource('/gestionMenu','MenuController');
Route::resource('/gestionGestion','GestionController');
Route::resource('/gestionTipoFP','TipoFPController');
Route::resource('/asignarGestionTipo','TipoFPGestionController');
Route::post('/asignarTipoFPFuncionario','Regis_UserController@asignarTipoFPFuncionario');
Route::delete('/eliminarTipoFPFuncionario/{idus001_tipofp}','Regis_UserController@eliminarTipoFPFuncionario');
Route::get('/asignarTipoFPFuncionarioMostrar/{idus001?}','Regis_UserController@asignarTipoFPFuncionarioMostrar');
//==================================================================================


//*************************************************************************************************************
//*************************************************************************************************************
//*********************************  RUTAS SISTEMA DE TRAMITES DEPARTAMENTALES  *******************************
//*************************************************************************************************************
//*************************************************************************************************************
Route::prefix('departamentos')->group(function () {
    Route::resource('gestion','DepartamentoController')->middleware('validar.ruta.por.tipoFP','administrador');
    Route::get('filtrarDepartamentosPorPeriodo/{idperiodo?}','DepartamentoController@filtrarDepartamentosPorPeriodo');

    Route::get('actividad', 'TdActividadModelController@index')->middleware('validar.ruta.por.tipoFP');
    Route::post('actividad', 'TdActividadModelController@store');
    Route::get('actividad/{id}/edit', 'TdActividadModelController@edit');
    Route::put('actividad/{id}', 'TdActividadModelController@update');
    Route::delete('actividad/{id}', 'TdActividadModelController@destroy');

    // Route::resource('actividad','TdActividadModelController')->middleware('validar.ruta.por.tipoFP','administrador');

    Route::get('filtrarActividadPorDepartamento/{iddepartamento}', 'TdActividadModelController@filtrarActividadPorDepartamento');
});


Route::prefix('periodo')->group(function () {
    Route::resource('gestion','PeriodoController');
});

Route::prefix('tipotramite')->group(function () {
    Route::resource('gestion','TdTipoTramiteModelController');
});


Route::prefix('tipodocumento')->group(function () {
    Route::resource('gestion','TdTipoDocumentoModelController');
});

Route::prefix('secuenciastramite')->group(function () {
    Route::resource('gestion','TdSecuenciasTramiteModelController');
});

Route::prefix('prioridadtramite')->group(function () {
    Route::resource('gestion','TdPrioridadTramiteController')->middleware('auth');
});


Route::prefix('archivo')->group(function () {
    Route::resource('listado','TdGestionListadoArchivoController')->middleware('auth');
    Route::get('/listado/{inicio?}/{fin?}/filtrar','TdGestionListadoArchivoController@filtrar')->middleware('auth');
    Route::get('/listado/{busqueda?}/filtrarportexto','TdGestionListadoArchivoController@filtrarportexto')->middleware('auth');
    Route::get('/listado/{lugar?}/filtrarporlugar','TdGestionListadoArchivoController@filtrarporlugar')->middleware('auth');
    Route::get('/listado/{ultimo?}/filtrarporultimo','TdGestionListadoArchivoController@filtrarporultimo')->middleware('auth');
    Route::resource('registro','TdGestionRegistroArchivoController')->middleware('auth');
});


Route::prefix('sector')->group(function () {
    Route::resource('gestion','TdSectorController')->middleware('auth');
});

Route::prefix('seccion')->group(function () {
    Route::resource('gestion','TdSeccionController')->middleware('auth');
});

Route::prefix('bodega')->group(function () {
    Route::resource('gestion','TdBodegaController')->middleware('auth');
});

Route::prefix('estructuradocumento')->group(function () {
    Route::resource('gestion','TdEstructuraDocumentoModelController');
    Route::post('AnioNuevo','TdEstructuraDocumentoModelController@AnioNuevo');
});

Route::prefix('formato')->group(function () {
    Route::resource('gestion','TdFormatoModelController')->middleware('auth');
    // // metodos para visualizar
    // Route::get('verFormato','TdFormatoModelController@verFormato');

});


Route::prefix('flujo')->group(function(){
    // Route::get("gestion", "TdFlujoController@index")->middleware('validar.ruta.por.tipoFP');
    Route::resource('gestion','TdFlujoController')->middleware('auth');
    Route::get('filtrarFlujoYDepartamentosPorTipoTramite/{idTipoTramite}','TdFlujoController@filtrarFlujoYDepartamentosPorTipoTramite');
    Route::get('filtratFlujosHijos/{idflujo}','TdFlujoController@filtratFlujosHijos');
    Route::get('filtrarActividadesPorDepartamento/{idDepartamento}','TdFlujoController@filtrarActividadesPorDepartamento');
    Route::get('mostrarTipoDocActivDeNodoFlujo/{idflujo}','TdFlujoController@mostrarTipoDocActivDeNodoFlujo');
});


Route::prefix('tramite')->group(function (){
    Route::resource('gestion','TdTramiteController')->middleware('auth');
    Route::get('verificarFlujoTipoTramite/{id?}','TdTramiteController@verificarFlujoTipoTramite');
    Route::get('detalleTramite/{iddetalle_tramite}', 'TdTramiteController@detalleTramite');
    Route::get('descargarDocumentos/{idtramite}', 'TdTramiteController@descargarDocumentos');
    Route::get('getAllDetalleTramiteAsociados/{iddetalle_tramite}', 'TdTramiteController@getAllDetalleTramiteAsociados'); // ruta para obtener todos los detalles de un tramite
});


Route::prefix('detalleTramite')->group(function(){
    Route::get('obtenerDocumentos/{iddetalle_tramite}', 'TdDetalleTramiteController@obtenerDocumentos'); //retorna los documentos generados en un detalle de tramite
    Route::post('gestion/{id}', 'TdDetalleTramiteController@update'); //actualiza o correige un tramite guardado
    Route::get('editarDetalleTramite', 'TdDetalleTramiteController@editarDetalleTramite'); //retorna la información de un tramite para editarlo
    Route::get('subirDetalleTramite/{iddetalle_tramite}', 'TdDetalleTramiteController@subirDetalleTramite'); //enviar un tramite a la bandeja del jefe

    Route::get('atenderDetalleTramite', 'TdDetalleTramiteController@atenderDetalleTramite'); // retorna la vista para atender un trámite
    Route::post('registrarAtencion/{iddetalle_tramite}', 'TdDetalleTramiteController@registrarAtencion');

    Route::get('terminarTramite', 'TdDetalleTramiteController@terminarTramite'); // retorna la vista para terminar un trámite
    Route::post('registrarTerminacion/{iddetalle_tramite}', 'TdDetalleTramiteController@registrarTerminacion'); // registra la terminación del trámite

    Route::post('devolverTramite/{iddetalle_tramite}', 'TdDetalleTramiteController@devolverTramite'); // registra la devolución de un trámite
    Route::get('revertirTramite/{iddetalle_tramite}', 'TdDetalleTramiteController@revertirTramite'); // ruta para revertir un trámite finalizado

    Route::delete('eliminar/{iddetalle_tramite}', 'TdDetalleTramiteController@eliminar'); // ruta para eliminar un detalle_tramite

    Route::get('recuperarTramite/{iddetalle_tramite}', 'TdDetalleTramiteController@recuperarTramite'); // ruta para devolver un trámite del departamento destino al departamento de origen

    Route::get('denegarTramite', 'TdDetalleTramiteController@denegarTramite'); // retorna la vista para denegar un trámite
    Route::post('registrarDenegacion/{iddetalle_tramite}', 'TdDetalleTramiteController@registrarDenegacion'); // registra la denegación del trámite


});

Route::prefix('revisionTramite')->group(function(){
    // Route::get('detalleTramite', 'RevisionTramiteController@detalleTramite');
    Route::get('verificarDocumentoFirmado/{iddetalle_tramite}', 'RevisionTramiteController@verificarDocumentoFirmado')->middleware('auth');
    Route::post('subirDocumentoFirmado/{iddetalle_tramite}','RevisionTramiteController@subirDocumentoFirmado')->middleware('auth');
    Route::get('aprobarDetalleTramite/{iddetalle_tramite}', 'RevisionTramiteController@aprobarDetalleTramite')->middleware('auth');
    Route::post('enviaraRevisionDetalleTramite/{iddetalle_tramite}', 'RevisionTramiteController@enviaraRevisionDetalleTramite')->middleware('auth');   
});

Route::prefix('firmaElectronica')->group(function(){
    Route::get('configurarFirma', 'FirmaElectronicaController@index')->middleware('auth');
    Route::post('guardarConfiguracion', 'FirmaElectronicaController@guardarConfiguracion')->middleware('auth');
    Route::get('eliminarArchivo/{idfirma_electronica}', 'FirmaElectronicaController@eliminarArchivo')->middleware('auth');
    Route::get('eliminarClave/{idfirma_electronica}', 'FirmaElectronicaController@eliminarClave')->middleware('auth');
});

Route::prefix('estadistica')->group(function(){
    Route::get('porUsuario', 'EstadisticaController@porUsuario')->middleware('auth');
    Route::get('filtrarEstadisticas/{idus001}/{fechaInicio}/{fechaFin}', 'EstadisticaController@filtrarEstadisticas')->middleware('auth');
});


// funcion para obtener un documento en base64
Route::get('/obtenerDocumento/{disco}/{documentName}', function($disco, $documentName){
    $documentEncode= base64_encode(\Storage::disk($disco)->get($documentName));
    return $documentEncode;
});

// funcion para buscar documentos en un disco
Route::get('/buscarDocumento/{disco}/{documentName}',function($disco, $documentName){
    
    //obtenemos la extension
    $info = new SplFileInfo($documentName);
    $extension = $info->getExtension();
    if($extension!= "pdf" && $extension!="PDF"){
        return \Storage::disk($disco)->download($documentName);
    }else{
        // obtenemos el documento del disco en base 64
        $documentEncode= base64_encode(\Storage::disk($disco)->get($documentName));
        return view("vistaPreviaDocumento")->with([
            "documentName"=>$documentName,
            "documentEncode"=>$documentEncode
        ]);        
    }
    

});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

//rutas bandeja de entrada de procesos
Route::prefix('gestionBandeja')->group(function(){
    Route::get('entrada', 'BandejaTramitesController@bandejaEntrada')->middleware('auth');
    Route::get('filtrarEntrada/{iddepartamento}/{idtipotramite}', 'BandejaTramitesController@filtrarEntrada')->middleware('auth');
    Route::get('enElaboracion', 'BandejaTramitesController@enElaboracion')->middleware('auth');
    Route::get('aprobarEnvio', 'BandejaTramitesController@aprobarEnvio')->middleware('auth');
    Route::get('enRevision', 'BandejaTramitesController@enRevision')->middleware('auth');
    Route::get('atendidosEnviados', 'BandejaTramitesController@atendidosEnviados')->middleware('auth');
    Route::get('filtrarAtendidoEnviado/{iddepartamento}/{idtipotramite}/{iniciado}', 'BandejaTramitesController@filtrarAtendidoEnviado')->middleware('auth');

    Route::get('finalizados', 'BandejaTramitesController@finalizados')->middleware('auth');
    Route::get('filtrarFinalizado/{iddepartamento}/{idtipotramite}', 'BandejaTramitesController@filtrarFinalizado')->middleware('auth');

    Route::get('cargarNotificacionBandejas', 'BandejaTramitesController@cargarNotificacionBandejas')->middleware('auth'); // ruta para cargar las bandejas
    Route::get('abrirTetalleTramite/{bandeja}/{iddetalle_tramite}','BandejaTramitesController@abrirTetalleTramite')->middleware('auth'); //ruta para abrir una bandeja y el detalle de un trámite en especifico
});


//*************************************************************************************************************
//*************************************************************************************************************
//*****************************  FIN DE RUTAS SISTEMA DE TRAMITES DEPARTAMENTALES  ****************************
//*************************************************************************************************************
//*************************************************************************************************************
