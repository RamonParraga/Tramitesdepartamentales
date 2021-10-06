@extends('layouts.service')
@section('contenido')
    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <!-- <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{asset('vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">

    <style type="text/css">

        /* estilos para escritorio */
        @media screen and (min-width: 767px) {
            .grafico_estadistico{
                margin-left: 50px;
            }
        }

    </style>

    
    <div class="row" id="adm">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3 style="margin: 20px 0px 0px 0px;"><i class="fa fa-edit"></i> Estadísticas</h3>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <div class="panel panel-dark" id="contet_lista_documentos">
                        <div class="panel-heading" style="padding: 5px 10px;"> 
                            <i class="fa fa-bar-chart-o"></i> Filtrar tiempo promedio por trámite
                        </div> 
                        <div class="panel-body"> 
                            <form class="form-horizontal form-label-left input_mask">

                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre_menu">Usuarios:</label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <div class="chosen-select-conten">
                                            <select id="cmb_usuarios" onchange="filtrarEstadistica()" data-placeholder="" required="required" class="chosen-select form-control" tabindex="5">
                                                <option value="{{ encrypt('0') }}">--TODOS LOS USUARIOS--</option>
                                                @isset($listUsuariosDepa)
                                                    @foreach ($listUsuariosDepa as $user)
                                                        <option class="opcionATFP_departamento" value="{{$user->idus001_encrypt}}">{{$user->name}}</option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre_menu">Fecha Inicio:</label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input type="date" id="est_fechaInicio" onchange="filtrarEstadistica()" class="form-control">
                                    </div>

                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre_menu">Fecha Fin:</label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input type="date" id="est_fechaFin" onchange="filtrarEstadistica()" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="">Tipo Muestra:</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12" style="padding-top: 8px;">
                                        <label for="media_dias">En dias </label> <input type="radio" id="media_dias" value="D" class="flat tipomuestra" name="media_muestra" checked onchange="filtrarEstadistica()" /> 
                                        <label for="media_horas" style="margin-left: 12px;">En horas </label> <input type="radio" id="media_horas" value="H" class="flat tipomuestra" name="media_muestra" onchange="filtrarEstadistica()" />                              
                                    </div>
                                </div>

                                <hr>
                                <div id="grafica_promedio_atencion" class="grafico_estadistico" style="height:350px;"></div>

                            </form>
                        </div>
                    </div>


                    <div class="panel panel-dark" id="contet_lista_documentos">
                        <div class="panel-heading" style="padding: 5px 10px;"> 
                            <i class="fa fa-pie-chart"></i> Cantidad de trámites generados
                        </div> 
                        <div class="panel-body"> 
                            <form class="form-horizontal form-label-left input_mask"> 
                                <h2 style="color: #4b8f36; padding-left: 40px;;">Distribución de atención por trámite</h2>                           
                                <div id="grafica_distribucion_atencion" style="height:350px;"></div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- datosUsuarios -->
    @isset($listTiemMedioTram) 
        <input type="hidden" id="input_listTiemMedioTram" data-field-id="{{$listTiemMedioTram}}"> 
    @endisset
    <!-- ECharts -->
    <script src="../vendors/echarts/dist/echarts.min.js"></script>
    <!-- Chart.js -->
    <script src="../vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- iCheck -->
    <script src="{{asset('vendors/iCheck/icheck.min.js')}}"></script>
    <!-- Libreria -->
    <script src="{{ asset('js/TramiteDepartamental/estadistica.js') }}"></script>
    <!-- Datatables -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    
@endsection