<!-- modals -->
    <!-- bootstrap-daterangepicker -->
    <link href="../vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <!-- bootstrap-datetimepicker -->
    <link href="../vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
   
    <!-- Large modal -->
        <div class="modal fade modal_gestionPeriodo" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Gestion de Periodos</h4>
                </div>
                <div class="modal-body">
                    <h4>Crear un nuevo periodo</h4>
                    <div class="x_content">
                        <br/>
                        <form id="frm_gestionPeriodos" enctype="multipart/form-data" class="form-horizontal form-label-left">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha de Inicio: </label>
                                <div class="col-md-9 col-sm-9 col-xs-12 xdisplay_inputx form-group has-feedback" >
                                    <input type="text" name="fecha_inicio" class="form-control has-feedback-left" id="single_cal3" placeholder="First Name">
                                    <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                    <span id="inputSuccess2Status4" class="sr-only">(success)</span>
                                </div>
                            </div>
                            <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha de Fin: </label>
                                    <div class="col-md-9 col-sm-9 col-xs-12 xdisplay_inputx form-group has-feedback" >
                                        <input type="text" name="fecha_fin" class="form-control has-feedback-left" id="single_cal4" placeholder="First Name">
                                        <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                        <span id="inputSuccess2Status4" class="sr-only">(success)</span>
                                    </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Estados: </label>
                                <div class="col-md-9 col-sm-9 col-xs-12 form-group">
                                    <select name="estado_periodo" id="estado_periodo" class="form-control">
                                        <option value="">Seleccione un estado</option>
                                        <option value="A">Activo</option>
                                        <option value="D">Desactivo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <button id="btn_cancelarPeriodo" class="btn btn-primary hidden" type="button" onclick="cancelarPeriodo()">Cancelar</button>
                                    <button id="btn_guardarPeriodo" onclick="guardarPeriodo()" type="button" class="btn btn-success"><i class="fa fa-cloud-upload"></i> Registrar</button>
                                </div>
                            </div>
                                
                        </form>
                    </div>
                    <div class="row"></div>
                    <div class="div_scroll" style="">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Fecha Inicio</th>
                                <th scope="col">Fecha Fin</th>
                                <th scope="col">Estado</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody id="tbody_periodos">
                                {{-- el contenido de esta tabla se carga con jquery desde la funcion gestionar_periodos()--}}
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>

                </div>
            </div>
        </div>
    {{-- /Large modal --}}



    <!-- bootstrap-daterangepicker -->
    <script src="../vendors/moment/min/moment.min.js"></script>
    <script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap-datetimepicker -->    
    <script src="../vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>