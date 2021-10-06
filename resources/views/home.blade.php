@extends('layouts.service')

@section('contenido')

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="../vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">

    <style type="text/css">
        .mensajeInfo{
            margin-top: 30px;
        }

        #main_content{
            padding: 10px 15px 0px 15px;
        }

        #widgets_content{
            background-image: url("/images/chone_resurge2.png"); 
            background-repeat: no-repeat;
            background-size: cover;
            padding: 10px;
        }

        .widgets_opacidad{
            padding-top: 10px;
        }

        /* COLORES DEL WIQUED */
            .tile-stats > .icon{
                /* color: #1abb9c !important; */
                color: #0898c3e8 !important
            }
            .tile-stats > .count{
                color: #00a65a !important;
            }
            .tile-stats > h3{
                color: #1ea1c8;
                font-weight: 700;
            }
            .tile-stats > p{
                font-weight: 700 !important;
                color: #00a65a !important;
            }




        .tile-stats{
            /* box-shadow: 0px 0px 7px 3px rgb(150, 141, 141); */
            /* box-shadow: 0px 0px 3px 3px rgba(186,186,186,1); */
            /* box-shadow: 2px 2px 3px 3px rgba(186,186,186,1); */
            box-shadow: 7px 7px 3px -2px rgba(186,186,186,1);

            /* border: 1px solid #73879c !important */
            /* border: 2px solid #73879c !important; */
            /* border: 2px solid #7396c6 !important; */
            border: 1px solid #1ea1c8 !important;

            margin-bottom: 19px;

        }

        .icon:hover{
            color: #1a80bb !important;
        }

        /* estilos solo para telefonos */
        @media screen and (max-width: 767px){
            .mensajeInfo{
                margin-top: 70px;
            }
        }
    </style>



            <!-- page content -->


                  <div class="row">
                    {{-- <div class="col-md-12"><h3>Bandejas principales</h3></div> --}}
                    <div class="col-md-12" style="padding: 0;">
                      <div class="">
                        <div class="x_content" style="margin-top: -10px !important;">
                          <div class="row" id="widgets_content">

                            <div class="widgets_opacidad" @if(auth()->guest() || userEsTipo('ADFP')) style="display:none;" @endif>

                                    <div class="animated flipInY col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-cloud-download"></i>
                                            </div>
                                            <div class="count"><i class="fa fa-paperclip"></i> <span id="notifi_tramite_entrante">0</span></div>
                
                                            <h3>Trámites entrantes</h3>
                                            <p>Para ver todos los trámites que tiene que atender haga click <a href="{{ url('gestionBandeja/entrada') }}"><i class="fa fa-hand-o-right"></i> Aquí.</a></p>
                                        </div>
                                    </div>

                                    <div class="animated flipInY col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-eraser"></i>
                                            </div>
                                            <div class="count"><i class="fa fa-paperclip"></i> <span id="notifi_tramite_borrador">0</span></div>
                
                                            <h3>Trámites en borrador</h3>
                                            <p>Para ver todos los trámites que aún no ha terminado de realizar haga click <a href="{{ url('gestionBandeja/enElaboracion') }}"><i class="fa fa-hand-o-right"></i> Aquí.</a></p>
                                        </div>
                                    </div>

                                    <div class="animated flipInY col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-gavel"></i>
                                            </div>
                                            <div class="count"><i class="fa fa-paperclip"></i> <span id="notifi_tramite_aprobar">0</span></div>
                                            @if(departamentoLogueado()['jefe_departamento'] == 1)
                                                <h3>Trámites por aprobar</h3>
                                                <p>Para ver todos los trámites que aún no ha aprobado haga click <a href="{{ url('gestionBandeja/aprobarEnvio') }}"><i class="fa fa-hand-o-right"></i> Aquí.</a></p>
                                            @else
                                                <h3>Trámites no aprobados</h3>
                                                <p>Para ver todos los trámites enviados o atendidos haga click <a href="{{ url('gestionBandeja/atendidosEnviados') }}"><i class="fa fa-hand-o-right"></i> Aquí.</a></p>                                           
                                            @endif

                                        </div>
                                    </div>

                                    <div class="animated flipInY col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-exclamation-triangle"></i>
                                            </div>
                                            <div class="count"><i class="fa fa-paperclip"></i> <span id="notifi_tramite_revision">0</span></div>
                
                                            <h3>Trámites en revisión</h3>
                                            <p>Para ver todos los trámites devueltos para realizar correcciones haga click <a href="{{ url('gestionBandeja/enRevision') }}"><i class="fa fa-hand-o-right"></i> Aquí.</a></p>
                                        </div>
                                    </div>

                            </div>


                          </div>      
    
                        </div>
                      </div>
                    </div>
                  </div>
                
            
              <!-- /page content -->

    {{-- MENSAJE DE INFORMACIÓN PARA LA VISTA PRINCIPAL --}}
    @if(session()->has('mensajeGeneral'))
        <script type="text/javascript">
            $(document).ready(function () {
                new PNotify({
                    title: 'Mensaje de Información',
                    text: '{{session('mensajeGeneral')}}',
                    type: '{{session('status')}}',
                    hide: true,
                    delay: 5000,
                    styling: 'bootstrap3',
                    addclass: 'mensajeInfo'
                });
            });
        </script> 
    @endif


    <script type="text/javascript">
        
        $(document).ready(function () {
            ajustarContenedor();
        });

        $(window).resize(function() {
            ajustarContenedor();
        });

        function ajustarContenedor(){
            var heightVentana = $(window).height()-70;
            console.clear();
            console.log(heightVentana);
            $("#widgets_content").css("min-height",heightVentana+"px");
        }
    </script>


    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>    
    <!-- Chart.js -->
    <script src="../vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- jQuery Sparklines -->
    <script src="../vendors/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
    <!-- easy-pie-chart -->
    <script src="../vendors/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

@endsection