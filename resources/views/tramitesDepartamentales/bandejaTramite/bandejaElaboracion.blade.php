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

</style>

<div id="listaTramites_enElaboracion" class="row center-block">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-dark">
            <div class="panel-heading">
                <i class="fa fa-eraser"></i> Trámites en Elaboración (Borrador)
            </div>
            <div class="panel-body">

                <div class="table-responsive">
                    <div class="row">
                        <div class="col-sm-12">
                            <table  id="tabla_tramites" style="color: black" class="dinamic_table table table-striped table-bordered dataTable no-footer text-center" role="grid" aria-describedby="datatable_bandeja">
                               
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_desc" tabindex="0" aria-controls="datatable" style="width: 10px;">Nº</th>
                                            <th class="sorting" style="">Código</th>
                                            <th class="sorting" style="width: 112px;">Fecha</th>
                                            <th class="sorting" style="">Asunto</th>
                                            <th class="sorting" style="width: 10px;">Acción</th>
                                        </tr>
                                    </thead>

                                    <tbody >

                                        @isset($listaTramite)
                                            @foreach ($listaTramite as $key => $detalle_tramite)
                                                
                                                <tr role="row" class="odd">
                                                    <td class="sorting_1 todo_mayus"><center> {{ $key+1 }} </center></td>
                                                    <td class="todo_mayus">{{ $detalle_tramite->tramite->codTramite }}</td>
                                                    <td class="todo_matus">{{ $detalle_tramite->fecha }}</td>
                                                    <td class="todo_mayus">{{ $detalle_tramite->asunto }}</td>
                                                    <td width="5%" class="bg-warning" style="text-align: center;">
                                                        <button type="button" onclick="verTramite('{{$detalle_tramite->iddetalle_tramite_encrypt}}')" class="btn btn-sm btn-info" ><i class="fa fa-eye"></i> Ver Detalle</button>
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


<div id="contet_ver_tramite" style="display: none;">

    <h3 style="margin-top: 60px; margin-bottom: 0px;"> <b>Detalle general del trámite</b> </h3>

    <hr class="hr_line">
        <button type="button" onclick="cerrarDetalleTramite()" class="btn btn-default btn_regresar"><i class="fa fa-mail-reply-all"></i> Regresar</button>
        <a id="btn_elaboracion_editar" href="" class="btn btn-sm btn-warning btn_regresar"><i class="fa fa-gear"></i> Editar</a>
        <form method="POST" action=""  enctype="multipart/form-data" style="display: inline;">
            {{csrf_field() }} 
            <input type="hidden" name="_method" value="DELETE">  
            <button id="btn_elaboracion_eliminar" type="button" class="btn btn-sm btn-danger btn_regresar" style="color: #ffff"><i class="fa fa-trash"></i> Eliminar</button>
        </form>
    <hr class="hr_line">

    @include('tramitesDepartamentales.detalleTramite.consultar.detalleTramite')
</div>



<script type="text/javascript">
    $(document).ready(function () {

        $('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0','overflow-x':'inherit'});
       
        $("#tabla_tramites").DataTable({
            dom: ""
            +"<'row' <'form-inline' <'col-sm-6 inputsearch'f>>>"
            +"<rt>"
            +"<'row'<'form-inline'"
            +" <'col-sm-6 col-md-6 col-lg-6'l>"
            +"<'col-sm-6 col-md-6 col-lg-6'p>>>",
            pageLength: 10,
            "language": {
                "lengthMenu": 'Mostrar <select class="form-control input-sm">'+
                            '<option value="5">5</option>'+
                            '<option value="10">10</option>'+
                            '<option value="15">15</option>'+
                            '<option value="20">20</option>'+
                            '<option value="30">30</option>'+
                            '<option value="-1">Todos</option>'+
                            '</select> registros',
                "search": "<b><i class='fa fa-search'></i> Buscar: </b>",
                "searchPlaceholder": "Ejm: GADM-000-2020-N",
                "zeroRecords": "No se encontraron registros coincidentes",
                "infoEmpty": "No hay registros para mostrar",
                "infoFiltered": " - filtrado de MAX registros",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "paginate": {
                    "previous": "Anterior",
                    "next": "Siguiente"
                }
            }
        });

      });

</script>

<script src="{{asset('js/TramiteDepartamental/bandejas/gestionElaboracion.js')}}"></script>
        

<!-- Datatables -->
<script src="{{asset('../vendors/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>


@endsection