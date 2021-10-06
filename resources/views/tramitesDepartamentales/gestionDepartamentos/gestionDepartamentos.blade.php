@extends('layouts.service')
@section('contenido')
    <!-- LIBRERIAS PARA LAS TABLAS DINAMICAS -->
        <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    {{-- FIN --}}

    {{-- LIBRERIAS PARA GRAFICAR ORGANIGRAMA --}}
        <link rel="stylesheet" href="{{asset('BasicPrimitives/packages/jquery-ui/jquery-ui.min.css')}}" />
        <script type="text/javascript" src="{{asset('BasicPrimitives/packages/jquery-ui/jquery-ui.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('BasicPrimitives/min/primitives.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('BasicPrimitives/min/primitives.jquery.min.js')}}"></script>
        <link href="{{asset('BasicPrimitives/min/primitives.latest.css?2106')}}" media="screen" rel="stylesheet" type="text/css" />
	{{-- FIN --}}
	
	<!-- PNotify -->
		<link href="{{asset('vendors/pnotify/dist/pnotify.css')}}" rel="stylesheet">
		<link href="{{asset('vendors/pnotify/dist/pnotify.buttons.css')}}" rel="stylesheet">
		<link href="{{asset('vendors/pnotify/dist/pnotify.nonblock.css')}}" rel="stylesheet">

		<script src="{{asset('vendors/pnotify/dist/pnotify.js')}}"></script>
		<script src="{{asset('vendors/pnotify/dist/pnotify.buttons.js')}}"></script>
		{{-- <script src="{{asset('vendors/pnotify/dist/pnotify.nonblock.js')}}"></script> --}}
    {{-- fin --}}

    <!-- iCheck -->
    <link href="{{asset('../vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">


    {{-- MENSAJES DE INFORMACION --}}
        @if (session()->has('mensajeInfo'))
            <script type="text/javascript">
                $(document).ready(function () {
                    new PNotify({
                        title: 'Mensaje de Información',
                        text: '{{session('mensajeInfo')}}',
                        type: '{{session('mensajeColor')}}',
                        hide: true,
                        delay: 2000,
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
                <h3>Gestionar Departamentos</h3>
            </div>
            <br>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="row" id="administador_departamentos">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
                <h2> <i class="fa fa-edit"></i> Registro de departamento</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
			  <br>
			  


			<form id="frm_departamento_gestion" method="POST" action="{{url('departamentos/gestion')}}"  enctype="multipart/form-data" class="form-horizontal form-label-left">
				{{csrf_field() }}
				<input id="method_departamento" type="hidden" name="_method" value="POST">

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio" for="gd_nombre">Nombre</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="text" name="gd_nombre" id="gd_nombre" required="required" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio" for="gd_codcabildo">Cod Cabildo</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="text" name="gd_codcabildo" id="gd_codcabildo"  required="required" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label for="gd_abreviacion" class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio">Abreviación</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="text" name="gd_abreviacion" id="gd_abreviacion" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label for="gd_nivel" class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio">Nivel</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="text" name="gd_nivel" id="gd_nivel" class="date-picker form-control col-md-7 col-xs-12" required="required">
                    </div>
                </div>
                <div class="form-group">
                    <label for="gd_correo" class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio">Correo</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="text" name="gd_correo" id="gd_correo" class="date-picker form-control col-md-7 col-xs-12" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio" for="icono_gestione">Periodo</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="chosen-select-conten componentLeft">
                            <select data-placeholder="Seleccione un periodo"  name="gd_select_periodo" id="gd_select_periodo" class="chosen-select form-control" tabindex="5">
                                <option class="gd_select_periodo" value="">Seleccione un periodo</option>
                                @isset($listaPeridos)
                                    @foreach ($listaPeridos as $periodo)
                                        @php 
                                            $estadoPeriodo=""; 
                                            if($periodo->estado=="A"){ // si el prediodo esta activo
                                                $estadoPeriodo=" --> (ACTIVO)";
                                            }
                                        @endphp
                                        <option class="gd_select_periodo" value="{{$periodo->idperiodo}}">{{'Del: '.$periodo->fecha_inicio.' Al: '.$periodo->fecha_fin.$estadoPeriodo}}</option>    
                                    @endforeach
                                @endisset
                            </select>
                        </div>	
                        <button type="button" onclick="gestionar_periodos()" class="btn btn-outline-primary componentRigth" onclick="cargar" data-toggle="modal" data-target=".modal_gestionPeriodo"><i class="fa fa-gear"></i> Admin</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="departamento_padre">Dep Padre</label>
                    <div id ="conten_select_departamento_padre" class="col-md-6 col-sm-6 col-xs-12">
                        <div class="chosen-select-conten">
                            <select data-placeholder="Seleccione un departamento"  name="gd_select_departamento_padre" id="gd_select_departamento_padre" class="chosen-select form-control" tabindex="5">        
                                <option class="gd_select_departamento_padre idperiodo_0" value="">Seleccione un departamento</option>
                                @isset($listaDepartamentos)
                                    @foreach ($listaDepartamentos as $departamento)
                                        <option class="gd_select_departamento_padre {{$departamento->periodo->estado=='A' ? '':'hidden'}}" value="{{$departamento->iddepartamento}}">{{$departamento->nombre}} </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>
                </div>


                
                {{-- INPUTS PARA DEFINIR EL --}}
                <div class="form-group"  style="user-select: none; display: none;">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio" for="">Permitir</label>
                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                        <div class="label_finaliar_fujo" style="margin-bottom: 0; margin-bottom: 0;">
                            <label for="check_tramite_externo" style="margin: 0;">
                                <input type="checkbox" id="check_tramite_externo" name="check_tramite_externo" class="flat td_seleccionado"> <strong>Tramites Externos</strong>
                            </label> 
                        </div>

                    </div>                               
                </div>


                <div class="form-group"><br>
                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <button id="btn_departamento_cancelar" class="btn btn-dark hidden" type="button"><i class="fa fa-close"></i> Cancelar</button>
                    <button id="btn_departamento_eliminar" class="btn btn-danger hidden" type="button"> <i class="fa fa-trash"></i> Eliminar</button>
					<button id="btn_departamento_guardar" type="submit"  class="btn btn-success"><i class='fa fa-cloud-upload'></i> Registrar</button>
                  </div>
                </div>
				<br>
                {{-- <div class="ln_solid"></div> --}}

            </form>

            {{-- formulario para eliminar un departamento --}}
            <form id="frm_departamento_gestion_eliminar" action="" method="POST">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
            </form>
            </div>
          </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
                <h2> <i class="fa fa-edit"></i> Orgamigrama de departamentos
                </h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>

            <div class="form-group margintop_pc">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" style="padding-top: 8px;" for="icono_gestione">Filtrar por Periodo: </label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                    <div class="chosen-select-conten componentLeft">
                        <select id="gd_filtrarDepartamentoOrganigrama" data-placeholder="Seleccione un periodo"  name="departamento_periodo" id="departamento_periodo" class="chosen-select form-control" tabindex="5">
                            @isset($listaPeridos)
                                @foreach ($listaPeridos as $periodo)
                                    <option @if ($periodo->estado=='A')
                                        selected 
                                    @endif value="{{$periodo->idperiodo}}">{{'Del: '.$periodo->fecha_inicio.' Al: '.$periodo->fecha_fin}}</option>    
                                @endforeach
                            @endisset
                        </select>
                    </div>	
                </div>
            </div>

            <div class="x_content">
                <br>
                <div id="alerta_info_cabe_pie" class="alert_info_doc" style="display: block;">
                    <i class="fa fa-info-circle" style="margin-right: 5px; font-size: 15px;"></i> 
                    Los departamentos <b>< AZULES ></b> tiene un jefe asignado, pero los departamentos <b> < NARANJA > </b> por el momento no tienen un jefe asignado
                </div>

                <div id="orgdiagram" style="overflow: hidden; border-style: ridge; border-width: 1px;"></div>
            </div>
          </div>
        </div>
    </div>
    
    {{-- vista para mostrar el organigrama de departamentos --}}
    @include('tramitesDepartamentales.gestionDepartamentos.organigrama')
    {{-- vista para la modal de administrar periodos --}}
	@include('tramitesDepartamentales.gestionDepartamentos.modales')

    <!-- Datatables -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

    {{-- ARCHIVO JQUERY PARA LA GESTION DE DEPARTAMENTOS --}}
        <script type="text/javascript" src="{{asset('js/TramiteDepartamental/gestionDepartamentos.js')}}"></script>
    {{-- ARCHIVO JQUERY PARA LA GESTION DE LOS PERIODOS --}}
        <script type="text/javascript" src="{{asset('js/TramiteDepartamental/gestionPeriodos.js')}}"></script>

    <!-- iCheck -->
    <script src="{{asset('../vendors/iCheck/icheck.min.js')}}"></script>

@endsection