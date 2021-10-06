    
    {{-- INCLUIMOS LA LIBRERIA PARA GRAFICAR EL FLUJO --}}

        {{-- libreria en linea (opcional) --}}
            {{-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript">
                google.charts.load('current', {packages:["orgchart"]});
            </script> --}}

        {{-- libreria local --}}
            <link rel="stylesheet" href="{{asset('gstatic_charts/charts/47/css/core/tooltip.css')}}">
            <link rel="stylesheet" href="{{asset('gstatic_charts/charts/47/css/util/util.css')}}">
            <link rel="stylesheet" href="{{asset('gstatic_charts/charts/47/css/orgchart/orgchart.css')}}">
            <script type="text/javascript" src="{{asset('gstatic_charts/charts/47/js/loader.js')}}"></script>

            <script type="text/javascript" src="{{asset('gstatic_charts/charts/47/js/jsapi_compiled_format_module.js')}}"></script>
            <script type="text/javascript" src="{{asset('gstatic_charts/charts/47/js/jsapi_compiled_default_module.js')}}"></script>
            <script type="text/javascript" src="{{asset('gstatic_charts/charts/47/js/jsapi_compiled_ui_module.js')}}"></script>
            <script type="text/javascript" src="{{asset('gstatic_charts/charts/47/js/jsapi_compiled_orgchart_module.js')}}"></script>


    {{-- ESTILOS PADA EDITAR DICHA LIBRERIA --}}

        <style type="text/css">
        
            .content_title_detalle{
                padding: 5px 8px;
                background: #446684;
                color: #fff;
                text-transform: uppercase;
                    text-align: left;
                    line-height: normal;
                font-weight: 500;
                border-radius: 3px 3px 0 0;
            }

            .organigram button{
                float: left;
                margin-top: 8px;
                margin-bottom: 10px;
            }

            .organigram table{
                    border-collapse: initial !important;
            }

            .organigram .content_info_detalle{
                padding: 5px 8px;
            }
            .organigram .content_info_detalle b{
                white-space: nowrap;
            }

            .organigram .info_detalle{
                color: #73879C;
            }

            /* estilos de google */
            .google-visualization-orgchart-node {
                text-align: center;
                vertical-align: middle;
                font-family: arial,helvetica;
                cursor: default;
                border: 2px solid #ffffff;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
                -webkit-box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.3);
                -moz-box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.3);
                box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.3);
                background-color: #ffffff;
                background: -webkit-gradient(linear, left top, left bottom, from(#ffffff), to(#ffffff));
                border:0px;
                padding: 0px;
                max-width: 220px !important;
            }

        </style>


    <div id="flujo_proceso" class="organigram" style="font-size: 18px !important;"></div>


    {{-- VENTANA MODAL PARA CARGAR LOS DOCUMENTOS --}}

      <div id="modal_detalle_tramite_documentos" data-backdrop="static" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Lista de documentos</h4>
                </div>
                <div class="modal-body">
            
                    <div class=" table-responsive">
                        <table style="color: black; margin-bottom: 0px;" class="table table-row-center-vertical table-bordered dataTable no-footer table-row-center-vertical" role="grid" aria-describedby="datatable_info">
                            <thead>
                                <tr role="row">                                                       
                                    <tr>
                                        <th>Tipo Documento</th>
                                        <th>Fecha</th>
                                        <th>Código</th>
                                        <th>Descripción</th>  
                                        <th style="width: 10px;">Nivel</th>                       
                                        <th style="width: 10px;">Ver</th>
                                    </tr>
                                </tr>
                            </thead>         
                            
                            <tbody id="tbody_detalle_tramite_documentos">
                                <tr>
                                    <td colspan="5">
                                        <center>No hay documentos</center>
                                    </td>
                                </tr>
                                {{-- EL CONTENIDO SE CARGA CON JQUERY --}}
                            </tbody>

                        </table>                            
                    </div>

                    <div id="content_visualizarDocumento_depa"></div>

                    <div class="modal-footer" style="padding-bottom: 0;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
