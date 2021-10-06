    <style type="text/css">
        .div_scroll_doc_act{
            border: 1px solid #d1d1d1;
            padding-left: 10px;
            padding-right: 10px;
            background-color: #f5f7fa;
            margin-bottom: 10px;
            max-height: 184px !important;
        }

        /* .mce-content-body p{
            margin: 5px 0 !important;
        } */
    </style>

    <div id="cont_editor_documento" class="">
            <span style="font-weight: 900; margin-left: 7px;">CREACIÓN DE DOCUMENTO</span>
            <div class="hr_divisor" style="margin-top:0;"></div>


            <div id="cont_lista_documentos_creados" style="display: none;">
        
                <div class="form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding-right: 0px;">
                        <div id="lista_documentos_creados">              
                            {{-- CONTENIDO SE CARGA CON JQUERY --}}
                        </div>                                 
                    </div>
        
                </div>         
            </div>
            

            <div class="form-group" id="content_tipo_documento">
                <label for="cmb_tipo_documento" class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12">Crear Documento<span class="required">*</span></label>
                <div class="col-md-10 col-sm-10 col-xs-12 colpr0">
                    
                    <div class="chosen-select-conten">
                        <select data-placeholder="No hay documentos para crear" id="cmb_tipo_documento" onchange="" class="chosen-select form-control" tabindex="5">
                            
                            @foreach ($listaTipoDocPrioritarios as $tipo_documento)                                
                                <option value="{{$tipo_documento->idtipo_documento_encrypt}}" @if($documentoPrincipal->tipo_documento->idtipo_documento == $tipo_documento->idtipo_documento) selected @endif>{{$tipo_documento->descripcion}}</option>                               
                            @endforeach

                        </select>
                    </div>

                </div>
            </div>

        {{-- <div class="form-group">
            <label for="" class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12">Descripción </label>
            <div class="col-md-10 col-sm-10 col-xs-12" style="padding-right: 0;">
                <input type="text" id="descripcion_documento" class="form-control" placeholder="Ingrese una descripción del documento" value="{{$documentoPrincipal->descripcion}}" style="text-transform: uppercase">
            </div>
        </div> --}}

        <div class="form-group">

            <div class="control-label alignTextLeft col-md-12 col-sm-12 col-xs-12" style="padding-right: 0;">
                <span id="titulo_editorDocumento" style="font-size: 17px;">Cuerpo del documento </span>
                <button id="btn_cancelar_edicion_documento" type="button" onclick="cancelar_edicion_documento()" class="btn btn-warning btn-sm" style="float: right; margin-right: 0; font-size: 16px; display: none;"><i class="fa fa-reply-all"></i> Cancelar Edición</button>
            </div>

        </div>

        <div id="alerta_info_cabe_pie" class="alert_info_doc hidden">
            Par guardar le documento da click en el icono 
            (<svg width="24" height="24"><path d="M5 16h14a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2c0-1.1.9-2 2-2zm0 2v2h14v-2H5zm10 0h2v2h-2v-2zm-4-6.4L8.7 9.3a1 1 0 1 0-1.4 1.4l4 4c.4.4 1 .4 1.4 0l4-4a1 1 0 1 0-1.4-1.4L13 11.6V4a1 1 0 0 0-2 0v7.6z" fill-rule="nonzero"></path></svg>). 
            La cebecera y pie de página del documento se agregan automaticamente al guardar el documento.
        </div>

        <input type="hidden" id="idtipo_documento_editar" value="0">

        <textarea id="full-featured-non-premium">
            {{$contenidoDocumentoPrincipal}}
        </textarea>
    </div>
