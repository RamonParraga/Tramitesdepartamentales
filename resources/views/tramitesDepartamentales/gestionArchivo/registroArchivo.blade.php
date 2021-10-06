<div class="row" id="adm">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
        
        <h2><i class="fa fa-edit"></i> Registro de Archivos Físicos de los Trámites</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br>


        @if (count($listaTramite) <= 0)
          <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>MENSAJE!</strong> No existen trámites para archivar.
            </div>
        @else
                        



        

          <form  method="POST"  id="id_frmgestionarchivo"  data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" accept-charset="UTF-8">
            {{ csrf_field() }}
                <input id="method_gestionarchivo" type="hidden" name="_method" value="POST">

              <div id="tablaeditar" class="hidden">
              <div class="table-responsive ">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="id_tablagestion3" class="table table-striped table-bordered">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable-keytable" rowspan="1" colspan="1" aria-label="Funcionario Público: activate to sort column ascending"  aria-sort="descending">#</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Código Trámite</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Asunto</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" >Observacion</th>
                                        
                                        

                                       
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody id="id_tablaprioridadtramite3"> 
                                                                            
                                           <!--  <tr role="row">
                                                <td class="sorting_1">{{ 1}}</td>
                                                <td><input type="text"id="codigotramite"name="as"></td>
                                                <td><p id="asunto"></p></td>
                                                 <td id="observacion"></td>
                                                      <td>
                                                        <label for="check_miercoles">
                                                            <input type="checkbox" value="" id="estado" class="flat validar check_actividad" name="tramite"> <strong class=" no_select_text">Registrar</strong>
                                                        </label>
                                                        <input type="hidden" name="idcodigotramite" id="idcodigotramite">
                                                    </td>
                                               
                                                
                                                
                                                
                                            </tr>                    -->                                                 
                                </tbody>
                                
                            </table>
                  </div>
                  </div>
                  </div>  
                  </div>    
        
          <div id="tablaregistro" class="">
          <div class="table-responsive">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="id_tablagestion" class="table table-striped table-bordered">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable-keytable" rowspan="1" colspan="1" aria-label="Funcionario Público: activate to sort column ascending"  aria-sort="descending">#</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Código Trámite</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Asunto</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" >Observación</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="">Estado </th>     
                                        

                                       
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody id="id_tablaprioridadtramite">
                                    @if(isset($listaTramite))
                                        @foreach ($listaTramite as $cont=>$listaTramite)                                        
                                            <tr role="row">
                                                <td class="sorting_1">{{ $cont+1}}</td>
                                                <td id="codtram">{{ $listaTramite->codTramite}}</td>
                                                <td>{{ $listaTramite->asunto}}</td>
                                                 <td>{{ $listaTramite->observacion}}</td>
                                                      <td>
                                                        <label for="check_miercoles">
                                                            <input type="checkbox" value="{{$listaTramite->idtramite}}" id="check_id_{{$listaTramite->idtramite}}" class="flat validar check_actividad" name="tramite[]"> <strong class=" no_select_text">Registrar</strong>
                                                        </label>
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

                <div class="form-group bodega">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                           <button type="button" id="btn_modal_bodega" class="btn btn-outline-primary" onclick="" data-toggle="modal" data-target=".modal_Bodega"><i class="fa fa-archive"></i> Asignar bodega</button>
                          
                         </div>
                 </div>

                   {{-- DIV PARA CARGAR LA BODEGA --}}
                            
                            <div id="contenedor_bodega" class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="departamento_padre"></label>
                                <div class="col-md-6 col-sm-6 col-xs-12"> 
                                    
                                    <div id="area_listaBodega" style="display: none;">       
                                        <div class="form-group" style="margin-bottom: 5px; margin-top: 15px;">           
                                            <p for="" style="float: left; margin-right: 10px; margin-bottom: 0px;"><i class="fa fa-align-left"></i> Bodega Seleccionada</p>
                                            <hr style="margin-top: 10px; margin-bottom: 10px; ">
                                        </div>
                                        {{--lista bodega  --}}
                                        <div id="contenedor_bod">
                                            {{-- EL CONTENIDO SE CARGA CON JQUERY --}}
                                        </div>
                                    </div> 

                                    
                               

            

                                </div>   
                            </div>

                                <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion_parroquia">Número Carpeta <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">

                                <input type="text" id="carpeta" name="carpeta" placeholder="Carpeta" required class="form-control">
                               

                            </div>
                        </div>
                          <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-success">Guardar</button>
                                <a href="gestion"><button type="button" id="btn_gestionarchivo_cancelar" class="btn btn-warning hidden">Cancelar</button></a>
                            </div>
                        </div>
                       
                   
                  </form>
                

               

          
        </div>
      @endif
      </div>
    </div>
  </div>


  
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