@extends('layouts.service')
@section('contenido')

{{-- Datetables --}}
<link href="{{asset('../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">

  <style type="text/css">
    
    .inputsearch div{
        float: left;
    }

    input[type="search"]{
        width: 100% !important;
    }

    .btn_icon{
        font-size: 18px;
        padding: 0px 8px;
    }

    .btn_regresar{
        margin-left: 0px;
        font-size: 14px;
        font-weight: 700;
        color: #446684;
    }

    td{
        vertical-align: middle !important;
    }
    
    .todo_mayus{
        text-align: left !important;
        text-transform: uppercase !important;
    }

    .icon_stop{
        color: #ff3030;
        font-size: 28px;
    }

    .lable_estado{
        padding: 5px 8px 5px 8px;
        font-size: 14px;
        display: block;
        text-transform: none;
        font-weight: 500;
        border-radius: .90em;
    }

  </style>


    <div id="listaTramites_entrada" class="row center-block">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div  class="panel panel-info-2">
            <div class="panel-heading"><i class="fa fa-cloud-download"></i> Bandeja de Atendidos y Enviados</h2></div>
            <div class="panel-body" style="margin-top: 10px;">

                {{-- COMBOS PARA REALIZAR LOS FILTROS --}}
                  <form class="form-horizontal form-label-left input_mask">
                     <div class="form-group">
                      <!-- <div class="col-md-4 col-sm-6 col-xs-12 form-group "> -->
                        <label class="control-label col-md- col-sm-3 col-xs-12" for=""for="cmb_departamento">Departamento:</label>
                         <div class="col-md-6 col-sm-6 col-xs-12 form-group ">

                         <div class="chosen-select-conten">
                        
                                <select data-placeholder="Seleccione una parroquia" id="cmb_departamento" required="required" class="chosen-select form-control cmb_filtrarTramite" tabindex="5" >
                                  <option value="{{encrypt('0')}}" selected>--EN TODOS LOS DEPARTAMENTO--</option>
                                  @if(isset($listDepartamento))
                                    @foreach($listDepartamento as $departamento)
                                      <option value="{{ $departamento->iddepartamento_encrypt }}">{{ $departamento->nombre }}</option>
                                    @endforeach
                                  @endif
                              </select>

                            </div>

                        </div>
                      </div>
                     
                    
                      <div class="form-group">
                      <!-- <div class="col-md-4 col-sm-6 col-xs-12 form-group "> -->
                        <label class="control-label col-md- col-sm-3 col-xs-12" for=""for="cmb_departamento">Tipo Trámite:</label>
                         <div class="col-md-6 col-sm-6 col-xs-12 form-group ">

                          <div class="chosen-select-conten">
                            <select data-placeholder="Seleccione una parroquia" id="cmb_tipoTramite" required="required" class="chosen-select form-control cmb_filtrarTramite" tabindex="5" >
                                <option value="{{encrypt('0')}}" selected>--TODOS LOS TIPOS DE TRÁMITE--</option>
                                @if(isset($listTipoTramite))
                                  @foreach($listTipoTramite as $tipoTramite)
                                      <option value="{{ $tipoTramite->idtipo_tramite_encrypt }}">{{ $tipoTramite->descripcion }}</option>
                                  @endforeach
                                @endif
                            </select>
                          </div>

                        </div>
                      </div>

                       <div class="form-group">
                      <!-- <div class="col-md-4 col-sm-6 col-xs-12 form-group "> -->
                        <label class="control-label col-md- col-sm-3 col-xs-12" for=""for="cmb_departamento">Atendido / Enviado:</label>
                         <div class="col-md-6 col-sm-6 col-xs-12 form-group ">

                            <div class="chosen-select-conten">
                        
                              <select data-placeholder="Seleccione una parroquia" id="cmb_inicio_atendido" required="required" class="chosen-select form-control cmb_filtrarTramite" tabindex="5" >
                                  <option value="{{encrypt('0')}}" selected>--TODOS LOS TRÁMITE ATENDIDOS Y ENVIADOS--</option>
                                  <option class="optionsolicitud2" value="{{encrypt('A')}}">ATENDIDOS</option>
                                  <option class="optionsolicitud1" value="{{encrypt('E')}}">ENVIADOS</option> 
                              </select>

                            </div>

                        </div>
                      </div>
                     
                     
                    
                  </form>
                {{-- fin --}}

                {{-- TABLA DE TRAMITES ENTRANTES --}}
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
                    <div class="title_button" style="margin-bottom: 15px;">
                      <h2>Lista de trámites</h2>
                      <div class="clearfix"></div>
                    </div>

                    <div id="alerta_info_cabe_pie" class="alert_info_doc" style="display: block;">
                      <i class="fa fa-info-circle" style="margin-right: 5px; font-size: 15px;"></i> 
                      A continuación, se muestran los ultimos {{ $limiteBandeja }} trámites, si necesita información historica estará disponible en la opción buscar. 
                    </div>

                    <div class="table-responsive">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="hidden" id="iddepaLog" value="{{departamentoLogueado()['iddepartamento']}}">
                                <table id="tabla_tramites" style="color: black; width: 100%;" class="dinamic_table table table-bordered dataTable no-footer text-center" role="grid" style="width: 100%;" aria-describedby="datatable_bandeja">
                                    <thead>
                                        <tr role="row">
                                            <!-- <th class="sorting_desc" style="width: 10px;">Nº</th> -->
                                            <th class="sorting" style="width: 110px;">Fecha</th>
                                            <th class="sorting" style="width: 150px;">Código</th>
                                            <th class="sorting" style="">Destino</th>
                                            <th class="sorting" style="">Asunto</th>
                                            <th class="sorting" style="width: 10px;">Inic/Atend</th>
                                            <th class="sorting" style="width: 10px;">Estado</th>
                                            <th class="sorting" style="width: 10px;">Ver</th>
                                                                                       
                                        </tr>
                                    </thead>

                                    <tbody >
                                        @if(isset($listaDetalleTramite))

                                            @foreach($listaDetalleTramite as $key=>$detalle_tramite)

                                              @php
                                                  //cargamos los departamento destino
                                                  $destinos = "";
                                                  $tipo_envio = "";
                                                  foreach($detalle_tramite->destino as $key => $destino){
                                                      if($destino->tipo_envio == "C"){ // enviado como copia
                                                          $tipo_envio = '
                                                              <b style="padding-left: 5px; color: #e60000;"> 
                                                                  <i style="font-size: 16px;" class="fa fa-angle-double-right"></i> 
                                                                  <span style="text-transform: none;">Copia</span> 
                                                                  <i style="font-size: 16px;" class="fa fa-copy"></i>
                                                              </b>';
                                                      }
                                                      $destinos = $destinos."<li>".$destino->departamento->nombre." ".$tipo_envio."</li>";
                                                  }

                                                  //verificamos los estados y la procedencia              
                                                    $atiendeEnvia = "<i><b><center style='text-transform: none;'>Enviado</center></b></i>";
                                                    $colorFila = "";
                                                    $estado = "asdfsd";
                                                    if($detalle_tramite->nivelAtencion > 1){ // es atendido
                                                        $atiendeEnvia = "<i><b><center style='text-transform: none;'>Atendido</center></b></i>"; 
                                                        $colorFila = "bg-warning";
                                                        if($detalle_tramite->aprobado == 1){ $estado = '<span class="label lable_estado label-success">Aprobado</span>'; }
                                                        else{ $estado = '<span class="label lable_estado label-danger">Pendiente</span>'; }
                                                        
                                                    }else{ // es enviado (iniciado desde el departamento)
                                                        if($detalle_tramite->aprobado == 1){ // verificamos si esta enviado
                                                            if($detalle_tramite->tramite->finalizado == 1){ $estado = '<span class="label lable_estado label-primary">Finalizado</span>'; }
                                                            else{ $estado = '<span class="label lable_estado label-warning">En proceso</span>'; }
                                                        }else{
                                                            $estado = '<span class="label lable_estado label-danger">Pendiente</span>';
                                                        }

                                                    }
                                              @endphp
                                             
                                                <tr role="row" class="odd {{ $colorFila }}">

                                                    <td class="todo_mayus">{{ $detalle_tramite->fecha }}</td>
                                                    <td class="todo_mayus">{{ $detalle_tramite->tramite->codTramite }}</td>                                                    
                                                    <td class="todo_mayus"><ul style="margin-bottom: 0px; padding-left: 20px;">@php print($destinos) @endphp</ul></td>
                                                    <td class="todo_mayus">{{ $detalle_tramite->asunto }}</td>
                                                    <td class="todo_mayus"> @php print($atiendeEnvia) @endphp </td>
                                                    <td class="todo_mayus"> @php print($estado) @endphp </td>
                                                    <td width="5%">
                                                      <button type="button" onclick="verTramite('{{$detalle_tramite->iddetalle_tramite_encrypt}}',this)" class="btn btn-sm btn-info btn_icon" data-toggle="tooltip" data-placement="top" title="Ver detalle general del trámite" style="margin-bottom: 0;"><i class="fa fa-eye"></i></button>
                                                    </td> 
                                    
                                                </tr>
                                            @endforeach

                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                  </div>
                {{-- fin --}}

            </div>
        </div>
      </div>
    </div>


    <div id="contet_ver_tramite" style="display: none;">
        <h3 style="margin-top: 60px; margin-bottom: 0px;"> <b>Detalle general del trámite</b> </h3>
        <hr class="hr_line">

            <button type="button" onclick="cerrarDetalleTramite()" class="btn btn-default btn_regresar"><i class="fa fa-mail-reply-all"></i> Regresar</button>              
            <span id="botones_para">
              {{-- se agregan con jquery los botones atender y terminar --}}
            </span>
            
        <hr class="hr_line">
        @include('tramitesDepartamentales.detalleTramite.consultar.detalleTramite')
    </div> 
    

    <script type="text/javascript">
      $(document).ready(function () {
  
          $('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0','overflow-x':'inherit'});
         
          var datatable = {
              placeholder: "Ejm: GADM-000-2020-N"
          }

          $("#tabla_tramites").DataTable({
              dom: ""
              +"<'row' <'form-inline' <'col-sm-6 inputsearch'f>>>"
              +"<rt>"
              +"<'row'<'form-inline'"
              +" <'col-sm-6 col-md-6 col-lg-6'l>"
              +"<'col-sm-6 col-md-6 col-lg-6'p>>>",
              pageLength: 10,
              order: [[ 0, "desc" ]],
              "language": datatableLenguaje(datatable),
          });
  
        });
  
    </script>

    {{-- jquery para gestionar la bandeja de entrada --}}
    <script src="{{asset('js/TramiteDepartamental/bandejas/gestionAtendidoEnviado.js')}}"></script>
   <!-- Datatables -->
   <script src="{{asset('vendors/datatables.net/js/jquery.dataTables.min.js')}}"></script>
   <script src="{{asset('vendors/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>

@endsection