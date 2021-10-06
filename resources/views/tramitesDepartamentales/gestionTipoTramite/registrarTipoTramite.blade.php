
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
      
      <h2><i class="fa fa-edit"></i> Registrar tipo de tramite</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <br>
      <form  method="POST"  id="id_frmtipotramite"  data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" accept-charset="UTF-8">
          {{ csrf_field() }}
        <input id="method_tipotramite" type="hidden" name="_method" value="POST">

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_descripcion">Descripcion: <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input type="text" id="id_descripcion" name="descripcion" required="required" class="form-control col-md-7 col-xs-12">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_nombre">Tipo: <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input type="text" name="tipo" id="id_nombre" required="required" class="form-control col-md-7 col-xs-12">
            </div>
          </div>

          <div class="form-group">
            <label for="id_ayuda" class="control-label col-md-3 col-sm-3 col-xs-12">Ayuda: <span class="required">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <input id="id_ayuda" class="form-control col-md-7 col-xs-12" type="text" name="ayuda">
            </div>
          </div>

          <div class="form-group"  style="user-select: none;">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
              <div class="col-md-6 col-sm-6 col-xs-12 ">
                  <label class="label_finaliar_fujo " for="input_tramite_global2">
                      <input type="checkbox" id="input_tramite_global" name="input_tramite_global" class="flat check_rqs"> <strong>Trámite global</strong>
                  </label> 
              </div>                               
          </div>

          <div class="form-group departamento">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_departamento">Departamento: <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12"> 
                <div class="chosen-select-conten componentLeft">
                    <select data-placeholder="Seleccione un departamento"  name="iddepartamento" id="iddepartamento" class="chosen-select form-control" tabindex="5">        
                        
                        <option value="" >Seleccione un departamento</option>                        
                        @foreach ($departamentos as $departamentos)
                            <option class="TP_option" value="{{$departamentos->iddepartamento}}">{{$departamentos->nombre}}</option>
                        @endforeach

                    </select>                    
                </div>
                <button onclick="agregar_departamento()" id="btn_agregar_departamento" type="button" class="btn btn-outline-primary componentRigth" style="margin-right: 0px;"><i class="fa fa-plus-square"></i> Agregar</button>
            </div>              
                
          </div>
          
          <div id="content_departamentos" class="form-group hidden" style="margin-bottom: 0px;">
                        
              <div class="col-md-3 col-sm-3 col-xs-12"></div>
              <div class="col-md-6 col-sm-6 col-xs-12">
                  <hr style="margin-top: 5px; margin-bottom: 5px;">
                  <h4><i class="fa fa-align-left"></i> Lista de departamentos.</h4>
                  <div id="conten_add_departamentos"  onmouseup="comprobar_numero_actividades(this);" style="margin-bottom: 10px;">
                      {{-- AQUÍ SE CARGARAN CON JQUERY LA ACTIVIDAD AGREGADA --}}
                  </div>
              </div>
                        
          </div>
          
          <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
              <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Guardar</button>
              <button type="button" id="btn_tipotramitecancelar" class="btn btn-warning hidden">Cancelar</button>
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
<script>
     
        
    </script>            
  @endif
{{-- FIN --}}