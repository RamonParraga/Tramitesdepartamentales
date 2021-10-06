@extends('layouts.service')
@section('contenido')

    <style type="text/css">
        .vert_center{
            vertical-align: middle !important;
            text-align: center;
        }
        .btn_revision{
            padding: 5px 30px;
        }

        .col_subir_doc{
            display: none;
        }
        .titulo_moda{
            font-weight: 300;
            color: #555;
            margin-top: 0px;
            margin-bottom: 32px;
            text-align: center;
            margin-left: 20px;
            margin-right: 20px;
        }
        .btn_firma{
            border-radius: 20px;
        }
        .btn_firma img{
            width: 100%;
        }
        .icon_success{
            font-size: 25px;
            color: #26ae2d;
        }
        .icon_warning{
            font-size: 25px;
            color: #ff851d;
        }
        .icon_danger{
            font-size: 25px;
            color: #db3131;
        }

        .lable_estado{
            padding: 5px;
            font-size: 14px;
            display: block;
            text-transform: none;
            font-weight: 500;
        }

        .btn_regresar{
            margin-left: 15px; margin-left: 0px; font-size: 14px; font-weight: 700; color: #446684;
        }

    </style>

    @isset($detalleTramite)
   
        <h3 style="margin-top: 60px; margin-bottom: 0px;"> <b>Revisión del trámite:</b> <i>{{$detalleTramite->tramite->codTramite}}</i> </h3>
        <hr class="hr_line">
            <div id="content_botones">
                <a id="btn_rev_regresar_bandeja" href="{{url('gestionBandeja/aprobarEnvio')}}" class="btn btn-default btn_regresar"><i class="fa fa-mail-reply-all"></i> Regresar</a>
                <button id="btn_rev_aprobar" onclick="aprobarTramite('{{$detalleTramite->iddetalle_tramite_encrypt}}')" class="btn btn_revision btn-success"><i class="fa fa-thumbs-o-up"></i> Aprobar</button>
                <button id="btn_rev_revision" class="btn btn_revision btn-warning" data-toggle="modal" data-target="#modal_detalle_revision"><i class="fa fa-eye"></i> Revisión</button>
                <button id="btn_rev_regresar" class="btn btn-default" style="display: none;"><i class="fa fa-reply-all"></i> Regresar</button>
            </div>
        <hr class="hr_line">

        {{-- DETALLE DEL TRAMITE --}}
            <div class="panel panel-dark" id="contet_detalle_tramite">

                <div class="panel-heading"> 
                    Detalles del Trámite
                </div> 

                <div class="panel-body"> 

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Código:</label>
                        <div class="col-sm-10">              
                            <p id="rt_codigoTramite"> {{$detalleTramite->tramite->codTramite}} </p>              
                        </div>
                    </div>
                
                
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Asunto:</label>
                        <div class="col-sm-10">                
                            <p id="rt_asunto"> {{$detalleTramite->asunto}} </p>                
                        </div>
                </div>
                

                <div class="form-group">
                        <label class="col-sm-2 control-label">Observación:</label>
                        <div class="col-sm-10">                        
                            <p id="rt_observacion"> {{$detalleTramite->observacion}} </p>                        
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">Destino(s):</label>
                        <div class="col-sm-10" id="rt_destino"> 
                            <ol style="padding-left: 20px;">                    

                                @foreach ($detalleTramite->destino as $destino)
                                    @php 
                                        $tipo_envio = "";
                                        if($destino->tipo_envio == "C"){
                                            $tipo_envio = " <i class='fa fa-angle-double-right'></i> Copia <i class='fa fa-copy'></i>";
                                        }
                                    @endphp
                                    <li>
                                        {{$destino->departamento->nombre}} 
                                        <b style='padding-left: 5px'>
                                            @php print($tipo_envio) @endphp
                                        </b>
                                    </li>
                                @endforeach

                            </ol>
                        </div>
                </div>  

                </div>
            </div>


        {{-- LISTA DE DOCUMENTOS ADJUNTOS Y CREADOS --}}
            <div class="panel panel-dark" id="contet_lista_documentos">

                <div class="panel-heading"> 
                    Lista de documentos
                </div> 

                <div class="panel-body"> 

                    <div class="table-responsive"> {{-- table-responsive --}}
                        <table style="color: black; margin-bottom: 0px;" class="table table-striped table-row-center-vertical table-bordered dataTable no-footer table-row-center-vertical" role="grid" aria-describedby="datatable_info">
                            <thead>
                                <tr role="row">                                                       
                                    <tr>
                                        <th>Tipo Documento</th>
                                        <th>Fecha</th>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th style="width: 10px;" class="col_origen">Origen</th>           
                                        <th style="width: 10px;">Documento</th>                                                                                                               
                                        <th style="width: 110px;" class="col_subir_doc">Subir Firmado</th>                                    
                                        <th style="width: 10px;" class="col_subir_doc col_icono_firma">Firmado</th>
                                    </tr>
                                </tr>
                            </thead>         
                            
                            <tbody id="tbody_detalle_tramite_documentos">
                                @if(sizeof($detalleTramite->documento)==0)
                                    <tr>
                                        <td colspan="5">
                                            <center>No hay documentos</center>
                                        </td>
                                    </tr>    
                                @else
                                    @foreach ($detalleTramite->documento as $documento)
                                        @php

                                            $color = "bg-success"; // color de la fima
                                            $tipo_creacion = '<span class="label label-success lable_estado">Principal</span>'; // mensaje de codimento creado o adjunto
                                            $class_doc_adj = ""; // para identificar las filas de los documentos adjuntos (para ocultarles)
                                            $icono_doc_firmado = '<i class="fa fa-close icon_danger"></i>';
                                            if($documento->tipo_creacion == "A"){
                                                $color = "bg-warning";
                                                $class_doc_adj = "documento_adjunto";
                                                $tipo_creacion = '<span class="label label-warning lable_estado">Adjunto</span>';
                                            }
                                            if($documento->firmado==1){
                                                $icono_doc_firmado = '<i class="fa fa-check-circle icon_success"></i>';
                                            }

                                        @endphp
                                        <tr class="{{$class_doc_adj}}">
                                            <td class="{{$color}} vert_center">{{$documento->tipo_documento->descripcion}}</td>
                                            <td class="{{$color}} vert_center">{{$documento->fechaCarga}}</td>
                                            <td class="{{$color}} vert_center">{{$documento->codigoDocumento}}</td>
                                            <td class="{{$color}} vert_center">{{$documento->descripcion}}</td>
                                            <td class="{{$color}} vert_center col_origen"><center>@php print($tipo_creacion) @endphp</center></td>                                           

                                            <td class="{{$color}} vert_center">
                                                <a href="{{url('buscarDocumento/disksServidorSFTPborradores/'.$documento->rutaDocumento.'.pdf')}}" target="_blank" data-toggle="tooltip" data-placement="top" title="Ver o descargar el documento" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                            </td>   

                                            <td class="{{$color}} vert_center col_subir_doc">
                                                <form action="{{url('revisionTramite/subirDocumentoFirmado/'.$documento->iddocumento_encrypt)}}" class="form_subirDocumento" method="POST" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="_method" value="POST">
                                                    <input type="file" name="input_subirDocumento" class="input_subirDocumento hidden" data-id="{{$documento->iddocumento_encrypt}}" accept="application/pdf">                      
                                                    <button type="button" onclick="subirDocumentoFirmado(this)" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Subir documento firmado"><i class="fa fa-upload"></i> Subir</button>
                                                </form>                                                
                                            </td>

                                            <td class="{{$color}} vert_center col_subir_doc col_icono_firma">
                                                <center>@php print($icono_doc_firmado) @endphp</center>
                                            </td>

                                        </tr>
                                    @endforeach                            
                                @endif

                                {{-- EL CONTENIDO SE CARGA CON JQUERY --}}
                            </tbody>

                        </table>                            
                    </div>

                </div>
            </div>
        {{-- agregamos el html de las ventanas modales --}}
        @include('tramitesDepartamentales.aprobarEnvio.ventanasModalRevision')

    @else
        @include('error')
    @endisset
   


    <script src="{{asset('js/TramiteDepartamental/tramites/aprobarEnvioTramite.js')}}"></script>

@endsection