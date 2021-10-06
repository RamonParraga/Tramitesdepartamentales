@extends('layouts.service')
@section('contenido')

	<!-- PNotify -->
        <link href="{{asset('vendors/pnotify/dist/pnotify.css')}}" rel="stylesheet">
        <link href="{{asset('vendors/pnotify/dist/pnotify.buttons.css')}}" rel="stylesheet">
        <link href="{{asset('vendors/pnotify/dist/pnotify.nonblock.css')}}" rel="stylesheet">

        <script src="{{asset('vendors/pnotify/dist/pnotify.js')}}"></script>
        <script src="{{asset('vendors/pnotify/dist/pnotify.buttons.js')}}"></script>
    {{-- fin --}}

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
                <h3>Gestionar Actividades de Departamentos</h3>
            </div>
            <br id="administador_actividad">
        </div>
    </div>

    <div class="row" >
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
                <h2> <i class="fa fa-edit"></i> Registro de actividades</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
			<br>
			  

			<form id="frm_actividad_gestion" method="POST" action="{{url('departamentos/actividad')}}"  enctype="multipart/form-data" class="form-horizontal form-label-left">
				{{csrf_field() }}
                <input id="method_actividad" type="hidden" name="_method" value="POST">

                <div class="form-group combos_actividad">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cmd_periodo">Periodo <span class="required">*</span></label>
                    <div id ="conten_select_cmd_periodo" class="col-md-6 col-sm-6 col-xs-12">
                        <div class="chosen-select-conten">
                            <select data-placeholder="Seleccione un departamento"  name="gd_select_cmd_periodo" id="gd_select_cmd_periodo" class="chosen-select form-control" tabindex="5">        
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

                <div class="form-group combos_actividad">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cmb_departamento">Departamentos <span class="required">*</span></label>
                    <div id ="conten_select_cmb_departamento" class="col-md-6 col-sm-6 col-xs-12">
                        <div class="chosen-select-conten">
                            <select data-placeholder="Seleccione un departamento" onchange="filtrar_actividades_por_departamento(this)" name="gd_select_cmb_departamento" id="gd_select_cmb_departamento" class="chosen-select form-control" tabindex="5">        
                                <option class="gd_select_cmb_departamento idperiodo_0" value="">Seleccione un departamento</option>
                                @isset($listaDepartamentos)
                                    @foreach ($listaDepartamentos as $departamento)
                                        <option class="gd_select_cmb_departamento {{$departamento->periodo->estado=='A' ? '':'hidden'}}" value="{{$departamento->iddepartamento}}">{{$departamento->nombre}} </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>
                </div> 

                <div class="form-group">

                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ga_actividad">Actividad<span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <textarea type="text" name="ga_actividad" id="ga_actividad" placeholder="Escriba una actividad aquí..." rows="3" class="form-control col-md-7 col-xs-12  componentLeft"></textarea>
                        <button onclick="agregar_actividad()" id="btn_agregar_actividad" type="button" class="btn btn-outline-primary componentRigth"><i class="fa fa-plus-square"></i> Agregar</button>
                    </div>
                    
                </div>

                <div id="conten_actividades_sin_guardar" class="form-group hidden" style="margin-bottom: 0px;">
                        
                    <div class="col-md-3 col-sm-3 col-xs-12"></div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <hr>
                        <h4><i class="fa fa-align-left"></i> Lista de actividades no registradas.</h4>
                        <div id="conten_add_actividades"  onmouseup="comprobar_numero_actividades(this);">
                            {{-- AQUÍ SE CARGARAN CON JQUERY LA ACTIVIDAD AGREGADA --}}
                        </div>
                    </div>
                        
                </div>
                
                <div class="form-group"><br>
                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <button id="btn_actividad_cancelar" class="btn btn-dark hidden" type="button"><i class="fa fa-close"></i> Cancelar</button>
                    <button id="btn_actividad_eliminar" class="btn btn-danger hidden" type="button"> <i class="fa fa-trash"></i> Eliminar</button>
					<button id="btn_actividad_guardar" type="submit"  class="btn btn-success"><i class='fa fa-cloud-upload'></i> Regisatrar</button>
                  </div>
                </div>
				<br>

            </form>

            {{-- formulario para eliminar una actividad --}}
            <form id="frm_actividad_eliminar" action="" method="POST">
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
                <h2> <i class="fa fa-edit"></i>Lista de actividades</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <br>
                <center id="msj_no_actividad"><h2> <i class="fa fa-search" style="padding-right: 10px;  font-size: 25px;"></i> No hay actividades</h2></center>
                {{-- CONTENIDO DE LAS ACTIVIDADES --}}
                <ul id="ul_lista_actividades" class="list-unstyled timeline">
                    {{-- LAS ACTIVIDADES (LI) SE CARGAN CON JQUERY --}}
                </ul>
            </div>
          </div>
        </div>
    </div>


    {{-- ARCHIVO JQUERY PARA LA GESTION DE DEPARTAMENTOS --}}
    <script type="text/javascript" src="{{asset('js/TramiteDepartamental/gestionDepartamentos.js')}}"></script>


@endsection