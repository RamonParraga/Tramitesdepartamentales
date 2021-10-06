<div class="row" id="admlistado">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
        
        <h2><i class="fa fa-edit"></i> Listado de Archivos Físicos de los Trámites</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br>


        @if (count($listaGestionArchivo) <= 0)
          <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>MENSAJE!</strong> No existen trámites para mostrar.
            </div>
        @else
                
                {{-- COMBOS PARA REALIZAR LOS FILTROS --}}
                 <form  method="POST"  id="id_frmbodega"  data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" accept-charset="UTF-8">
            {{ csrf_field() }}
          <input id="method_bodega" type="hidden" name="_method" value="POST">

               <div class="form-group">
                        <label class="control-label col-md- col-sm-3 col-xs-12" for="">Método de búsqueda<span class="required"></span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                            <div class="check_rqs">
                                <label for="check_lunes">
                                    <input type="checkbox" value="General" id="check_fecha" class="flat check_rqs" name="check_fecha"> <strong class="no_selecionar">Fecha</strong>
                                </label>
                                <label for="check_martes">
                                        <input type="checkbox" value="Area" id="check_tramite" class="flat check_rqs" name="check_tramite" > <strong class="no_selecionar">Trámite</strong>
                                </label>
                                 <label for="check_martes">
                                        <input type="checkbox" value="Area" id="check_lugar" class="flat check_rqs" name="check_lugar" > <strong class="no_selecionar">Bodega</strong>
                                </label>
                                <label for="check_martes">
                                        <input type="checkbox" value="Area" id="check_ultmio" class="flat check_rqs" name="check_lugar" > <strong class="no_selecionar">Últimos 10 registros</strong>
                                </label>

                                
                            </div>

                        </div>
                    </div>
                  </form>
                  <div id="busquedafecha" class="hidden">
                  <form class="form-horizontal form-label-left input_mask" id="fecha_">

                    <div class="form-group">
                      <!-- <div class="col-md-4 col-sm-6 col-xs-12 form-group "> -->
                        <label class="control-label col-md- col-sm-3 col-xs-12" for=""for="cmb_departamento">Fecha Inicio:</label>
                         <div class="col-md-6 col-sm-6 col-xs-12 form-group ">

                          <input type="date" name="inicio" id="inicio" class="form form-control col-md-6 col-sm-6 col-xs-12" required>

                        </div>
                      </div>

                      <div class="form-group">
                      <!-- <div class="col-md-4 col-sm-6 col-xs-12 form-group "> -->
                        <label class="control-label col-md- col-sm-3 col-xs-12" for=""for="cmb_departamento">Fecha Fin:</label>
                         <div class="col-md-6 col-sm-6 col-xs-12 form-group ">
                            <input type="date" name="fin" required id="fin" class="form form-control col-md-6 col-sm-6 col-xs-12">
                        </div>
                      </div>
                       <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                               <input type="button" name="search" id="search" value="Buscar" class="btn btn-info" onclick="filtratArchivoporfechas()" />

                            </div>
                        </div>
                        <div class="ln_solid"></div>
                            

                           <!--  <input type="submit" name="guardar" value="guardar" /> -->
                      
                  </form>
                </div>


                  <div id="busquedatexto" class="hidden">
                   <form class="form-horizontal form-label-left input_mask">
                     
                    <div class="form-group">
                      <!-- <div class="col-md-4 col-sm-6 col-xs-12 form-group "> -->
                        <label class="control-label col-md- col-sm-3 col-xs-12" for=""for="cmb_departamento">Descripción:</label>
                         <div class="col-md-6 col-sm-6 col-xs-12 form-group ">
                          <input type="text" name="busqueda" id="busqueda" class="form form-control col-md-6 col-sm-6 col-xs-12">

                        </div>
                      </div>
                     
                       <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                               <input type="button" name="search" id="search" value="Buscar" class="btn btn-info" onclick="filtrarArchivoportexto()" />

                            </div>
                        </div>
                        <div class="ln_solid"></div>
                            
                  </form>
                </div>

                  <div id="busquedalugar" class="hidden">
                   <form class="form-horizontal form-label-left input_mask">
                     <div class="form-group">
                      <!-- <div class="col-md-4 col-sm-6 col-xs-12 form-group "> -->
                        <label class="control-label col-md- col-sm-3 col-xs-12" for=""for="cmb_departamento">Bodega:</label>
                         <div class="col-md-6 col-sm-6 col-xs-12 form-group ">

                         <div class="chosen-select-conten">
                        
                                <select data-placeholder="Seleccione una bodega"  name="cmb_lugar" id="cmb_lugar" required="required" class="chosen-select form-control" tabindex="5">
                                    @if(isset($listaSeccion))
                                        @foreach($listaSeccion as $bodega) 
                                          <option value=""></option>
                                            <option class="option_lugar" value="{{$bodega->id_seccion}}">{{$bodega->sector->bodega['nombre']}} -- {{$bodega->sector['descripcion']}} -- {{$bodega->descripcion}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                        </div>
                      </div>
                     
                       <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                               <input type="button" name="search" id="search" value="Buscar" class="btn btn-info" onclick="filtrarArchivoporlugar()" />

                            </div>
                        </div>
                        <div class="ln_solid"></div>
                  </form>
                </div>


                <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
                    <div class="title_button">
                      <h2>Lista de trámites</h2>
                      <div class="clearfix"></div>
                    </div>

                <div class="table-responsive">
                    <div class="row">
                        
                              <table style="color: black"  id="tabla_tramites" class="dinamic_table table table-striped table-bordered dataTable no-footer text-center" role="grid" style="width: 100%;" aria-describedby="datatable_bandeja">
                                <thead>
                                    <tr role="row">
                                        
                                        <th class="sorting" style="width: 140px;">Código Trámite</th>
                                        <th class="sorting" style="">Asunto</th>
                                        <th class="sorting" style="">Observación</th>
                                        <th class="sorting" style="">F.Registro</th>
                                        <th class="sorting" style="">F.Movimiento</th>
                                        <th class="sorting" style="">Bodega</th>
                                        <th class="sorting" style="">Carpeta </th>
                                        <th class="sorting" style="width: 10px;"></th> 

                                    </tr>
                                </thead>
                                <tbody id="id_tablaprioridadtramite">

                                                                         
                                            <tr role="row">
                                                
                                               <td class="todo_mayus"></td>
                                                <td class="todo_mayus"></td>
                                                <td class="todo_mayus"></td>
                                              
                                                <td class="todo_mayus"></td>
                                                <td class="todo_mayus"></td>
                                                
                                                <td class="todo_mayus"></td>
                                                <td class="todo_mayus"></td>

                                                <td class="paddingTR" style="text-align: center; vertical-align: middle;">
                                                    <center>

                                                    

                                                    </center>

                                                </td>  
                                                  

                                               
                                                
                                                
                                                
                                            </tr>
                                                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
            @endif                       
        </div>
    </div>
</div>

<!-- 
<script type="text/javascript">
        $(document).ready(function () {
            $("#id_tablalistado").DataTable({
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por pagina",
            "zeroRecords": "No se encontraron resultados en su busqueda",
            "searchPlaceholder": "Buscar registros",
            "info": "Mostrando registros de _START_ al _END_ de un total de  _TOTAL_ registros",
            "infoEmpty": "No existen registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
        }
    }); 
            $('.collapse-link').click();
            $('.datatable_wrapper').children('.row').css('overflow','inherit !important');
            $('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0','overflow-x':'inherit'});
        });
    </script> -->

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
              "order": [[ 1, "desc" ]],
          });
          $('#check_ultmio').iCheck('check');
  
        });
  
    </script>


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


