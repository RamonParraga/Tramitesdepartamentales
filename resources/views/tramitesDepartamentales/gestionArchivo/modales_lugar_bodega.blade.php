    {{-- LISTA DE LIBRERIAS NECESARIAS --}}
    <!-- iCheck -->
    <link href="{{asset('vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">
    
    <!-- MODAL PARA AGREGAR LOS TIPOS DE DOCUMENTOS -->
    <div class="modal fade modal_Bodega" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Seleccione una bodega</h4>
            </div>
            <div class="modal-body">
                
            <!-- Lista de tipo de documentos -->
                <div class="">
                    <div class="x_panel" style="margin-bottom: 0;">

                      <div class="x_title">
                        <h2><i class="fa fa-book"></i> Lista <small>Bodega</small></h2>
                        <div class="clearfix"></div>
                      </div>

                      <div id="lista_tipo_documentos_modal" class="x_content div_scroll_select_doc_act">
                        
                        
                        @isset($listaSeccion)
                            <ul class="to_do">
                                @foreach ($listaSeccion as $listaSeccion)                                    
                                    <li id="{{'li_lugar_bod_'.$listaSeccion->id_seccion}}" class="li_tipo_documento">
                                        <label class="label_doc_act_select" for="{{'tipo_doc_'.$listaSeccion->id_seccion}}">
                                            <input type="checkbox" name="chk"id="{{'tipo_doc_'.$listaSeccion->id_seccion}}" value="{{$listaSeccion->id_seccion}}" class="flat td_seleccionado"> {{$listaSeccion->sector->bodega['nombre']}} - {{$listaSeccion->sector['descripcion']}} - {{$listaSeccion->descripcion}}
                                        </label>                                
                                    </li>                                    
                                @endforeach
                            </ul>
                        @endisset
                
                      </div>
                    </div>
                  </div>
            <!-- Fin de la lista -->
            <label for="" id="pruebaxdxdd"></label>

            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                    {{-- <button type="button" id="btn_agregar_tipo_documentos" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</button> --}}
                </form>
            </div>

            </div>
        </div>
    </div>


    <!-- MODAL PARA AGREGAR LAS ACTIVIDADES -->
    <div class="modal fade modal_addActividades" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Agregar actvidades a realizar</h4>
            </div>
            <div class="modal-body">

                <!-- Lista de tipo de documentos -->
                    <div class="">
                        <div class="x_panel" style="margin-bottom: 0;">

                        <div class="x_title">
                            <h2><i class="fa fa-tasks"></i> Lista <small>Actividades</small></h2>
                            <div class="clearfix"></div>
                        </div>

                        <div id="lista_actividades_modal" class="x_content div_scroll_select_doc_act">

                            @isset($listaActividades)
                                <ul class="to_do">
                                    @foreach ($listaActividades as $actividad)
                                        <li id="{{'li_actividad_'.$actividad->idactividad}}" class="hidden li_actividad">
                                            <label class="label_doc_act_select" for="{{'actividad_'.$actividad->idactividad}}">
                                                <input type="checkbox" id="{{'actividad_'.$actividad->idactividad}}" value="{{$actividad->idactividad}}" class="flat a_seleccionado"> {{$actividad->descripcion}}
                                            </label> 
                                        </li>                                
                                    @endforeach
                                </ul> 
                            @endisset

                        </div>
                        </div>
                    </div>
                <!-- Fin de la lista -->                    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Salir</button>
                <button type="button" id="btn_agregar_actividades" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</button>
            </div>

            </div>
        </div>
    </div>


    <!-- MODAL PARA MOSTRAR LOS TIPO DE DOCUMENTOS Y ACTIVIDADES DE UN NODE DE UN FLUJO -->
    <div class="modal fade modal_mostarInfoNodoFLujo" id="modal_mostarInfoNodoFLujo" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
                {{-- <h4 class="modal-title" id="myModalLabel">Información del nodo de un flujo</h4> --}}
                <h4> <b>DEPARTAMENTO: </b> <span id="modal_nombre_departamento">DEPARTAMENTO DE PRUEBA</span></h4>
            </div>
            <div id="mensajeCargandoActDoc" style="margin: 20px 15px 0px 15px;">
                {{-- spinner se carga con jquery --}}
            </div>
            <div class="modal-body" id="body_modal_gestion_nodo" style="font-size: 15px;">

                <div id="mostrar_listaTipoDocumentos">       
                             
                    <p style="float: left; margin-right: 10px; margin-bottom: 0px;"><i class="fa fa-book"></i> <strong> Documentos a realizar</strong></p><br>
                    <hr style="margin-bottom:10px; margin-top:5px;">

                    <div id="mostrar_listaTipoDocumentos_mensaje" class="div_informacion hidden">
                        <i class="fa fa-info-circle"></i> No hay tipos de documento agregados
                    </div>

                    {{--lista documentos  --}}
                    <ul id="contenedor_doc_mostrar" class="div_scroll_doc_act">
                        {{-- EL CONTENIDO SE CARGA CON JQUERY --}}
                    </ul>

                </div> 
                <br>
                <div id="mostrar_listaActividades">       
                               
                    <p style="float: left; margin-right: 10px; margin-bottom: 0px;"><i class="fa fa-tasks"></i><strong> Actividades a realizar</strong></p><br>
                    <hr style="margin-bottom:10px; margin-top:5px;">
                    
                    <div id="mostrar_listaActividades_mensaje" class="div_informacion hidden">
                        <i class="fa fa-info-circle"></i> No hay actividades agregadas
                    </div>

                    {{--lista actividades  --}}
                    <ul id="contenedor_act_mostrar" class="div_scroll_doc_act">
                        {{-- EL CONTENIDO SE CARGA CON JQUERY --}}
                    </ul>

                </div> 
    
            </div>

            <div class="modal-footer">
                <form id="frm_flujo_eliminar" action=""  method="POST">
                    {{csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                    <button type="button" id="btn_eliminarFlujo" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</button>
                    <button type="button" id="btn_agregarNodo" data-dismiss="modal" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Nodo</button>
                </form>
            </div>

            </div>
        </div>
    </div>
    

    {{-- LIBRERIAS NECESARIAS --}}

    <!-- iCheck -->
    <script src="{{asset('vendors/iCheck/icheck.min.js')}}"></script>

    <script>
        
      

    </script>
