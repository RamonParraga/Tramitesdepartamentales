@extends('layouts.service')
@section('contenido')

   
<!-- Datatables -->
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <!-- <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">

	<!-- PNotify -->
        <link href="{{asset('vendors/pnotify/dist/pnotify.css')}}" rel="stylesheet">
        <link href="{{asset('vendors/pnotify/dist/pnotify.buttons.css')}}" rel="stylesheet">
        <link href="{{asset('vendors/pnotify/dist/pnotify.nonblock.css')}}" rel="stylesheet">

        <script src="{{asset('vendors/pnotify/dist/pnotify.js')}}"></script>
        <script src="{{asset('vendors/pnotify/dist/pnotify.buttons.js')}}"></script>
    {{-- fin --}}

    <div class="row">
        <div class="col-md-12">
            <div class="title_left">
                <h3>Gestionar Permisos de los Usuarios</h3>
            </div>
            <br>
        </div>
    </div>

    <div class="clearfix"></div>
    
    <div class="" id="administrador_permisos">
        <div class="x_panel">
            <div class="x_title">
            <h2><i class="fa fa-bars"></i><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"> Administrador de Permisos </font></font></h2>
            <ul class="nav navbar-right panel_toolbox">
                <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
            </ul>
            <div class="clearfix"></div>
            </div>
            <div class="x_content x_content_border_mobil">

                <div class="" role="tabpanel" data-example-id="togglable-tabs">

                    <ul id="myTab" class="nav nav-tabs bar_tabs ul_mobil" role="tablist" style="user-select: none;">
                        <li role="presentation" class="@if(session()->has('mensajePInfoMenu')) active @endif ">
                            <a href="#menu" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Crear Menú</font></font>
                            </a>
                        </li>
                        <li role="presentation" class="@if(session()->has('mensajePInfoGestion')) active @endif ">
                            <a href="#gestion" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Crear Gestión</font></font>
                            </a>
                        </li>
                        <li role="presentation" class="@if(session()->has('mensajePInfoTipoFP')) active @endif ">
                            <a href="#tipoFP" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Crear Tipos FP</font></font>
                            </a>
                        </li>
                        <li role="presentation" class="@if(session()->has('mensajePInfoAsignarGesion')) active @endif ">
                            <a href="#asignarGestion" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Asignar Gestión a un Tipo FP</font></font>
                            </a>
                        </li>          

                        <li role="presentation" class="@if(session()->has('mensajePInfoAsignarTipoFP') || isset($inicial)) active @endif ">
                            <a href="#asignarTipo_FP" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Asignar Tipo a un FP</font></font>
                            </a>
                        </li>
                    </ul>


                </div>

                <div id="myTabContent" class="tab-content">
                    <div role="tabpanel" class="tab-pane fade @if(session()->has('mensajePInfoMenu')) active in @endif" id="menu" aria-labelledby="home-tab">
                        @include('gestionPermisos.menu')
                    </div>
                    <div role="tabpanel" class="tab-pane fade @if(session()->has('mensajePInfoGestion')) active in @endif" id="gestion" aria-labelledby="profile-tab">
                        @include('gestionPermisos.gestion')
                    </div>
                    <div role="tabpanel" class="tab-pane fade @if(session()->has('mensajePInfoTipoFP')) active in @endif" id="tipoFP" aria-labelledby="profile-tab">
                        @include('gestionPermisos.tipoFP')
                    </div>
                    <div role="tabpanel" class="tab-pane fade @if(session()->has('mensajePInfoAsignarGesion')) active in @endif" id="asignarGestion" aria-labelledby="profile-tab">
                        @include('gestionPermisos.asignarGestion')
                    </div>
                    <div role="tabpanel" class="tab-pane fade @if(session()->has('mensajePInfoAsignarTipoFP') || isset($inicial)) active in @endif" id="asignarTipo_FP" aria-labelledby="profile-tab">
                        @include('gestionPermisos.asignartipoFP')
                    </div>
                </div>

            </div>
        </div>
    </div>



    <div class="">
        <div class="x_panel">
            <div class="x_title">
            <h2><i class="fa fa-bars"></i><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"> Organigrama General del Menú de Opciones </font></font></h2>
            <ul class="nav navbar-right panel_toolbox">
                <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-down"></i></a></li>
            </ul>
            <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: none;">
                @include('gestionPermisos.OrganigramaGeneral')
            </div>
        </div>
    </div>


<!-- VENTANA MODAL PARA SELECCIONAR ICONOS -->

<div id="modalSeleccionarIcono" class="modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	    <div class="modal-header">
	        <label class="modal-title" style="font-size: 130%;">SELECCIONE UN ICONO</label>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	    </div>
	    <div class="modal-body" style="padding: 4px;">
            <input type="hidden" id="idInputSeleccionado">
            <input type="hidden" id="idButtonSeleccionado">
            @include('gestionPermisos.ListaIconos')
            <br><br>
        </div>
    </div>
  </div>
</div>




    <script type="text/javascript">
        $(document).ready(function () {
            $('.collapse-link').click();
            $('.datatable_wrapper').children('.row').css('overflow','inherit !important');
            $('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0','overflow-x':'inherit'});
        });
    </script>

    <!-- Datatables -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <!-- <script src="../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script> -->
    <script src="{{asset('/js/gestionPermisos.js')}}"></script>
@endsection