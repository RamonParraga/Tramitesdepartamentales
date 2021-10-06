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
      padding: 5px;
      font-size: 14px;
      display: block;
      text-transform: none;
      font-weight: 500;
    }

  </style>


    <div id="listaTramites_entrada" class="row center-block">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div  class="panel panel-info-2">
            <div class="panel-heading"><i class="fa fa-cloud-download"></i> Bandeja de Finalizados</h2></div>
            <div class="panel-body" style="margin-top: 10px;">

                {{-- COMBOS PARA REALIZAR LOS FILTROS --}}
                  <form class="form-horizontal form-label-left input_mask">
                     <div class="form-group">
                      <!-- <div class="col-md-4 col-sm-6 col-xs-12 form-group "> -->
                        <label class="control-label col-md- col-sm-3 col-xs-12" for=""for="cmb_departamento">Departamento:</label>
                         <div class="col-md-6 col-sm-6 col-xs-12 form-group ">

                         <div class="chosen-select-conten">
                        
                              <select data-placeholder="Seleccione una parroquia" id="cmb_departamento" required="required" class="chosen-select form-control cmb_filtrarTramite" tabindex="5" >
                                  <option value="{{encrypt('0')}}" selected>--TODOS LOS DEPARTAMENTO--</option>
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

                     
                    
                  </form>
                {{-- fin --}}

                {{-- TABLA DE TRAMITES ENTRANTES --}}
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
                    <div class="title_button"  style="margin-bottom: 10px;">
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
                                <table style="color: black"  id="tabla_tramites" class="dinamic_table table table-striped table-bordered dataTable no-footer text-center" role="grid" style="width: 100%;" aria-describedby="datatable_bandeja">
                                    <thead>
                                        <tr role="row">
                                            <!-- <th class="sorting_desc" style="width: 10px;">Nº</th> -->
                                            <th class="sorting" style="width: 110px;">Fecha</th>
                                            <th class="sorting" style="width: 160px;">Código</th>
                                            <th class="sorting" style="">Origen</th>
                                            <th class="sorting" style="">Asunto</th>
                                            <th class="sorting" style="">Motivo</th>                                     
                                            <th class="sorting" style="width: 10px;">Acción</th>                                                                                    
                                        </tr>
                                    </thead>

                                    <tbody >
                                        @if(isset($listaDetalleTramite))

                                            @foreach($listaDetalleTramite as $key=>$detalle)
                                            

                                             
                                                <tr role="row" class="odd">
                                                   <!--  <td class="sorting_1"><center> {{$key+1}} </center></td> -->
                                                    <td class="todo_mayus">{{ $detalle->fecha }}</td>
                                                    <td class="todo_mayus">{{ $detalle->tramite->codTramite }}</td>
                                                    <td class="todo_mayus">{{ $detalle->detalle_tramite_padre->departamento_origen->nombre }}</td>
                                                    <td class="todo_mayus">{{ $detalle->detalle_tramite_padre->asunto}}</td>
                                                    <td class="todo_mayus">{{ $detalle->observacion}}</td>
                                                    <td width="5%" class="bg-warning">
                                                      <button type="button" onclick="verTramite('{{$detalle->iddetalle_tramite_encrypt }}')" class="btn btn-sm btn-info" style="margin-bottom: 0;"><i class="fa fa-eye"></i> Ver Detalle</button>
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
            <button id="btn_finalizados_revertir" type="button" onclick="" class="btn btn-success btn_regresar" style="color: #fff"><i class="fa fa-history"></i> Revertir</button>
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
    <script src="{{asset('js/TramiteDepartamental/bandejas/gestionFinalizado.js')}}"></script>
   <!-- Datatables -->
   <script src="{{asset('vendors/datatables.net/js/jquery.dataTables.min.js')}}"></script>
   <script src="{{asset('vendors/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>

@endsection