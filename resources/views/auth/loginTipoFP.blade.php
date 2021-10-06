<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{asset('images/logotitulo.ico')}}" type="image/png">

    <title>Tramites Departamentales</title>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    {{-- nuevos estilos de boostrap botones --}}
    <link rel="stylesheet" href="{{asset('/css/nuevosEstilosBootstrap4.css')}}">
    {{-- estilos para login y selecicon de tipo fp--}}
    <link rel="stylesheet" href="{{asset('/css/estilosLoginTipoFP.css')}}">

  </head>

  <body class="login">
    <div class="full-page-background-1">
    <div class="full-page-background-2">
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form" style="margin-top: 15%;">
          <section class="login_content contenedorSelecTipoFP">
              <center>
                <div class="contenAvatar">
                    <img src="{{asset('/images/user.png')}}" alt="">
                </div>
              </center>
             <form class="form-horizontal" method="POST" action="{{ route('seleccionarTipoFP') }}">
                {{ csrf_field() }}

                <h4>
                  <span class="border-nombre-user">
                    @guest
                      <span class="nombre-user">No logueado (este texto no se muestra</span>
                    @else
                      <span class="nombre-user">{{auth()->user()->name}}</span>
                    @endguest
                    
                  </span>
                </h4>

                <div class="form-group comboTipoFP">
                  <label class="control-label col-md-1 col-sm-1 col-xs-12"></label>
                  <div class="col-md-10 col-sm-10 col-xs-12">
                    <select class="form-control" name="cmb_seleccionarTipoFP" required="required">
                      <option value="" selected disabled="true">Seleccione un Tipo</option>
                      @isset($listatipoFPasignados)
                          @foreach ($listatipoFPasignados as $tipoFPasignado)
                              <option value="{{encrypt($tipoFPasignado->tipofp->idtipoFP)}}">
                                {{$tipoFPasignado->tipofp->descripcion}}
                              </option>
                          @endforeach
                      @endisset
                      
                    </select>
                  </div>
                  <label class="control-label col-md-1 col-sm-1 col-xs-12"></label>
                </div>
      
                


                <div>
                  <button type="submit" class="btn btn-outline-primary submit"><i class="fa fa-sign-in"></i> Continuar</button>
                </div>

              <div class="clearfix"></div>

              <div class="separator">
                <div class="clearfix"></div><br/>
                <div>
                  <i class="fa fa-file-text-o" style="font-size: 20px; margin-right: 5px;"></i> Tramites Departamentales Chone
                  <p>©2019 Todos los derechos Reservados</p>
                </div>
              </div>

            </form>
          </section>
        </div>

        <div id="register" class="animate form registration_form">
          <section class="login_content">
            
          </section>
        </div>
      </div>
    </div>
    
    </div>

    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.min.js"></script>


  </body>
</html>

