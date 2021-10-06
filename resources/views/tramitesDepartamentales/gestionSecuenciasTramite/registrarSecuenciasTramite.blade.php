<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
        
        <h2><i class="fa fa-edit"></i> Registrar Secuencias de Trámites</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br>
        <form  method="POST"  id="id_frmSecuenciatramite" action="{{url('secuenciastramite/gestion')}}" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" accept-charset="UTF-8">
            {{ csrf_field() }}
          <input id="method_secuenciatramite" type="hidden" name="_method" value="POST">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_descripcion">Año:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="anio" id="anio" required="required" class="form-control col-md-7 col-xs-12" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_descripcion">Número:<span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="numero" id="numero" required="required" class="form-control col-md-7 col-xs-12" required="required">
              </div>
            </div>
            <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="estado">Prioridad:<span class="required" required>*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
              <div class="chosen-select-conten">
                    <select data-placeholder="Seleccione un periodo"  name="prioridad" id="prioridad_secuencias" class="chosen-select form-control" tabindex="5">                      
                        @isset($listaPrioridad)
                            @foreach ($listaPrioridad as $prioridad)                                
                                <option class="prioridad" value="{{$prioridad->idprioridad_tramite}}">{{$prioridad->descripcion}}</option>    
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>
            </div>
            
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="button" id="btn_Secuenciacancelar" class="btn btn-warning hidden">Cancelar</button>
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