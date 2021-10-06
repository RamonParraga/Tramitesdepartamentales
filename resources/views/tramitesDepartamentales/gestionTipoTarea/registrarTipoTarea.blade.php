<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
        
        <h2><i class="fa fa-edit"></i> Registrar tipo de tarea</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br>
        <form  method="POST"  id="id_frmtipotarea"  data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" accept-charset="UTF-8">
            {{ csrf_field() }}
          <input id="method_tipotarea" type="hidden" name="_method" value="POST">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_descripcion">Descripcion:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="descripcion" id="id_descripcion" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="estado">Estado:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="chosen-select-conten">
                      <select data-placeholder="Seleccione un departamento"  name="estado" id="id_estado" class="chosen-select form-control required" tabindex="5">        
                              <option value="" >Seleccione un estado</option>
                              <option class="TP_option" value="ACTIVO">ACTIVO</option>
                              <option class="TP_option" value="DESACTIVO">DESACTIVO</option>
                      </select>
                  </div>
              </div>
            </div>
            
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Guardar</button>
                <button type="button" id="btn_tipotareacancelar" class="btn btn-warning hidden">Cancelar</button>
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