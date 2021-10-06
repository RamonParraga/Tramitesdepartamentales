

{{-- MODAL PARA VISUALIZAR UN DOCUMENTO --}}
   
   <div class="modal fade modal_visualizarDocumento" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-file-pdf-o"></i> Vista previa del documento</h4>
                </div>
                <div class="modal-body">

                <div id="content_visualizarDocumento"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>

            </div>
        </div>
    </div>