

    function aprobarTramite(iddetalle_tramite_encrypt){

        $.get("/revisionTramite/verificarDocumentoFirmado/"+iddetalle_tramite_encrypt, function(retorno){

            $("#informacion_certificado").html("");

            if(!retorno.error){ // si no hay error
                if(retorno.firma=="listo"){
                    finalizarAprobacionTramite();
                }else{
                    $('#modal_subir_documento_firmado').modal('show'); 
                }
            }else{
                alertNotificar(retorno.mensaje, retorno.status);
            }
        }).fail(function(){
            alertNotificar("No se pudo completar la acción", "error");
        });
    }


    // funcion para aprobar por completo el tramite
    function finalizarAprobacionTramite(){
        swal({
            title: "",
            text: "¿Está seguro que desea aprobar el támite?",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Si, aprobarlo!",
            cancelButtonText: "No, cancela!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if(isConfirm){ // si dice que quiere eliminar
                enviar_tramite_destino();
            }
            sweetAlert.close();   // ocultamos la ventana de pregunta
        }); 
    }


    // funcion para enviar el trámite con documento firmado a los departamentos destino
    function enviar_tramite_destino(){

        vistacargando("M", "Espere...");
        iddetalle_tramite_encrypt = $("#id_detalle_tramite_encrypt").val();
                
        $.get("/revisionTramite/aprobarDetalleTramite/"+iddetalle_tramite_encrypt, function(retorno){
            
            alertNotificar(retorno.resultado.mensaje, retorno.resultado.status);
            if(!retorno.error){ // si no hay error
                window.location.href="/gestionBandeja/aprobarEnvio";
            }else{
                vistacargando();
            }

        }).fail(function(){
            vistacargando();
        });
    }


    // METODOS PARA SUBIR UN DOCUMENTO FIRMADO -----------------------------------------------

        // evento del input file de subir documento firmado
        $(".input_subirDocumento").change(function(e){
            
            var formulario = $(this).parents(".form_subirDocumento");

            // obtenemos y mostramos el nombre del documento seleccionado
                var nombreDocSelc = $(this)[0].files[0].name;

            // validamos la extencion del documeto
                var tipoDocSalec = nombreDocSelc.split('.').pop(); // obtenemos la extención del documento
                if(arrFormatos[`${tipoDocSalec}`] != true){
                    alertNotificar(`El formato del documento .${tipoDocSalec} no está permitido`, "error");
                    return;
                }

            // obtenemos el tamaño del documento 
                var tamArchivo = $(this)[0].files[0].size; // obtenemos el tamaño del archivo
                var tamArchivo = ((tamArchivo/1024)/1024);
                if(tamArchivo>tamMaxDoc){
                    alertNotificar(`Solo se permite adjuntar documentos con un tamaño máximo de ${tamMaxDoc}MB`, "error");
                    return;
                }
            
            // enviamos el documento
                $(formulario).submit();
        });

        $(".form_subirDocumento").submit(function(e){
            e.preventDefault();

            var ruta = $(this).attr("action");
            var FrmData = new FormData(this);
    

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            vistacargando('M','Subiendo...'); // mostramos la ventana de espera

            $.ajax({
                url: ruta,
                method: 'POST',
                data: FrmData,
                dataType: 'json',
                contentType:false,
                cache:false,
                processData:false,
                complete: function(requestData){
                    retorno = requestData.responseJSON;
                    // si es completado
                    alertNotificar(retorno.resultado.mensaje, retorno.resultado.status);

                    if(!retorno.error){
                        // ponemos el icono de firmado
                        $("#icono_estado_firma").html('<span class="fa fa-check-circle"></span>');
                        $("#icono_estado_firma").parent().removeClass('btn_rojo');
                        $("#icono_estado_firma").parent().addClass('btn_verde');
                        $("#btn_enviar_tramite").show(200);
                    }
                    vistacargando(); // ocultamos la ventana de espera
                },
                error: function(error){
                    // alertNotificar(","error");
                    vistacargando(); // ocultamos la ventana de espera
                }
            }); 
        });

    // -----------------------------------------------------------------------------------------


    // FUNCION PARA ENVIAR UN TRÁMITE A REVISIÓN

        $("#frm_enviaraRevision").submit(function(e){
            e.preventDefault();

            var ruta = $(this).attr("action");
            var FrmData = new FormData(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            vistacargando('M','Subiendo...'); // mostramos la ventana de espera

            $.ajax({
                url: ruta,
                method: 'POST',
                data: FrmData,
                dataType: 'json',
                contentType:false,
                cache:false,
                processData:false,
                complete: function(requestData){
                    retorno = requestData.responseJSON;
                    // si es completado
                    alertNotificar(retorno.resultado.mensaje, retorno.resultado.status);

                    if(!retorno.error){
                        // ponemos el icono de firmado
                        window.location.href="/gestionBandeja/aprobarEnvio";
                    }else{
                        vistacargando(); // ocultamos la ventana de espera
                    }
                    
                },
                error: function(error){
                    // alertNotificar(","error");
                    vistacargando(); // ocultamos la ventana de espera
                }
            }); 
        });

    // FUNCION PARA SELECCIONAR UN ARCHVO --------------

        $(".seleccionar_archivo").click(function(e){
            $(this).parent().siblings('input').val($(this).parent().prop('title'));
            this.value = null; // limpiamos el archivo
        });

        $(".seleccionar_archivo").change(function(e){

            if(this.files.length>0){ // si se selecciona un archivo
                archivo=(this.files[0].name);
                $(this).parent().siblings('input').val(archivo);
            }else{
                return;
            }

        });

