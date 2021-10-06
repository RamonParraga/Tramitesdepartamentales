

    @if(session()->has('mensaje'))
      <script type="text/javascript">
          $(document).ready(function () {
              new PNotify({
                  title: 'Mensaje de Información',
                  text: '{{session('mensaje')}}',
                  type: '{{session('status')}}',
                  hide: true,
                  delay: 4000,
                  styling: 'bootstrap3',
                  addclass: ''
              });
          });
      </script> 
  @endif


<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
        
        <h2><i class="fa fa-edit"></i> Registrar Estructura Documento</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br>
        <div id="administradorEstuctura"></div>
        <form  method="POST"  id="id_frmestructuradocumento" class="form-horizontal form-label-left" >
            {{ csrf_field() }}
          <input id="method_estructuradocumento" type="hidden" name="_method" value="POST">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_anio">Año:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="anio" id="id_anio" placeholder="Agregar año"  required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_secuencia">Secuencia:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="secuencia" id="id_secuencia" placeholder="Agregar inicio de secuencia"  required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_departamento">Selecionar departamento:<span class="required">*</span>
              </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="chosen-select-conten">
                        <select data-placeholder="Seleccione un departamento"  name="iddepartamento" id="TP_departamento" class="chosen-select form-control" tabindex="5">        
                            <option value="" >Seleccione un departamento</option>
                            
                            @foreach ($departamento as $departamento)
                                <option class="TP_optionDe" value="{{$departamento->iddepartamento}}">{{$departamento->nombre}}</option>
                            @endforeach
    
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_tipodocumento">Selecionar un Tipo de Documento:<span class="required">*</span>
              </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="chosen-select-conten">
                        <select data-placeholder="Seleccione un Tipo de Documento"  name="idtipodocumento" id="TP_tipodocumento" class=" chosen-select form-control" tabindex="5">        
                            <option value="" >Seleccione un Tipo de Documento</option>
                            
                            @foreach ($tipodocumento as $tipodocumento)
                                <option class="TP_option" value="{{$tipodocumento->idtipo_documento}}">{{$tipodocumento->descripcion}}</option>
                            @endforeach
    
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" id="btn_estructuracancelar">
                <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Guardar</button>
                <button type="button" id="btn_modalNuevoAnio" onclick="modalNuevoAnio()" class="btn btn-warning"><i class="fa fa-cog"></i> Nuevo Año</button>
                <button type="button"   id="btnCancelarFormato" class="btn btn-danger hidden"><i class="fa fa-times"></i> Cancelar</button>
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