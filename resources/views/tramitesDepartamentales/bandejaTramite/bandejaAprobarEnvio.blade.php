@extends('layouts.service')
@section('contenido')

{{-- Datetables --}}
<link href="{{asset('../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">

<style type="text/css">
	.inputsearch div{
		float: left;
    }
    
    .inputsearch .dataTables_filter label{
        float: left;
    }
    
    input[type="search"]{
		width: 100% !important;
    }

    
    td{
        vertical-align: inherit !important;
    }

    .todo_mayus{
        text-transform: uppercase;
        text-align: left;
    }

    .btn_icon{
        font-size: 18px;
        padding: 0px 8px;
    }

    .btn_info_icon{
        font-size: 20px; margin-right: 0px; padding-top: 2px; padding-bottom: 2px; color:#169F85;
    }

    .btn_regresar{
        margin-left: 15px; margin-left: 0px; font-size: 14px; font-weight: 700; color: #446684;
    }

    .btn_config_firma{
        float: right;
    }

    /* estilos solo para telefonos */
    @media screen and (max-width: 767px){
        .ocultar_mobil{
            display: none;
        }
    }

</style>

<div id="listaTramites_enElaboracion" class="row center-block">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-success-2">
            <div class="panel-heading">
                <span><i class="fa fa-thumbs-up i_submenu ocultar_mobil"></i> Aprobar Trámites Pendientes </span>                
            </div>
            <div class="panel-body">                

                <div id="alerta_info_cabe_pie" class="alert_info_doc" style="display: block;">
                    <i class="fa fa-info-circle" style="margin-right: 5px; font-size: 15px;"></i> 
                    A continuación, se muestran los trámites generados en el departamento que necesitan ser aprobados y firmados para enviar los departamentos destino.
                 </div>

                <div class="table-responsive">
                    <div class="row">
                        <div class="col-sm-12">
                            <table  id="tabla_tramites" style="color: black" class="dinamic_table table table-striped table-bordered dataTable no-footer text-center" role="grid" aria-describedby="datatable_bandeja">
                               
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_desc" tabindex="0" aria-controls="datatable" style="width: 10px;">Nº</th>
                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" >Código</th>
                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" >Asunto</th>
                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" >Fecha</th>
                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" style="width: 10px;">Acción</th>
                                        </tr>
                                    </thead>

                                    <tbody >

                                        @isset($listaTramite)
                                            @foreach ($listaTramite as $key => $detalle_tramite)
                                                
                                                <tr role="row" class="odd">
                                                    <td class="sorting_1 todo_mayus"><center> {{$key+1}} </center></td>
                                                    <td class="todo_mayus">{{$detalle_tramite->tramite->codTramite}}</td>
                                                    <td class="todo_mayus">{{$detalle_tramite->asunto}}</td>
                                                    <td class="todo_mayus">{{$detalle_tramite->fecha}}</td>
                                                    <td width="5%" class="bg-warning" style="text-align: center;">
                                                        <button type="button" onclick="verTramite('{{$detalle_tramite->iddetalle_tramite_encrypt}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Ver Detalle</button>
                                                    </td>

                                                </tr>

                                            @endforeach                                            
                                        @endisset
                    
                                    </tbody>                    
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('tramitesDepartamentales.aprobarEnvio.ventanasModalRevision')

<div id="contet_ver_tramite" style="display: none;">

    <h3 style="margin-top: 60px; margin-bottom: 0px;"> <b>Detalle general del trámite</b> </h3>
    <hr class="hr_line">
        <button type="button" onclick="cerrarDetalleTramite()" class="btn btn-default btn_regresar"><i class="fa fa-mail-reply-all"></i> Regresar</button>
        <button id="btn_rev_aprobar" onclick="" class="btn btn_revision btn_regresar btn-success" style="color: #fff"><i class="fa fa-send"></i> Firmar y enviar</button>
        <button id="btn_rev_revision" class="btn btn_revision btn_regresar btn-warning" data-toggle="modal" data-target="#modal_detalle_revision"><i class="fa fa-eye"></i> Revisión</button>
    <hr class="hr_line">
    
    @include('tramitesDepartamentales.detalleTramite.consultar.detalleTramite')
</div>


<!-- gestion aprobar envio -->
<script src="{{asset('js/TramiteDepartamental/bandejas/gestionAprobarEnvio.js')}}"></script>
<script src="{{asset('js/TramiteDepartamental/tramites/aprobarEnvioTramite.js')}}"></script>
<!-- Datatables -->
<script src="{{asset('../vendors/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>


@endsection