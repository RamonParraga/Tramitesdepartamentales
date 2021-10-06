
    {{-- estilos para la ventana de creacion de un tramite --}}
    <link href="{{asset('css/estilosTramite.css')}}" rel="stylesheet">

    <style type="text/css">
        .separardor{
            color: #38a7c9;
            font-weight: 800;
            margin-right: 4px;
        }

        .separador_i{
            margin-right: 15px;
        }
    </style>


    <div class="panel panel-info-2" style="margin-bottom: 8px;">
        <div class="panel-heading" style="font-size: 13px !important;">Solo puede subir archivos con un tamaño maximo de <span class="mostrar_tamMaxDoc"></span>MB</div>
            <div class="panel-body">
                
                <div id="cont_btn_adjuntar_doc">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="control-label col-md-1 col-sm-1 col-xs-12" style="padding:0;"></label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <div id="mensajeInfoDoc" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <center style="padding-bottom: 10px;">
                                <button type="button" onclick="adjuntarNuevoDocumento()" class="btn btn-outline-info" style="margin-right: 0px;"><b> <i class="fa fa-upload"></i> Seleccionar archivo</b></button>
                            </center>
                            
                        </div>                    
                    </div>
                                        
                </div>

                <div id="cont_lista_documentos_adjuntos" style="display: none; margin-top: -10px;">
            
                    <div class="form-group" style="margin-bottom: 8px; margin-top: 25px;">
                        <div id ="" class="col-md-12 col-sm-12 col-xs-12">
                            <p style="float: left; margin-right: 10px; margin-bottom: 0px; font-size: 15px;">
                                <i class="fa fa-align-left"></i> Lista de documentos adjuntos
                            </p>
                            <hr style="margin-top: 10px; margin-bottom: 0; ">
                        </div>
                    </div> 
            
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div id="lista_documentos_adjuntos" class="div_scroll_doc_act" style="max-height: 400px !important;"> 
                                {{-- CODIGO SE CARGA CON JQUERY --}}
                            </div>                                 
                        </div>
            
                    </div>         
                </div>

            </div>
        </div>
    </div>









    {{-- VISTA PARA LA VISTA PREVIA DE DOCUMENTO ADJUNTO --}}

    <div id="modalVistaPreviaDocumento" data-backdrop="static" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
    
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
              </button>
              <h4 class="modal-title" id="modal_nombreDocumentoSeleccionado">Documento</h4>
            </div>
            <div class="modal-body">
    
                <div class="form-horizontal form-label-left">
    
                    <div id="VistaPreviaDoc" style="border-top: 1px solid #e5e5e5;">
    
                    </div>

                    <br>

                    <div class="form-group" id="content_tipo_documento">
                        <label for="cmb_tipo_documento_docAdj" class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12">Crear Documento<span class="required">*</span></label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            
                            <div class="chosen-select-conten">
                                <select data-placeholder="No hay documentos para crear" id="cmb_tipo_documento_docAdj" onchange="" class="cmb_tipo_documento chosen-select form-control" tabindex="5">
                                   
                                    @isset($listaTipoDocumentos)
                                        @foreach ($listaTipoDocumentos as $tipo_documento)                                        
                                            <option value="{{$tipo_documento->idtipo_documento_encrypt}}">{{$tipo_documento->descripcion}}</option>                                                                   
                                        @endforeach                                        
                                    @endisset

                                </select>
                            </div>
        
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="tramite_asunto">Código: </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <input type="text" id="modal_codigo_docAdj" placeholder="Ejm: GADM-0000-00-00000" class="form-control col-md-7 col-xs-12 sinespecial" style="text-transform: uppercase;">
                            <span class="sinespecialMsj"></span>
                        </div>
                    </div>
    
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="tramite_asunto">Descripción: </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <input type="text" id="modal_descripcion_docAdj" placeholder="Ejm: Memorandum" class="form-control col-md-7 col-xs-12 sinespecial">
                            <span class="sinespecialMsj"></span>
                        </div>
                    </div>
    
                </div>
                
    
                <input type="hidden" id="modal_id_documento_adjunto">
                <input type="hidden" id="modal_nameFile">
    
    
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cancelarDocumento()">Cancelar</button>
              <button type="button" class="btn btn-primary" onclick="agregarDocumento()">Agregar Documento</button>
            </div>
    
          </div>
        </div>
      </div>

    
    
    {{-- MODAL PARA VISUALIZAR UN DOCUMENTO --}}
   
   <div class="modal fade modal_visualizarDocumento" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-file-pdf-o"></i> Vista previa del documento</h4>
                </div>
                <div class="modal-body">

                <div id="content_visualizarDocumento"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>

            </div>
        </div>
    </div>