<div class="row" id="adm">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
        
        <h2><i class="fa fa-edit"></i> Registrar Sección</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br>
        <form  method="POST"  id="id_frmseccion"  data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" accept-charset="UTF-8">
            {{ csrf_field() }}
          <input id="method_seccion" type="hidden" name="_method" value="POST">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_nombrel">Descripción:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="descripcion" id="id_descripcion" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

          
          
           

                   


            <div class="form-group departamento">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_departamento">Bodega: <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12"> 
                <div class="chosen-select-conten">
                    <select onchange="cargartdSector()" data-placeholder="Seleccione una bodega" name="bodega" id="idbodega" class="chosen-select form-control" tabindex="5">  
                        @if(isset($listaBodega))
                            @foreach($listaBodega as $bodega) 
                              {{-- <option value=""></option>
                                <option class="option_sector" value="{{$bodega->bodega->id_bodega}}">{{$bodega->bodega->nombre}}</option> --}}

                                <option value=""></option>
                                <option class="option_sector" value="{{$bodega->id_bodega}}">{{$bodega->nombre}}</option>
                            @endforeach
                        @endif
                    </select>                    
                </div>            
          </div>
        </div>

        <div class="form-group departamento">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_departamento">Sector: <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12"> 
                <div class="chosen-select-conten">
                    <select data-placeholder="Seleccione un sector" name="idsector" id="idsector" class="chosen-select form-control" tabindex="5">  
                                    {{-- @if(isset($listaBodega))
                                        @foreach($listaBodega as $bodega)  --}}
                                          <option classs="option_sector" value=""></option>
                                           {{--  <option class="option_sector" value="{{$bodega->bodega->id_bodega}}">{{$bodega->bodega->nombre}}</option>
                                        @endforeach
                                    @endif --}}
                                </select>                    
                </div>
               
                
          </div>
        </div>

         {{--  <div id="content_bodegas" class="form-group hidden" style="margin-bottom: 0px;">
                        
              <div class="col-md-3 col-sm-3 col-xs-12"></div>
              <div class="col-md-6 col-sm-6 col-xs-12">
                  <hr style="margin-top: 5px; margin-bottom: 5px;">
                  <h4><i class="fa fa-align-left"></i> Lista de departamentos.</h4>
                  <div id="conten_add_bodegas"  onmouseup="comprobar_numero_actividades(this);" style="margin-bottom: 10px;"> --}}
                      {{-- AQUÍ SE CARGARAN CON JQUERY LA ACTIVIDAD AGREGADA --}}
                {{--   </div>
              </div>
                        
          </div> --}}

            
            
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Guardar</button>
                <button type="button" id="btn_seccioncancelar" class="btn btn-warning hidden">Cancelar</button>
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