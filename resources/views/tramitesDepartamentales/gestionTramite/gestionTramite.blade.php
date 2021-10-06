@extends('layouts.service')
@section('contenido')



    <!-- iCheck -->
   <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    <!-- Datatables -->
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    
    {{-- estilos para la ventana de creacion de un tramite --}}
    <link href="{{asset('css/estilosTramite.css')}}" rel="stylesheet">

    {{-- <div class="row">
        <div class="col-md-12">
            <div class="title_left">
                <h3>Crear un nuevo Trámite</h3>
            </div>
            <br>
        </div>
    </div> --}}


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 10px;">
            <div class="x_panel">
                <div class="x_title">
                    <h2> <i class="fa fa-edit"></i> Definir Trámite</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="margin-top:0">

                <form id="frm_registrarTramite" action="{{url('tramite/gestion')}}" method="POST"  enctype="multipart/form-data" class="form-horizontal form-label-left" autocomplete="off">
                    {{ csrf_field() }}
                    <input id="method_flujo" type="hidden" name="_method" value="POST">

                    
                    <div class="contentInfUser">
                        <span class="textInfoUser">Usuario: </span>
                        @guest
                            No logueado
                        @else
                            <span class="labelInfoUser"><i class="fa fa-user"></i> {{ Auth::user()->name }}</span>
                            <span class="labelInfoUser">/</span>
                            <span class="labelInfoUser"><i class="fa fa-group"></i> {{departamentoLogueado()["tipoFP"]}}</span>
                            <span class="labelInfoUser">/</span>
                            <span class="labelInfoUser"><i class="fa fa-sitemap"></i> {{departamentoLogueado()["departamento"]}}</span>
                        @endguest
                    </div>


                    {{-- COMBO PARA SELECIONAR LA PROCEDENCIA DEL TRAMITE --}}
                    @if (!Auth::guest()) {{-- SI ESTA LOGUEADO --}}
                        @if (departamentoLogueado()["objdepartamento"]->tramite_externo == 1) {{-- MOSTRAMOS COMBO SOLO SI ES PERMITIDO LOS TRAMITES EXTERNOS--}}
                            <div id="content_select_procedencia" class="form-group" style="margin-top:10px;" >
                                <label class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12" >Procedencia <span class="required">*</span></label>
                                <div class="col-md-10 col-sm-10 col-xs-12 colpr0">
                                    <div class="chosen-select-conten">
                                        <select name="gf_select_procedencia" onchange="" id="gf_select_procedencia" class="chosen-select form-control" tabindex="5">        
                                            <option data-field-id="INT" value="{{encrypt('INT')}}">TRÁMITE INTERNO</option>
                                            <option data-field-id="EXT" value="{{encrypt('EXT')}}">TRÁMITE EXTENO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>                                 
                        @endif   
                    @endif

                    {{-- /COMBO PARA SELECIONAR LA PROCEDENCIA DEL TRAMITE --}}

                    {{-- COMBO DEL LA PRIORIDAD DEL TRÁMITE --}}

                    <div id="content_select_prioridad" class="form-group" style="margin-top:10px;" >
                        <label class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12" >Prioridad <span class="required">*</span></label>
                        <div class="col-md-10 col-sm-10 col-xs-12 colpr0">
                            <div class="chosen-select-conten">
                                <select name="gf_select_prioridad" id="gf_select_prioridad" onchange="" class="chosen-select form-control" tabindex="5">                            
                                    @isset($listaPrioridad)
                                        @foreach ($listaPrioridad as $prioridad)
                                            <option class="option_prioridad" value="{{encrypt($prioridad->idprioridad_tramite)}}">{{$prioridad->descripcion}}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        </div>
                    </div> 
                    
                    {{-- /COMBO DEL LA PRIORIDAD DEL TRÁMITE --}}

                    {{-- COMBO PARA SELECCIONAR EL TIPO DE TRAMITE --}}
                    <div id="content_select_tipo_tramite" class="form-group" style="margin-top:10px;" >
                        <label class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12" >Tipo de trámite <span class="required">*</span></label>
                        <div class="col-md-10 col-sm-10 col-xs-12 colpr0">
                            <div class="chosen-select-conten">
                                <select data-placeholder="Seleccione un tipo de trámite"  name="gf_select_tipo_tramite" onchange="" id="gf_select_tipo_tramite" class="chosen-select form-control" tabindex="5">        
                                    <option class="option_0" selected disabled value="0">Seleccione un tipo de trámite</option>
                                    @isset($listaTipoTramites)
                                        @foreach ($listaTipoTramites as $tipoTramite)
                                            <option class="option_tramite" value="{{encrypt($tipoTramite->idtipo_tramite)}}">{{$tipoTramite->descripcion}}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        </div>
                    </div> 
                    {{-- /COMBO PARA SELECCIONAR EL TIPO DE TRAMITE --}}


                    <div class="hr_divisor"></div>
                    
                    <div id="tramite_alerta_general" class="margin_content" style="display: none; user-select: text;"></div>
                

                    <span id="content_btnTramie" class="disabled_content">
                        <button id="btn_guardar_borrador" class="btn btn-info btn-sm" type="submit"><i class="fa fa-save"></i> Aceptar</button>
                        <button id="btn_cancelar_tramite" class="btn btn-info btn-sm" type="button"><i class="fa fa-thumbs-o-down"></i> Cancelar</button>
                    </span>
                        <button id="btn_subir_tramite" class="btn btn-info btn-sm" type="button" data-toggle="tooltip" data-placement="top" title="Enviar al Jefe del Departamento" style="display: none;">
                            <i class="fa fa-thumbs-o-up"></i> Subir trámite
                        </button>

                    <div id="content_gestion_tramite" class="disabled_content">

                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                            <ul id="myTab" class="nav nav-tabs bar_tabs  nav_tabs_tramite" role="tablist" style="margin-top: 5px; margin-right: 20px;">
                                <li role="presentation" class="active first_li">
                                    <a href="#tab_iniciar_tramite" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Información del trámite</a>
                                </li>
                                <li role="presentation" class="">
                                    <a href="#tab_adjuntar_documentos"role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Anexos <span id="num_documentos_req" style="padding: 1px 4px; display: none;" class="badge bg-red"></span></a>
                                </li>
                            </ul>
                            <div id="myTabContent" class="tab-content">
                                <div role="tabpanel" class="tab-pane fade active in" id="tab_iniciar_tramite" aria-labelledby="home-tab">
                                    @include('tramitesDepartamentales.gestionTramite.datosDelTramite')
                                    @include('tramitesDepartamentales.gestionTramite.crearDocumento')
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="tab_adjuntar_documentos" aria-labelledby="profile-tab">
                                    @include('tramitesDepartamentales.gestionTramite.adjuntarDocumento')
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
                {{-- End form --}}

                </div>
            </div>
        </div>
    </div>

    @include('tramitesDepartamentales.gestionTramite.ventanasModal')


    <!-- Datatables -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

    <!-- jQuery Smart Wizard -->
    <script src="../vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js"></script>

    {{-- Libreria Tinymce --}}
    <script src="{{asset('EditorTextoTinymce/js/tinymce/tinymce.min.js')}}"></script>

    <!-- iCheck -->
    <script src="../vendors/iCheck/icheck.min.js"></script>

    {{-- Libreria js para la creacion de un tramite --}}
    {{-- <script src="{{asset('js/TramiteDepartamental/gestionTramites.js')}}"></script> --}}
    <script src="{{asset('js/TramiteDepartamental/tramites/gestionTramites.js')}}"></script>


@endsection