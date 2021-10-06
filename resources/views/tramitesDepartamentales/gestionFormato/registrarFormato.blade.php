<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
        
        <h2><i class="fa fa-edit"></i> Registrar Formato</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br>
        <form  method="POST" id="id_frmformato"  data-parsley-validate="" class="form-horizontal form-label-left" accept-charset="UTF-8" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input id="method_formato" type="hidden" name="_method" value="POST">

            {{-- IMAGENES DE CABECERA Y FOOTER --}}
              <div class="form-group" style="margin-bottom: 5px; margin-top: 20px;">
                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="departamento_padre"></label>
                <div id="" class="col-md-10 col-sm-10 col-xs-12">
                    <p for="" style="float: left; margin-right: 10px;">Imagenes Cabecera y Pie de pagina</p>
                    <hr style="margin-top: 10px; margin-bottom: 10px; ">
                </div>
              </div>
              
              <div class="form-group">
                <label class="control-label col-md-1 col-sm-1 col-xs-12"></label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <label for="id_cabecerapagina">Cabecera<span class="required">*</span></label>
                  <input type="file" name="cabecerapagina" accept="image/x-png,image/jpeg"  id="id_cabecerapagina" class="form-control col-md-6 col-xs-12">
                </div>
                
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <label for="id_piepagina">Pie de Pagina:<span class="required">*</span></label>
                  <input type="file" name="piepagina" id="id_piepagina" accept="image/x-png,image/jpeg" class="form-control col-md-6 col-xs-12">
                </div>
              </div>

            {{-- /IMAGENES DE CABECERA Y FOOTER --}}
            


            {{-- FORMATO DE PAGINA --}}

              <div class="form-group" style="margin-bottom: 5px; margin-top: 20px;">
                  <label class="control-label col-md-1 col-sm-1 col-xs-12" for="departamento_padre"></label>
                  <div id="" class="col-md-10 col-sm-10 col-xs-12">
                      <p for="" style="float: left; margin-right: 10px;">Formato de Hoja</p>
                      <hr style="margin-top: 10px; margin-bottom: 10px; ">
                  </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-1 col-sm-1 col-xs-12"></label>
                <div class="col-md-3 col-sm-3 col-xs-12">
                  <label for="p_margin_top">Top <span class="required">*</span></label>
                  <input type="number" name="p_margin_top" id="p_margin_top" value="@isset($formatoDocumento){{$formatoDocumento->page_margin_top}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                </div>
                <div class="col-md-2 col-sm-2 col-xs-12">
                  <label for="p_margin_right">Right <span class="required">*</span></label>
                  <input type="number" name="p_margin_right" id="p_margin_right" value="@isset($formatoDocumento){{$formatoDocumento->page_margin_right}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12">
                  <label for="p_margin_bottom">Bottom:<span class="required">*</span></label>
                  <input type="number" name="p_margin_bottom" id="p_margin_bottom" value="@isset($formatoDocumento){{$formatoDocumento->page_margin_bottom}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                </div>
                <div class="col-md-2 col-sm-2 col-xs-12">
                  <label for="p_margin_left">Left:<span class="required">*</span></label>
                  <input type="number" name="p_margin_left" id="p_margin_left" value="@isset($formatoDocumento){{$formatoDocumento->page_margin_left}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                </div>
              </div>
            {{-- /FORMATO DE PAGINA --}}


            {{-- FORMATO DE HEADER --}}
                <div class="form-group" style="margin-bottom: 5px; margin-top: 20px;">
                  <label class="control-label col-md-1 col-sm-1 col-xs-12" for="departamento_padre"></label>
                  <div id="" class="col-md-10 col-sm-10 col-xs-12">
                      <p for="" style="float: left; margin-right: 10px;">Formato de Cabecera</p>
                      <hr style="margin-top: 10px; margin-bottom: 10px; ">
                  </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-1 col-sm-1 col-xs-12"></label>
                    <div class="col-md-5 col-sm-5 col-xs-12">
                      <label for="header_top">Top:<span class="required">*</span></label>
                      <input type="number" name="header_top" id="header_top" value="@isset($formatoDocumento){{$formatoDocumento->header_top}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                    </div>

                    <div class="col-md-5 col-sm-5 col-xs-12">
                      <label for="header_height">Height:<span class="required">*</span></label>
                      <input type="number" name="header_height" id="header_height" value="@isset($formatoDocumento){{$formatoDocumento->header_height}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                    </div>
                </div>
  
            {{-- /FORMATO DE HEADER --}}


            {{-- FORMATO DE FOOTER --}}
                <div class="form-group" style="margin-bottom: 5px; margin-top: 20px;">
                  <label class="control-label col-md-1 col-sm-1 col-xs-12" for="departamento_padre"></label>
                  <div id="" class="col-md-10 col-sm-10 col-xs-12">
                      <p for="" style="float: left; margin-right: 10px;">Formato de pie de página</p>
                      <hr style="margin-top: 10px; margin-bottom: 10px; ">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-1 col-sm-1 col-xs-12"></label>
                  <div class="col-md-5 col-sm-5 col-xs-12">
                    <label for="footer_bottom">Bottom:<span class="required">*</span></label>
                    <input type="number" name="footer_bottom" id="footer_bottom" value="@isset($formatoDocumento){{$formatoDocumento->footer_bottom}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                  </div>

                  <div class="col-md-5 col-sm-5 col-xs-12">
                    <label for="footer_height">Height:<span class="required">*</span></label>
                    <input type="number" name="footer_height" id="footer_height" value="@isset($formatoDocumento){{$formatoDocumento->footer_height}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                  </div>
                </div>                         
                
            {{-- /FORMATO DE FOOTER --}}

            {{-- FORMATO DE CUERPO DOCUMENTO --}}
                <div class="form-group" style="margin-bottom: 5px; margin-top: 20px;">
                  <label class="control-label col-md-1 col-sm-1 col-xs-12" for="departamento_padre"></label>
                  <div id="" class="col-md-10 col-sm-10 col-xs-12">
                      <p for="" style="float: left; margin-right: 10px;">Formato cuerpo del documento</p>
                      <hr style="margin-top: 10px; margin-bottom: 10px; ">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-1 col-sm-1 col-xs-12"></label>
                  <div class="col-md-5 col-sm-5 col-xs-12">
                    <label for="main_left">Left:<span class="required">*</span></label>
                    <input type="number" name="main_left" id="main_left" value="@isset($formatoDocumento){{$formatoDocumento->main_left}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                  </div>

                  <div class="col-md-5 col-sm-5 col-xs-12">
                    <label for="main_right">Right:<span class="required">*</span></label>
                    <input type="number" name="main_right" id="main_right" value="@isset($formatoDocumento){{$formatoDocumento->main_right}}@endisset" required="required" class="form-control col-md-6 col-xs-12">
                  </div>
                </div>                                         
            {{-- /FORMATO DE CUERPO DOCUMENTO --}}

            
            
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-1">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                <button type="button" id="btn_resetearValores" class="btn btn-warning"><i class="fa fa-history"></i> Valores por defecto</button>
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