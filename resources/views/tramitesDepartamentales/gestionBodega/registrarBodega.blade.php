<div class="row" id="adm">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
        
        <h2><i class="fa fa-edit"></i> Registrar Bodega</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br>
        <form  method="POST"  id="id_frmbodega"  data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" accept-charset="UTF-8">
            {{ csrf_field() }}
          <input id="method_bodega" type="hidden" name="_method" value="POST">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_nombrel">Nombre:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="nombre" id="id_nombre" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_ubicacionl">Ubicación:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="ubicacion" id="id_ubicacion" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

           {{--   <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_tipol">Tipo:<span class="required">*</span>
              </label>
                 <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="chosen-select-conten">
                        
                                <select data-placeholder="Seleccione una opción"  name="cmb_tipobodega" id="cmb_tipobodega" required="required" class="chosen-select form-control" tabindex="5">
                                    
                                        
                            <option value=""></option>           
                            <option class="optionsolicitud1" value="G">General</option>
                            <option class="optionsolicitud2"value="A">Área</option>
                                            
                                        
                                </select>
                            </div>
                        </div>
                                                       
                   </div> --}}

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="">Tipo<span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                            <div class="check_rqs">
                                <label for="check_lunes">
                                    <input type="checkbox" value="General" id="check_general" class="flat check_rqs"name="check_general"> <strong class="no_selecionar">General</strong>
                                </label>
                                <label for="check_martes">
                                        <input type="checkbox" value="Area" id="check_area"class="flat check_rqs" name="check_area" > <strong class="no_selecionar">Área</strong>
                                </label>
                                
                                
                            </div>

                        </div>
                    </div>


             <div class="form-group hidden"id="content_departamentos">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_areal">Area:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="chosen-select-conten">
                        
                                <select data-placeholder="Seleccione una área"  name="cmb_area" id="cmb_area" required="required" class="chosen-select form-control" tabindex="5">
                                    @if(isset($listaArea))
                                        @foreach($listaArea as $areas) 
                                          <option value=""></option>
                                            <option class="option_area" value="{{$areas->iddepartamento}}">{{$areas->nombre}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
            </div>

            
            
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Guardar</button>
                <button type="button" id="btn_bodegacancelar" class="btn btn-warning hidden">Cancelar</button>
              </div>
            </div>
          </form>
        </div>
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