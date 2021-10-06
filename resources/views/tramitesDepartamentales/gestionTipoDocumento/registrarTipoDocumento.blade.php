<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
        
        <h2><i class="fa fa-edit"></i> Registrar Tipo Documento</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br>
        <form  method="POST"  id="id_frmtipodocumento"  data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" accept-charset="UTF-8">
            {{ csrf_field() }}
          <input id="method_tipodocumento" type="hidden" name="_method" value="POST">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_descripcion">Descripción:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="descripcion" id="id_descripcion" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_abreviacion">Abreviación:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="abreviacion" id="id_abreviacion" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_estructura">Estructura:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="estructura" id="id_estructura" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_secuencia">Secuencia:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="secuencia" id="id_secuencia" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_secuencia">Prioridad:<span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="chosen-select-conten">
                      <select id="id_prioridad" name="prioridad" class="form-control" required>
                        <option value="1">Principal</option>
                        <option value="0">Anexo</option>
                      </select>
                  </div>
                </div>
              </div>
            
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Guardar</button>
                <button type="button" id="btn_tipodocumentocancelar" class="btn btn-warning hidden">Cancelar</button>
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