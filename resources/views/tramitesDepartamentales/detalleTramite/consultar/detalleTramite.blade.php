
    <style type="text/css">
    
        .td_titulo{
            /* background-color: #ddd; */
            background-color: #446684;
            color: #ffff;
            font-weight: 600;
            text-align: right;
            padding-right: 10px;
            width: 200px;
            text-transform: uppercase;
        }

        .tabla_informacion{
            border: 1px solid #446684;
            color: black; 
            margin-bottom: 0px;
            border-width: 0px 1px 1px 1px;
        }

        .tabla_informacion tbody tr td{
            border: 1px solid #446684;
            font-weight: 600;
        }

        .border_titulo{
            border-top-color: #e6e9ed8c !important;
            border-top-style: solid;
            border-top-width: 1px;
        }

        /* VISTA PAR VENTANAS PEQUEÑAS Y TABLETAS O CELULAR EN ORIZONTAL */
        @media screen and (max-width: 1160px) and (min-width: 767px){

            ul.nav_tabs_tramite li{
                margin-top: -24px !important;
                /* width: 90%; */
            }

            ul.nav_tabs_tramite li a{
                padding: 5px 17px !important;
                width: 100px !important;
            }      
        }


        /* ESTILO SOLO PARA CELULAR VERTICAL */
        @media screen and (max-width: 767px){


            ul.nav_tabs_tramite li a{
                padding: 5px 10px !important;
            }

            ul.nav_tabs_tramite li a span{
                display: none !important;
            }

            ul.nav_tabs_tramite li a i{
                display: inline !important;
                font-size: 20px;
                /* margin: 5px; */
            }

        }

    </style>

    <div class="row center-block">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div  class="panel panel-dark">
                <div class="panel-heading"><b>TRÁMITE || <span id="codigo_tramite">GAD-000000-00</span></b></div>
                <div class="panel-body" style="margin-top: 10px;">

                    <div role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTab" class="nav nav-tabs bar_tabs  nav_tabs_tramite" role="tablist" style="margin-top: 5px; margin-right: 20px;">

                            <li role="presentation" class="active first_li">                            
                                <a href="#informacion_documento" id="tab_informacion_documento" role="tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fa fa-file-pdf-o" style="display: none;"></i>
                                    <span>Información Documento</span>
                                </a>
                            </li>
                            <li role="presentation" class="">                        
                                <a id="a_datos_generales" href="#datos_generales" id="datos_generales_tab" role="tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fa fa-info-circle" style="display: none;"></i>
                                    <span>Datos Generales</span>
                                </a>
                            </li>
                            <li role="presentation" class="">                            
                                <a href="#datos_documentos" role="tab" id="documentos-tab" data-toggle="tab" aria-expanded="false">
                                    <i class="fa fa-bars" style="display: none;"></i>
                                    <span>Todos los Documentos</span>    
                                </a>
                            </li>
                            <li role="presentation" class="">                            
                                <a href="#historial" role="tab" id="historial_tramite" data-toggle="tab" aria-expanded="false">
                                    <i class="fa fa-line-chart" style="display: none;"></i>
                                    <span>Historial Trámite</span>
                                </a>
                            </li>

                        </ul>
                        
                        <div class="tab-content">

                            {{-- DATOS DEL DETALLE TRAMITE --}}
                                <div id="informacion_documento" role="tabpanel" class="tab-pane fade active in" aria-labelledby="home-tab">
                                    <div class="panel-body" style="padding-top: 0px;">
                                        @include('tramitesDepartamentales.detalleTramite.consultar.informacionDocumento')                         
                                    </div>
                                </div>
                            {{-- / --}}
                            
                            {{-- CONTENIDO DEL DATOS DEL TRAMITE --}}
                                <div id="datos_generales" role="tabpanel" class="tab-pane fade" aria-labelledby="home-tab">
                                    <div class="panel-body" style="padding-top: 0px;">
                                        @include('tramitesDepartamentales.detalleTramite.consultar.datosGenerales')                                         
                                    </div>
                                </div>
                            {{-- / --}}

                            {{-- CONTENIDO DE TODOS LOS DOCUMENTOS ADJUNTOS AL TRÁMITE --}}
                                <div id="datos_documentos" role="tabpanel" class="tab-pane fade" aria-labelledby="home-tab">
                                    <div class="panel-body" style="padding-top: 0px;">
                                        @include('tramitesDepartamentales.detalleTramite.consultar.documentosTramite')                         
                                    </div>
                                </div>
                            {{-- / --}}

                            {{-- VISTA PREVIA DEL FLUJO --}}
                                <div id="historial" role="tabpanel" class="tab-pane fade" aria-labelledby="home-tab">
                                    <div class="panel-body table-responsive">
                                        @include('tramitesDepartamentales.detalleTramite.consultar.historial')                                                                                     
                                    </div>
                                </div>
                            {{-- / --}}                   

                        </div>
                    </div>

                </div>        
            </div>
        </div>
    </div>


    {{-- VISTA PREVIA DEL DOCUMENTO --}}
    
    <div id="modal_vista_previa_documento" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-file-pdf-o"></i> Vista previa del documento principal</h4>
                </div>
                <div class="modal-body">

                <div id="content_visualizarDocumento"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>


    {{-- libreria para mostrar los detalles del trámite --}}
    <script src="{{asset('js/TramiteDepartamental/tramites/detalleTramite.js')}}"></script>