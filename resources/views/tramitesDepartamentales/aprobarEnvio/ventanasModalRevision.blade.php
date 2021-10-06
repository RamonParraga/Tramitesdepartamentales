
<style type="text/css">
    .vert_center{
        vertical-align: middle !important;
        text-align: center;
    }
    .btn_revision{
        padding: 5px 30px;
    }

    .col_subir_doc{
        display: none;
    }
    .titulo_moda{
        font-weight: 300;
        color: #555;
        margin-top: 0px;
        margin-bottom: 32px;
        text-align: center;
        margin-left: 20px;
        margin-right: 20px;
    }
    .btn_firma{
        border-radius: 20px;
    }
    .btn_firma img{
        width: 100%;
    }
    .icon_success{
        font-size: 25px;
        color: #26ae2d;
    }
    .icon_danger{
        font-size: 25px;
        color: #db3131;
    }

    .lable_estado{
        padding: 5px;
        font-size: 14px;
        display: block;
        text-transform: none;
        font-weight: 500;
    }

    .btn_regresar{
        margin-left: 15px; margin-left: 0px; font-size: 14px; font-weight: 700; color: #446684;
    }

    /* success */
        .icon_success{
            color: #5cb85c !important;
        }
        .tile-stats:hover .icon_success i{
            color: #5cb85c !important;
        }
    /* danger */
        .icon_danger{
            color: #d9534f !important;
        }
        .tile-stats:hover .icon_danger i{
            color: #d9534f !important;
        }
    /* warning */
        .icon_warning{
            color: #ff851d !important;
        }
        .tile-stats:hover .icon_warning i{
            color: #ff851d !important;
        }
    /* ------------ */

    .tile-stats .icon{   
        top: -5px !important;         
        right: 35px;
    }

    .tile-stats .icon i{
        font-size: 50px !important;
    }

</style>

<input type="hidden" id="id_detalle_tramite_encrypt" value="">

{{-- modal para registrar el detalle de revisión --}}

    <div id="modal_detalle_revision" data-backdrop="static" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Enviar trámite a revisión</h4>
                </div>

               <form id="frm_enviaraRevision" action="" method="POST"  enctype="multipart/form-data" class="form-horizontal form-label-left">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="POST">

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="" class="col-md-12 col-sm-12 col-xs-12">Descripción respecto a los cambios a realizar</label>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <textarea type="text" name="textarea_detalle_revision" id="textarea_detalle_revision" placeholder="Ingrese el motivo por el que se envia a revisión" rows="5" class="date-picker form-control col-md-7 col-xs-12 sinespecial" required="required" style="text-transform: uppercase;"></textarea>
                                <span class="sinespecialMsj"></span>  
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">  
                        <button type="button" class="btn btn-default btn-padding-lg" data-dismiss="modal"><i class="fa fa-thumbs-o-down"></i> Cancelar</button>  
                        <button type="submit" class="btn btn-primary btn-padding-lg"><i class="fa fa-envelope"></i> Enviar</button>                  
                    </div>

                </form>
            </div>
        </div>
    </div>


{{-- modal para subir el documento firmado --}}
    <div id="modal_subir_documento_firmado" data-backdrop="static" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Subir documento firmado</h4>
                </div>
                <div class="modal-body">

                    <h4 style="margin-bottom: 20px;"><center><i><b>Debe descargar el documento y subirlo firmado</b></i></center></h4>

                    <form id="form_subir_documento_firmado" action="" class="form-horizontal form-label-left form_subirDocumento" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="POST">


                        <div class="form-group">                            
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="">Documento:</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <div class="input-prepend input-group">
                                    <label  title="No seleccionado" class=" btn_azul control-label btn btn-primary btn-upload add-on input-group-addon" for="input_subirDocumento">                            
                                        <input  type="file" name="input_subirDocumento" id="input_subirDocumento" class="seleccionar_archivo input_subirDocumento sr-only" accept="application/pdf" >                                    
                                        <span class="fa fa-upload"></span>
                                    </label>
                                    <input class="form-control" type="text" value="No seleccionado" style="pointer-events: none;">
                                    <label title="No seleccionado" class="control-label btn_rojo input-group-addon" style="padding: 5px 12px; font-size: 20px;">
                                        <span id="icono_estado_firma"><span class="fa fa-times-circle"></span></span>
                                    </label>
                                </div> 
                            </div>
                        </div>

                        <div class="modal-footer"> 
                            <center>
                                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-mail-reply-all"></i> Salir</button>  
                                <button type="button" id="btn_enviar_tramite" onclick="enviar_tramite_destino()" class="btn btn-success" style="display: none;"><i class="fa fa-send"></i> Enviar trámite</button>                                   
                            </center>               
                        </div>                       
                    </form>  
                    

                </div>
            </div>
        </div>
    </div>


