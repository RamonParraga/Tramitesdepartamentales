
    <style type="text/css">
        .separardor{
            color: #38a7c9;
            font-weight: 800;
            margin-right: 4px;
        }

        .separador_i{
            margin-right: 15px;
        }

        .doc_requeridos{
            margin-left: 8px;
        }
    </style>


    <div class="panel panel-default">
        <div class="panel-heading">Solo puede subir archivos con un tamaño maximo de <span class="mostrar_tamMaxDoc"></span>MB</div>
            <div class="panel-body">
                
                {{-- vista previa de documentos requeridos --}}
                <div id="content_documentos_requeridos" style="display: none;">
                    <label for="">Documentos requeridos:</label>
                    <span id="documentos_requeridos">
                        {{-- contenido se carga con jquery --}}
                    </span>
                </div>
                
                {{-- si tiene flujo definido cargamos la informacion de los documentos requeridos --}}
                @if($flujo)
                    
                    <script class="scrypt_delete" type="text/javascript">
                        $(document).ready(function(){
                            var list_idtipodoc = $("#lista_documentos_adjuntos").find('.idtipo_documento_adj');

                            $.each(list_idtipodoc, function (index, idtipodoc) { 
                                var idtipo_documento_encrypt = $(idtipodoc).val();
                                var desc_tipo_documento = $(idtipodoc).parent().siblings('.infoDoc').children('.tipo_documento_adj').html();
                                $("#documentos_requeridos").append(`<code style="display:none;" id="tipo_requerido_${idtipo_documento_encrypt.substr(0, 40)}" class="doc_requeridos">${desc_tipo_documento}</code>`);
                            });
                            
                        });
                    </script>
                @endif

                <div id="cont_btn_adjuntar_doc">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="control-label col-md-1 col-sm-1 col-xs-12" style="padding:0;"></label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <div id="mensajeInfoDoc" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <center>
                                <button type="button" onclick="adjuntarNuevoDocumento()" class="btn btn-primary" style="margin-right: 0px;"><i class="fa fa-upload"></i> Seleccionar archivo</button>
                            </center>
                        </div>                    
                    </div>
                                        
                </div>


                @if(sizeof($listaDocumentoAdjunto)>0)
                    <div id="cont_lista_documentos_adjuntos" style="margin-top: -10px;">
                
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
                                
                                    
                                    @foreach ($listaDocumentoAdjunto as $documento)
                                        @php
                                            $codigo_docAdj = $documento->codigoDocumento;
                                            $descripcion_docAdj = $documento->descripcion;    
                                            $iddocumento_encrypt = encrypt($documento->iddocumento);
                                            $nombreDocumentoFisico = $documento->rutaDocumento;
                                            $extension = $documento->extension;

                                            $descripcion_tipo_documento_adj = $documento->tipo_documento->descripcion;
                                            $idtipo_documento_adj_encrypt = encrypt($documento->tipo_documento->idtipo_documento);
                                        @endphp
                                        <div class="alert active_documento f_documento_adjunto fade in docActivo" style="margin-bottom: 5px;">
                                            <button type="button" class="btn btn-danger btn-sm btn_doc_creado" onclick="quitarDocumentoAdjunto(this)"><i class="fa fa-trash"></i></button>
                                            
                                            @if($documento->extension == "pdf" || $documento->extension=="PDF")
                                                 <button type="button" onclick="visualizarDocumentoAdjunto_editar('{{$nombreDocumentoFisico}}')" class="btn btn-primary btn-sm btn_doc_creado"><i class="fa fa-eye"></i></button>
                                            @endif
                                           
                                            <strong><i class="icono_left fa fa-file-pdf-o"></i></strong> 
                                            <span class="nameFile"><b>{{$nombreDocumentoFisico}}.{{$extension}}</b></span>
                            
                                            <hr style="margin: 10px 0px 0px 0px; border-top: 1px solid #48aecd;">
                                            <div class="infoDoc" style="margin-top: 10px;">
                                                <b class="separardor">CÓDIGO:</b> <span class="separador_i">{{$codigo_docAdj}}</span>
                                                <b class="separardor">DESCRIPCIÓN: </b> <span class="separador_i">{{$descripcion_docAdj}}</span>
                                                <b class="separardor">TIPO DOCUMENTO: </b> <span class="tipo_documento_adj">{{$descripcion_tipo_documento_adj}}</span>
                                            </div>
                                            <div class="infoDocEnviar">
                                                {{-- para enviar el id de los documentos adjuntos que no se borraron durante la edición --}}
                                                 <input type="hidden" name="id_documento_adjunto_conservado[]" value="{{$iddocumento_encrypt}}">
                                                {{-- para validar el tipo de documento que se conserva --}}
                                                 <input type="hidden" name="input_id_tipo_documento_conservado[]" value="{{$idtipo_documento_adj_encrypt}}" class="idtipo_documento_adj">
                                            </div>
                                        </div>

                                    @endforeach


                                </div>                                 
                            </div>
                
                        </div>         
                    </div>  
                @else
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
                @endif


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
    
                    <div id="VistaPreviaDoc" style="border-top: 1px solid #e5e5e5;"></div>

                    {{-- Info de No se puede visualizar el documento --}}
                    <div id="sinVistaPrevia" style="display: none;">
                        <center>
                            <i class="fa fa-file-archive-o" style="font-size: 150px; opacity: 0.3;"></i>
                            <h4>Sin vista previa</h4>
                        </center>
                    </div>

                    <br>

                    <div class="form-group" id="content_tipo_documento">
                        <label for="cmb_tipo_documento_docAdj" class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12">Crear Documento<span class="required">*</span></label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            
                            <div class="chosen-select-conten">
                                <select data-placeholder="No hay documentos para crear" id="cmb_tipo_documento_docAdj" onchange="" class="cmb_tipo_documento chosen-select form-control" tabindex="5">
                                    
                                    @foreach ($listaTipoDocumentos as $tipo_documento)
                                        @if(!isset($listaTipoDocumentosUsados[$tipo_documento->idtipo_documento]))
                                            <option value="{{$tipo_documento->idtipo_documento_encrypt}}">{{$tipo_documento->descripcion}}</option>
                                        @endif                                
                                    @endforeach

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