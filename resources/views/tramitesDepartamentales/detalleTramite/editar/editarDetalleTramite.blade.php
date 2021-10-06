@extends('layouts.service')
@section('contenido')



    <!-- iCheck -->
   <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    <!-- Datatables -->
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    
    {{-- estilos para la ventana de creacion de un tramite --}}
    <link href="{{asset('css/estilosTramite.css')}}" rel="stylesheet">

    <style type="text/css">
        .btn_regresar{
            margin-left: 15px; margin-left: 0px; font-size: 14px; font-weight: 700; color: #446684;
        }
    </style>

    @isset($detalle_tramite)

        @if($detalle_tramite->estado=="R")
            <a href="{{url('gestionBandeja/enRevision')}}" class="btn btn-default btn_regresar" style="margin-bottom: 0px;"><i class="fa fa-mail-reply-all"></i> Regresar</a>
        @else
            <a href="{{url('gestionBandeja/enElaboracion')}}" class="btn btn-default btn_regresar" style="margin-bottom: 0px;"><i class="fa fa-mail-reply-all"></i> Regresar</a>
        @endif

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 10px;">
                
                {{-- VARIABLES PARA ESTILOS --}}
                @php
                    $titulo = "EDITANDO EL TRÁMITE";
                    $bordepanel = "border-color: #446684;";
                @endphp

                {{-- NOTIFICACIÓN DE LA EDICIÓN --}}
                    @if($detalle_tramite->estado=="R")

                        @php
                            $titulo = "CORRIGIENDO EL TRÁMITE";
                            $bordepanel = "border-color: #3499b8;";
                        @endphp

                        <div class="panel panel-info-2" style="margin-bottom: 10px;">
                            <div class="panel-heading" style="padding: 5px 15px;">
                                <b style="font-size: 12px; text-transform: uppercase; font-size: 16px;">
                                    <i class="fa fa-wrench"></i> Descripción respecto a los cambios a realizar
                                </b>
                            </div>
                            <div class="panel-body" style="text-transform: uppercase;">
                                {{$detalle_tramite->detRevision}}
                            </div>
                        </div>

                        {{-- para saver a donde redireccionar cuando se suba el trámite --}}
                        <input type="hidden" id="ruta_redirect_subir" value="{{url('gestionBandeja/enRevision')}}">       
                    @else
                        {{-- para saver a donde redireccionar cuando se suba el trámite --}}
                        <input type="hidden" id="ruta_redirect_subir" value="{{url('gestionBandeja/enElaboracion')}}">                       
                    @endif
               

                {{-- CONTENIDO PARA EDITAR EL TRAMITE --}}
                <div class="x_panel" style="{{$bordepanel}} border-radius: 4px;">
                    <div class="x_title">
                        <h2> <b><i class="fa fa-edit"></i> {{$titulo}}</b> || {{$detalle_tramite->tramite->tipo_tramite->descripcion}} || {{$detalle_tramite->tramite->codTramite}}</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="margin-top:0">

                    <form id="frm_editarDetalleTramite" action="{{url('detalleTramite/gestion/'.$iddetalle_tramite)}}" method="POST"  enctype="multipart/form-data" class="form-horizontal form-label-left">
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



                        <div class="hr_divisor"></div>
                    
                        <div id="tramite_alerta_general" class="margin_content" style="display: none; user-select: text;"></div>

                        <div id="content_gestion_tramite">

                            <span id="content_btnTramie">
                                {{-- SI EL TIPO DE TRAMITE NO TIENE UN FLUJO DEFINIDO --}}                                
                                <button id="btn_guardar_borrador" class="btn btn-info btn-sm" type="submit"><i class="fa fa-save"></i> Guardar</button>
                                {{-- <button id="btn_cancelar_tramite" class="btn btn-info btn-sm" type="button"><i class="fa fa-thumbs-o-down"></i> Cancelar</button> --}}
                            </span>

                            <button id="btn_subir_tramite" onclick="subirDetalleTramiteEdit('{{$iddetalle_tramite}}')" class="btn btn-info btn-sm" type="button" data-toggle="tooltip" data-placement="top" title="Enviar al Jefe del Departamento">
                                <i class="fa fa-thumbs-o-up"></i> Subir trámite
                            </button>

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
                                        @include('tramitesDepartamentales.detalleTramite.editar.datosDelTramite')
                                        @include('tramitesDepartamentales.detalleTramite.editar.crearDocumento')
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="tab_adjuntar_documentos" aria-labelledby="profile-tab">
                                        @include('tramitesDepartamentales.detalleTramite.editar.adjuntarDocumento')
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

        @include('tramitesDepartamentales.detalleTramite.editar.ventanasModal')

    @else
        @include('error')
    @endisset


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
    <script src="{{asset('js/TramiteDepartamental/tramites/editarDetalleTramite.js')}}"></script>


@endsection