@extends('layouts.service')
@section('contenido')


    {{-- LIBRERIAS PARA GRAFICAR ORGANIGRAMA --}}
        <link rel="stylesheet" href="{{asset('BasicPrimitives/packages/jquery-ui/jquery-ui.min.css')}}" />
        <script type="text/javascript" src="{{asset('BasicPrimitives/packages/jquery-ui/jquery-ui.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('BasicPrimitives/min/primitives.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('BasicPrimitives/min/primitives.jquery.min.js')}}"></script>
		<link href="{{asset('BasicPrimitives/min/primitives.latest.css?2106')}}" media="screen" rel="stylesheet" type="text/css" />		
		<script type="text/javascript" src="{{asset('BasicPrimitives/packages/jquerylayout/jquery.layout-latest.min.js')}}"></script>
    {{-- FIN --}}

    <!-- PNotify -->
        <link href="{{asset('vendors/pnotify/dist/pnotify.css')}}" rel="stylesheet">
        <link href="{{asset('vendors/pnotify/dist/pnotify.buttons.css')}}" rel="stylesheet">
        <link href="{{asset('vendors/pnotify/dist/pnotify.nonblock.css')}}" rel="stylesheet">

        <script src="{{asset('vendors/pnotify/dist/pnotify.js')}}"></script>
        <script src="{{asset('vendors/pnotify/dist/pnotify.buttons.js')}}"></script>
    {{-- FIN --}}

    <style type="text/css">
        
        /* ESTILOS PARA MOBIL */
        @media screen and (max-width: 1044px){
            .form-group .btn{
                margin-bottom: 5px !important;
                width: 100%;
            }
        }
    </style>
    
    @if (session()->has('old_tipo_tramite'))
        {{-- input para almacenar el id del tipo de tramite seleciconado despues de un return back --}}
        <input type="hidden" id="old_tipo_tramite" value="{{session('old_tipo_tramite')}}">
        <script type="text/javascript">
            $(document).ready(function(){
                // obtenemos el id del tipo de tramite
                var id_tipo_tramite = $("#old_tipo_tramite").val(); 
                // volvemos a seleccionar el tipo de tramite en el combo
                $(`#gf_select_tipo_tramite option[value="${id_tipo_tramite}"]`).attr("selected", true);
                // llamamos la funcion para cargar el combo de departamentos pagre
                cargar_cmb_departamentos($("#gf_select_tipo_tramite"));
            });
        </script>
    @endif


        {{-- MENSAJES DE INFORMACION --}}
        @if (session()->has('mensajeInfo'))
            <script type="text/javascript">
                $(document).ready(function () {
                    PNotify.removeAll();
                    new PNotify({
                        title: 'Mensaje de Información',
                        text: '{{session('mensajeInfo')}}',
                        type: '{{session('mensajeColor')}}',
                        hide: true,
                        delay: 4000,
                        styling: 'bootstrap3',
                        addclass: ''
                    });
                });
            </script>            
        @endif
    {{-- FIN --}}

    <div class="row">
        <div class="col-md-12">
            <div class="title_left">
                <h3>Gestionar Flujo General de Trámites</h3>
            </div>
            <br>
        </div>
    </div>

    <div class="row" id="administador_departamentos">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" id="f_contenedor_general">
                <div class="x_title">
                    <h2> <i class="fa fa-edit"></i> Definir Flujo</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form id="frm_flujo_gestion" method="POST" action="{{url('flujo/gestion')}}"  enctype="multipart/form-data" class="form-horizontal form-label-left">
                        {{csrf_field()}}
                        <input id="method_flujo" type="hidden" name="_method" value="POST">


                        {{-- COMBO PARA CARGAR TODOS LOS TIPO DE TRAMITE --}}
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="departamento_padre">Tipo Trámite <span class="required">*</span></label>
                            <div id ="conten_select_tipo_tramite" class="col-md-7 col-sm-7 col-xs-12">
                                <div class="chosen-select-conten">
                                    <select data-placeholder="Seleccione un tipo de trámite"  name="gf_select_tipo_tramite" onchange="cargar_cmb_departamentos(this)" id="gf_select_tipo_tramite" class="chosen-select form-control" tabindex="5">        
                                        <option class="gf_select_tipo_tramite" value=""></option>
                                        @isset($listaTipoTramite)
                                            @foreach ($listaTipoTramite as $tipoTramite)
                                                <option class="gf_select_tipo_tramite" value="{{$tipoTramite->idtipo_tramite}}">{{$tipoTramite->descripcion}} </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>
                        </div>


                        {{-- SOLO MOSTRAMOS CUANDO SE VA A AGREGAR UN NODO AL FLUJO DEL TRAMITE SELECCIONADO --}}

                        <div id="contentAgregarNodoFlujo" style="display: none">

                            <input type="hidden" id="id_flujo_padre" name="id_flujo_padre"> {{-- para almacenar el id nodo padre del nuevo nodo por defecto 0 --}}

                            <div class="form-group" style="margin-bottom: 5px; margin-top: 20px;">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="departamento_padre"></label>
                                <div id ="" class="col-md-7 col-sm-7 col-xs-12">
                                    <p for="" style="float: left; margin-right: 10px;"><span id="mensajeAgregarNodo">Agregar Nodo</span></p>
                                    <hr style="margin-top: 10px; margin-bottom: 10px; ">
                                </div>
                            </div> 

                            
                            {{-- COMBO PARA CARGAR LOS DEPARTAMENTOS DESTINO --}}
                            
                            <div class="form-group"  id="contet_cmb_departamento">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="departamento_padre">Departamento <span class="required">*</span></label>
                                <div id ="conten_select_departamento_destino" class="col-md-7 col-sm-7 col-xs-12">
                                    <div class="chosen-select-conten">
                                        <select data-placeholder="Seleccione un departamento destino"  name="gf_select_departamento_destino" id="gf_select_departamento_destino" class="chosen-select form-control" tabindex="5">        
                                            <option class="gf_select_departamento_destino idperiodo_0" value="">Seleccione un departamento destino</option>
                                        </select>
                                    </div>                                
                                </div>
                            </div>


                            {{-- INPUT PARA REGISTRAR LA HORA MAXIMA --}}
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="">Hora Máxima <span class="required">*</span></label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    {{-- required="required" --}}
                                    <input type="number" id="gf_hora_maxima" name="gf_hora_maxima" min="0" value="12" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="">Tipo Envio <span class="required">*</span></label>
                                <div class="col-md-7 col-sm-7 col-xs-12" style="padding-top: 5px;">
                                    <label for="enviar_para">PARA: </label> <input type="radio" class="flat" name="tipo_envio" id="enviar_para" value="P" checked="" required /> 
                                    <label for="enviar_copia" style="margin-left: 8px;">COPIA: </label> <input type="radio" class="flat" name="tipo_envio" id="enviar_copia" value="C" />                              
                                </div>
                            </div>


                            <div id="content_finalizar_add_cod_act">

                                {{-- INPUT PARA INDICAR QUE SE DESEA FINALIZAR UN FLUJO --}}
                                <div class="form-group"  style="user-select: none;">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12 ">
                                        <label class="label_finaliar_fujo " for="input_flujo_finalizar">
                                            <input type="checkbox" id="input_flujo_finalizar" name="input_flujo_finalizar" class="flat td_seleccionado"> <strong>Finalizar el flujo</strong>
                                        </label> 
                                    </div>                               
                                </div>

                                {{-- BOTONES DE AGREAR DOCUMENTOS Y ACTIVIDADES --}}
                                <div class="form-group" style="user-select: none;">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <button type="button" id="btn_modal_tipo_documentos" disabled class="btn btn-outline-primary" onclick="" data-toggle="modal" data-target=".modal_addTipoDocumento"><i class="fa fa-file-pdf-o"></i> Agregar Documentos</button>
                                        <button type="button" id="btn_modal_actividades" disabled class="btn btn-outline-primary" data-toggle="modal" data-target=".modal_addActividades"><i class="fa fa-bookmark"></i> <span id="text_btn_actividades"> Sin Actividades</span></button>
                                    </div>
                                </div>

                            </div>





                            {{-- DIV PARA CARGAR LOS DOCUMENTOS Y ACTIVIDADES QUE SE ASIGNARA A UN NODO DE UN FLUJO --}}
                            
                            <div id="contenedor_doc_activ" class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="departamento_padre"></label>
                                <div class="col-md-7 col-sm-7 col-xs-12"> 
                                    
                                    <div id="area_listaDocumentos" style="display: none;">       
                                        <div class="form-group" style="margin-bottom: 5px; margin-top: 15px;">           
                                            <p for="" style="float: left; margin-right: 10px; margin-bottom: 0px;"><i class="fa fa-align-left"></i> Documentos Requeridos</p>
                                            <hr style="margin-top: 10px; margin-bottom: 10px; ">
                                        </div>
                                        {{--lista documentos  --}}
                                        <div id="contenedor_doc" class="div_scroll_doc_act">
                                            {{-- EL CONTENIDO SE CARGA CON JQUERY --}}
                                        </div>
                                    </div> 

                                    
                                    <div id="area_listaActividades" style="display: none;">
                                        <div class="form-group" style="margin-bottom: 5px; margin-top: 15px;">                                    
                                            <p for="" style="float: left; margin-right: 10px;  margin-bottom: 0px;"><i class="fa fa-align-left"></i> Actividades a realizar</p>
                                            <hr style="margin-top: 10px; margin-bottom: 10px; ">                              
                                        </div> 
                                        {{--lista actividades  --}}
                                        <div id="contenedor_activ"  class="div_scroll_doc_act">
                                            {{-- EL CONTENIDO SE CARGA CON JQUERY --}}
                                        </div>
                                    </div>

            
                                </div>   
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button id="btn_flujo_registrar" style="padding: 6px 48px;" type="submit" class="btn btn-success"><i class='fa fa-cloud-upload'></i> Registrar</button>
                                    <button id="btn_flujo_cancelar"  style="padding: 6px 40px;"  type="button" class="btn btn btn-dark hidden" ><i class="fa fa-close"></i> Cancelar</button>                                   
                                </div> 
                            </div>
                            
                        </div>

                        
                        {{-- IMAGEN PRINCIPAL (ICONO DE FLUJO) --}}
                        <div id="imagenInicioLogoFLujo" style="opacity: 0.2; padding-top: 25px;">
                            <center>
                                <img src="{{asset('images/iconFlujo1.png')}}" style="width: 28%">
                            </center>
                        </div>

                    </form>

                    <br>


                    <div id="contenedor_grafico_flujo" style="display: none;">
                        <hr>
                        <h2><i class="fa fa-line-chart"></i> Flujo del Tipo de Trámite: <b><span id="tittleTipoTramite"></span></b></h2>
                        <div id="graficoFlujo" style="overflow: hidden; border-style: ridge; border-width: 1px;"></div>
                    </div>                       

                    
                </div>
            </div>
        </div>
    </div>


    {{-- modales para agregar tipos de documento y las actividades  --}}
    @include('tramitesDepartamentales.gestionFlujo.modales_doc_act')

    {{-- vista del grafico del fujo de un tramite --}}
    @include('tramitesDepartamentales.gestionFlujo.graficoFlujo')

	<script type="text/javascript" src="{{asset('js/TramiteDepartamental/gestionFlujos.js')}}"></script>

@endsection