    <!-- Large modal -->
    <div class="modal fade modal_NuevoAnio" id="modal_NuevoAnio" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
                <center>
                <h4 class="modal-title" id="myModalLabel">Gestion de Año</h4>
                </center>
            </div>
            <div class="modal-body">
                <form role="form" id="frm_NuevoAnio" action="{{url('estructuradocumento/AnioNuevo')}}" method="POST" class="form-horizontal form-label-left" accept-charset="UTF-8" enctype="plain">
                    {{ csrf_field() }}
                    <input id="method_estructuradocumento" type="hidden" name="_method" value="POST">

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_anionuevo">Año:<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" name="anionuevo" id="id_anionuevo" placeholder="Agregar año"  required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <button type="submit" class="btn btn-success" type="button" class="btn btn-success"><i class="fa fa-cloud-upload"></i> Resetear</button>
                        </div>
                    </div>
                </form>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
            </div>

            </div>
        </div>
    </div>
