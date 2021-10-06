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

    .ocultar{
      display: none;
    }

    .icon_stop{
        color: #ff3030;
        font-size: 28px;
    }

    .btn-outline-danger{
      color: #d9534f;
    }

  </style>

    @if(session()->has('iddetalle_tramite'))
      <div id="content_button_click">
          <input type="hidden" id="button_click" value="{{session('iddetalle_tramite')}}">
          <script type="text/javascript">
              $(document).ready(function () {
                  setTimeout(() => {
                      $("#btn_detalle_"+$("#button_click").val()).click();
                      $("#content_button_click").remove();
                  }, 500);
              });
          </script>        
      </div>

    @endif

    <div id="listaTramites_entrada" class="row center-block">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div  class="panel panel-dark">
            <div class="panel-heading"><i class="fa fa-cloud-download"></i> Bandeja de Entrada</h2></div>
            <div class="panel-body" style="margin-top: 10px;">

                {{-- COMBOS PARA REALIZAR LOS FILTROS --}}
                  <form class="form-horizontal form-label-left input_mask">
                      <div class="col-md-6 col-sm-6 col-xs-12 form-group ">
                        <label for="cmb_departamento">Departamento</label>
                        <div class="input-group">

                          <span class="input-group-btn">
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
                          </span>
                        </div>
                      </div>
                      <div class="col-md-6 col-sm-6 col-xs-12 form-group ">
                        <label for="cmb_tipoTramite">Tipo Trámite</label>
                        <div class="input-group">
                          <span class="input-group-btn">
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
                          </span>
                        </div>
                      </div>
                  </form>
                {{-- fin --}}

                {{-- TABLA DE TRAMITES ENTRANTES --}}
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
                    <div class="title_button">
                      <h2>Lista de trámites</h2>
                      <div class="clearfix"></div>
                    </div>
                    <div class="table-responsive">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="hidden" id="iddepaLog" value="{{departamentoLogueado()['iddepartamento']}}">
                                <table style="color: black"  id="tabla_tramites" class="dinamic_table table table-striped table-bordered dataTable no-footer text-center" role="grid" style="width: 100%;" aria-describedby="datatable_bandeja">
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_desc" style="width: 10px;">Prioridad</th>
                                            <th class="sorting" style="width: 259px;">Código</th>
                                            <th class="sorting" style="width: 259px;">Fecha</th>
                                            <th class="sorting" style="width: 259px;">Origen</th>
                                            <th class="sorting" style="width: 259px;">Asunto</th>
                                            <th class="sorting" style="width: 10px;">Acción</th>
                                            <th class="sorting ocultar" style="width: 10px;"></th>
                                            <th class="sorting ocultar" style="width: 10px;"></th>
                                        </tr>
                                    </thead>

                                    <tbody >
                                        @if(isset($listaTramite))

                                            @foreach($listaTramite as $key=>$destino)
                                              @php
                                                  $mostrarTerminar = false;
                                                  if(is_null($destino->detalle_tramite->flujo)){ // flujo no definido
                                                    if($destino->tipo_envio == "P"){
                                                      $mostrarTerminar = true;
                                                    }
                                                  }else{ // flujo definido
                                                    foreach ($destino->detalle_tramite->flujo->flujo_hijo as $fh => $flujo_hijo){
                                                      if($flujo_hijo->iddepartamento == departamentoLogueado()['iddepartamento']){
                                                        if($flujo_hijo->tipo_flujo == "G" && $flujo_hijo->estado_finalizar == 1){
                                                          $mostrarTerminar = true;
                                                          break;
                                                        }
                                                      }
                                                    }
                                                  }
                                              @endphp
                                                <tr role="row" class="odd">
                                                    <td class="sorting_1 todo_mayus"><center> {{$destino->detalle_tramite->tramite->prioridad->descripcion}} </center></td>
                                                    <td class="todo_mayus">{{ $destino->detalle_tramite->tramite->codTramite }}</td>
                                                    <td class="todo_mayus">{{ $destino->detalle_tramite->tramite->fechaCreacion }}</td>
                                                    <td class="todo_mayus">{{ $destino->detalle_tramite->departamento_origen->nombre }}</td>
                                                    <td class="todo_mayus">{{ $destino->detalle_tramite->asunto }}</td>
                                                    <td width="5%" class="bg-warning">
                                                      <button type="button" id="btn_detalle_{{$destino->detalle_tramite->iddetalle_tramite}}" onclick="verTramite('{{$destino->detalle_tramite->iddetalle_tramite_encrypt}}',this)" class="btn btn-sm btn-info" style="margin-bottom: 0;"><i class="fa fa-eye"></i> Ver Detalle</button>
                                                    </td> 
                                                    {{-- botones solo permitidos para destinos 'PARA' --}}
                                                    {{-- estos botones estan ocultos --}}
                                                    @if($destino->tipo_envio =="P")
                                                        <td width="5%" class="botones_para ocultar" data-detalle = "{{$destino->detalle_tramite->iddetalle_tramite_encrypt}}">                                                                                                               
                                                            <a href="{{url('detalleTramite/atenderDetalleTramite?iddetalle_tramite='.$destino->detalle_tramite->iddetalle_tramite_encrypt)}}" class="btn btn-sm btn-warning" style="margin-bottom: 0;"><i class="fa fa-edit"></i></a>
                                                        </td>

                                                        @if($mostrarTerminar)
                                                          <td width="5%" class="botones_para ocultar">
                                                              <a href="{{url('detalleTramite/terminarTramite?iddetalle_tramite='.$destino->detalle_tramite->iddetalle_tramite_encrypt)}}" class="btn btn-sm btn-success" style="margin-bottom: 0;"><i class="fa fa-thumbs-o-up"></i></a>
                                                          </td>
                                                        @else
                                                          <td width="5%" class="ocultar"> <i class="fa fa-ban icon_stop"></i> </td>                                                         
                                                        @endif

                                                    @else
                                                        <td width="5%" class="ocultar"> <i class="fa fa-ban icon_stop"></i> </td>
                                                        <td width="5%" class="ocultar"> <i class="fa fa-ban icon_stop"></i> </td>
                                                    @endif    
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
    

    {{-- modal para registrar el detalle de la devolucion --}}

    <div id="modal_detalle_devolver" data-backdrop="static" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog ">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                  </button>
                  <h4 class="modal-title" id="myModalLabel">Devolver el trámite</h4>
              </div>

              <form id="frm_devolverTramite" action="" method="POST"  enctype="multipart/form-data" class="form-horizontal form-label-left">
                  {{ csrf_field() }}
                  <input type="hidden" name="_method" value="POST">

                  <div class="modal-body">
                      <div class="form-group">
                          <label for="" class="col-md-12 col-sm-12 col-xs-12">Descripción respecto a los cambios a realizar</label>
                          <div class="col-md-12 col-sm-12 col-xs-12">
                              <textarea type="text" name="textarea_detalle_revision" id="textarea_detalle_revision" placeholder="Ingrese el motivo por el que se devuelve el trámite" rows="5" class="date-picker form-control col-md-7 col-xs-12 sinespecial" required="required" style="text-transform: uppercase;"></textarea>
                              <span class="sinespecialMsj"></span>  
                          </div>
                      </div>
                  </div>

                  <div class="modal-footer">  
                      <button type="button" class="btn btn-default btn-padding-lg" data-dismiss="modal"><i class="fa fa-thumbs-o-down"></i> Cancelar</button>  
                      <button type="submit" class="btn btn-primary btn-padding-lg"><i class="fa fa-history"></i> Devolver</button>                  
                  </div>

              </form>
          </div>
      </div>
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
              "language": datatableLenguaje(datatable),
          });
  
        });
  
    </script>

    {{-- jquery para gestionar la bandeja de entrada --}}
    <script src="{{asset('js/TramiteDepartamental/bandejas/gestionEntrada.js')}}"></script>
   <!-- Datatables -->
   <script src="{{asset('vendors/datatables.net/js/jquery.dataTables.min.js')}}"></script>
   <script src="{{asset('vendors/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>

@endsection