
    $(document).ready(function(){

        // CARGAR CARACTERITICAS DEL EDITOR DE TEXTO TINYMCE (EDITOR DE TEXTO)
        tinymce.init({
            selector: 'textarea#full-featured-non-premium',
            language: 'es',
            plugins: 'preview fullpage paste importcss searchreplace autolink autosave directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
            imagetools_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            toolbar_sticky: true,
            autosave_interval: "30s",
            autosave_prefix: "{path}{query}-{id}-",
            autosave_restore_when_empty: false,
            autosave_retention: "2m",
            image_advtab: true,
            content_css: [
                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
                '/EditorTextoTinymce/css/codepen.min.css'
            ],
            link_list: [
                { title: 'My page 1', value: 'http://www.tinymce.com' },
                { title: 'My page 2', value: 'http://www.moxiecode.com' }
            ],
            image_list: [
                { title: 'My page 1', value: 'http://www.tinymce.com' },
                { title: 'My page 2', value: 'http://www.moxiecode.com' }
            ],
            image_class_list: [
                { title: 'None', value: '' },
                { title: 'Some class', value: 'class-name' }
            ],
            importcss_append: true,
            height: 400,
            file_picker_callback: function (callback, value, meta) {
                /* Provide file and text for the link dialog */
                if (meta.filetype === 'file') {
                callback('https://www.google.com/logos/google.jpg', { text: 'My text' });
                }

                /* Provide image and alt text for the image dialog */
                if (meta.filetype === 'image') {
                callback('https://www.google.com/logos/google.jpg', { alt: 'My alt text' });
                }

                /* Provide alternative source and posted for the media dialog */
                if (meta.filetype === 'media') {
                callback('movie.mp4', { source2: 'alt.ogg', poster: 'https://www.google.com/logos/google.jpg' });
                }
            },
            templates: [
                { title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
                { title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
                { title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
            ],
            template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
            template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
            height: 600,
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_noneditable_class: "mceNonEditable",
            toolbar_drawer: 'sliding',
            contextmenu: "link image imagetools table",
        });

        // /CARGAR CARACTERITICAS DEL EDITOR DE TEXTO TINYMCE (EDITOR DE TEXTO)

    });


    function limpiarTodo(){

        // reiniciamos el contenido del editor de documento
        tinyMCE.activeEditor.setContent(`<p style="text-align: center; font-size: 15px;">
            <p style="font-size: 15px; text-align: left;">De mi consideración: <br><br><br><br><br><br>
            <span  style="margin-top: 100px;">Con sentimientos de distinguida consideración.</span> </p>`);

        // ------- limpiamos el contenido de la ventana principal ------------
            //limpiamos todos los PARA agregados en la venana principal
            $("#areaGeneralDepartamentoAgregados").addClass("hidden");
            $("#div_conteDepEnviar").html("");
            //limpiamos todos las COPIAS agregadas en la venana principal
            $("#areaGeneralDepartamentoCopias").addClass("hidden");
            $("#div_conteCopiaEnviar").html("");
            //limpiamos todos los INTERESADOS agregados en la venana principal
            $("#areaGeneralInteresadosAgregados").addClass("hidden");
            $("#div_conteInteresados").html("");
    
        // ---- limpiamos inputs y habilitar de deshabilitar conponentes ------
            //en la ventana principal
            $("#content_gestion_tramite").addClass("disabled_content");
            $("#content_btnTramie").addClass("disabled_content");
            $("#content_select_tipo_tramite").removeClass("disabled_content");
            $("#content_select_procedencia").removeClass("disabled_content");
            $("#content_select_prioridad").removeClass("disabled_content");
            $("#alerta_info_cabe_pie").hide(); // ocultamos mensaje de la cabecera del documento
            $(".option_tramite").prop("selected",false); // deseleccionamos el tipo de tramite seleccionado
            $(".option_0").prop("selected",true);
            $("#gf_select_tipo_tramite").trigger("chosen:updated"); // actualizamos el combo
            // en la modal
            $("#input_buscar").val("");

        // ---- limpiamos el los campos ------------
            $("#gt_asunto").val("");
            $("#gt_observaciones").val("");

        // ---- limpiamos los documentos adjuntos ----------
            $("#lista_documentos_adjuntos").html("");
            $("#cont_lista_documentos_adjuntos").hide();
        
        // ---- ocultamos los botones principales ----------
            $("#btn_subir_tramite").hide(200);

        // ---- limpiamos la información de los tipo de documentos a adjuntar ----
            $("#num_documentos_req").hide();
            $("#num_documentos_req").html("");
            $("#documentos_requeridos").html("");
            $("#content_documentos_requeridos").hide();
    }

    // funcion para borrar todos los cambios que se han realizado de nuevo trámite
    $("#btn_cancelar_tramite").click(function(){
        swal({
            title: "",
            text: "¿Esta seguro que desea descartar los últimos cambios realizados?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si, descartar todo!",
            cancelButtonText: "No, cancela!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if (isConfirm) { // si dice que quiere cancelar todo

                limpiarTodo();
               
            }
            sweetAlert.close();   // ocultamos la ventana de pregunta
        }); 
    });



// ================= FUNCIONES PARA CREAR DOCUMENTOS TEMPORALES ===============================

    //function que se ejecuta para obtener el contenido del editor de texto 
    function guardarDocumentoTermporal(){
    
        var contenido= tinymce.activeEditor.getContent(); // obtenerl el contenido creado por el editor de texto
        var idtipo_documento_encrypt = $("#cmb_tipo_documento").val(); // obtenemos el id del tipo de documento seleciconado en el combo
        var nombreTipoDocumento = $("#cmb_tipo_documento option:selected").html(); // obtenemos el html del tipo de documento seleccionado en el combo
        var descripcion_documento = "DOCUMENTO PRINCIPAL"; // obtenemos la descripcion que el usuario le da al documento

        // var descripcion_documento = $("#descripcion_documento").val(); // obtenemos la descripcion que el usuario le da al documento
        
        if(idtipo_documento_encrypt==null){
            alertNotificar("Ya no hay documentos por crear","default");
            return;
        }
       
        // validamos que se ingrese bien la información
        if(nombreTipoDocumento=="" || descripcion_documento==""){
            alertNotificar("Ingrese todos los datos del documento","default"); 
            return;
        }
          

        // agregamos la informacion del doc creado a la lista
        var identificador_doc = idtipo_documento_encrypt.substr(0,20); // generar un id al documento
        $("#lista_documentos_creados").html(`          
            <div class="contenido_guardado contenido_${identificador_doc}" style="display:none;">
                <input type="hidden" name="input_contenido_documento[]" value='${contenido}'>
            </div>
            <input type="text" class="descripcion_documento desc_doc_${identificador_doc}" name="input_descripcion_documento[]" value="${descripcion_documento}">
            <input type="text" class="idtipo_documento" name="input_id_tipo_documento[]" value="${idtipo_documento_encrypt}">  
        `);
                      
    }



// ================= FUNCIONES PARA LA BUSCAR Y AGREGAR UN DESTINATARIO =======================



    //funcion para validar si el tipoTramite tiene un flujo definido
    $("#gf_select_tipo_tramite").change(function(e){
        var idTipoTramite = $("#gf_select_tipo_tramite").val();
    
        vistacargando('M','Espere...'); // mostramos la ventana de espera
        $.get(`/tramite/verificarFlujoTipoTramite/${idTipoTramite}`, function (resultado){
            console.clear();
            console.log(resultado);

            vistacargando(); // ocultamos la ventana de espera

            if(resultado.error){
                // error se modifica el codigo html
                vistacargando(); // ocultamos la ventana de espera
                alertNotificar("La petición no se pudo realizar", "error");
                return;
            }else if(!resultado.flujo){
                
                alertNotificar("El tipo de trámite no tiene un flujo definido", "default"); return; //borrar solo este codigo
   
            }else{
                // si tiene un flujo bien definido (finalizado)
                
                // cargamos la informacion del departamento al que se va a enviar el tramite
                $.each(resultado.primerNodoFlujoTramite.flujo_hijo,function(index, flujo_destino){

                    if(flujo_destino.tipo_envio=="P"){// si es un destino para
                        $("#areaGeneralDepartamentoAgregados").removeClass("hidden");  //mostramos el contenedor de PARA
                        $("#div_conteDepEnviar").append(`
                            <div class=" depEnviar_content">                                                                                                                        
                                <h2 class="title">
                                    <i class="fa fa-cube iconoTittle"></i>
                                    <p>${flujo_destino.departamento_jefe.us001_tipofp[0].us001.name}
                                        <span class="labelInfoUser">/</span>
                                        <span class="labelInfoUser"><i class="fa fa-bookmark"></i> ${flujo_destino.departamento_jefe.nombre}</span>
                                    </p>                                            
                                </h2>       
                                <input type="hidden" name="input_depaEnviarPara[]" value="${flujo_destino.departamento_jefe.iddepartamento_encrypt}">
                            </div>                 
                        `);

                    }else{// si es un destino copia
                        $("#areaGeneralDepartamentoCopias").removeClass("hidden"); //mostramos el contenedor de COPIA
                        $("#div_conteCopiaEnviar").append(`
                            <div class=" depEnviar_content">                                                                                                                        
                                <h2 class="title">
                                    <i class="fa fa-cube iconoTittle"></i>
                                    <p>${flujo_destino.departamento_jefe.us001_tipofp[0].us001.name}
                                        <span class="labelInfoUser">/</span>
                                        <span class="labelInfoUser"><i class="fa fa-bookmark"></i> ${flujo_destino.departamento_jefe.nombre}</span>
                                    </p>                                            
                                </h2>       
                                <input type="hidden" name="input_depaEnviarCopia[]" value="${flujo_destino.departamento_jefe.iddepartamento_encrypt}">
                            </div>                 
                        `);
                    }

                });



                // quitamos las opciones de agregar destino en la modal de buscarParaInteresados
                    $(".option_nointeresado").hide(); // ocultamos las opciones que no sean interesado
                    $(".option_nointeresado").prop("selected",false);
                    $("#option_interesado").prop("selected",true); // seleccionamos por defecto la opcion de interesado

            }

            $("#content_gestion_tramite").removeClass("disabled_content"); // habilitamos todas las opciones de iniciar un tramite
            $("#content_btnTramie").removeClass("disabled_content"); // habilitamos los botones
            $("#tramite_alerta_general").hide(200); // ocultamos las alerta (en caso que exista o no)
            $("#btn_subir_tramite").hide(); // ocultamos el boton de finalizar el trámite
            $("#content_select_tipo_tramite").addClass("disabled_content"); // desabilitamos el combo de selección de tipo de tramite
            $("#content_select_procedencia").addClass("disabled_content"); // desabilitamos el combo de selección de procedencia del tramite
            $("#content_select_prioridad").addClass("disabled_content"); // desabilitamos el combo de selección de prioridad

            // mostramos por unos segundos la informacion de la cabecera del documento
            if($("#alerta_info_cabe_pie").hasClass("hidden")){
                $("#alerta_info_cabe_pie").hide();
                $("#alerta_info_cabe_pie").removeClass("hidden");
            }
            $("#alerta_info_cabe_pie").show(400);
            setTimeout(function(){ 
                $("#alerta_info_cabe_pie").hide(400);
            },10000);

            // CARGAMOS EL COMBO DE LOS TIPOS DE DOCUMENTOS QUE DEBE O PUEDE GENERAR

            $(".cmb_tipo_documento").html(""); // limpiamos los option asignados
            $.each(resultado.listaTipoDocumentos, function(index, tipo_documento){
                $(".cmb_tipo_documento").append(`
                    <option value="${tipo_documento.idtipo_documento_encrypt}">${tipo_documento.descripcion}</option>
                `);

                if(resultado.flujo){ // solo si tiene flujo definido
                    //cargamos los tipo de documentos por adjuntar
                    $("#num_documentos_req").html(resultado.listaTipoDocumentos.length);
                    $("#num_documentos_req").show();

                    $("#content_documentos_requeridos").show();
                    $("#documentos_requeridos").append(`<code id="tipo_requerido_${tipo_documento.idtipo_documento_encrypt.substr(0, 40)}" class="doc_requeridos">${tipo_documento.descripcion}</code>`);
                }

            });
            $(".cmb_tipo_documento").trigger("chosen:updated"); // actualizamos el combo

        }).fail(function(){
            vistacargando(); // ocultamos la ventana de espera
        });
        
    });


// ================= /FUNCIONES PARA LA BUSCAR Y AGREGAR UN DESTINATARIO =======================



// funcion para guardar un borrador del tramite que se está iniciando

    $("#frm_registrarTramite").submit(function(e){
        // (IMPORTANTE) quitamos  los documentos adjuntos que se cancelaron
        $("#lista_documentos_adjuntos").find(".hidden_documento").remove();
        guardarDocumentoTermporal();
        
        e.preventDefault();
       
        var formulario = this; // obtenemos el formulacion

        swal({
            title: "",
            text: "¿Está seguro que desea registrar el támite?",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Si, guardalo!",
            cancelButtonText: "No, cancela!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if (isConfirm) { // si dice que quiere eliminar

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                var FrmData = new FormData(formulario);
                vistacargando('M','Registrando...'); // mostramos la ventana de espera
                $.ajax({
                    url: '/tramite/gestion',
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
                            mostrarAlertaFija("tramite_alerta_general", "CÓDIGO DEL TRÁMITE: "+ retorno.resultado.codTramite, "success");
                            $('html,body').animate({scrollTop:$('.main_container').offset().top},400);
                            limpiarTodo();
                            // mostramos el boton de finalizar trámite
                            $("#btn_subir_tramite").show(200);
                            $("#btn_subir_tramite").attr("onclick", `subirDetalleTramite('${retorno.resultado.iddetalle_tramite_encrypt}')`);
                        }
                        vistacargando(); // ocultamos la ventana de espera
                    },
                    error: function(error){
                        // alertNotificar(","error");
                        vistacargando(); // ocultamos la ventana de espera
                    }
                }); 

            }

            sweetAlert.close();   // ocultamos la ventana de pregunta
        }); 


    });

    // funcion para enviar el trámite a la bandeja del jefe
    function subirDetalleTramite(iddetalle_tramite_encrypt){

        swal({
            title: "",
            text: "¿Está seguro que desea subir el támite?",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Si, subirlo!",
            cancelButtonText: "No, cancela!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if (isConfirm) { // si dice que quiere eliminar
                
                vistacargando("M", "Subiendo...");
                
                $.get("/detalleTramite/subirDetalleTramite/"+iddetalle_tramite_encrypt, function(retorno){
                    
                    alertNotificar(retorno.resultado.mensaje, retorno.resultado.status);
                    if(!retorno.error){ // si no hay error
                        limpiarTodo();
                        $("#tramite_alerta_general").hide(200);
                    }
                    vistacargando();

                }).fail(function(){
                    vistacargando();
                });
            }

            sweetAlert.close();   // ocultamos la ventana de pregunta
        }); 
    }

// ======================= FUNCIONES PARA ADJUNTAR DOCUMENTO ===================================


    var contador = 1; // inicializamos un contador para concatenar con los id a generar

    function adjuntarNuevoDocumento(){

        var id_inputfile = "input_file_"+contador; // id de input file para seleccionar un archivo
        var id_documento_adjunto = "documento_adjunto_"+contador; // id del contenedos visual del archivo seleccionado
        contador++; // incrementamos el contados para que los id no se repitan

        
        // agregamos oculta la estructura del documento que en teoria se va a seleccionar
        $("#lista_documentos_adjuntos").append(`
            <div id="${id_documento_adjunto}" class="alert hidden_documento file_delete f_documento_adjunto fade in docActivo" style="margin-bottom: 5px;">
                <button type="button" class="btn btn-danger btn-sm btn_doc_creado" onclick="quitarDocumentoAdjunto(this)"><i class="fa fa-trash"></i></button>
                <button type="button" onclick="visualizarDocumentoAdjunto(this)" class="btn btn-primary btn-sm btn_doc_creado btn_visualizar"><i class="fa fa-eye"></i></button>
                <strong><i class="icono_left fa fa-file-pdf-o"></i></strong> 
                <span class="nameFile"></span>

                <hr style="margin: 10px 0px 0px 0px; border-top: 1px solid #48aecd;">
                <div class="infoDoc" style="margin-top: 10px;"></div>
                <div class="infoDocEnviar">
                    <input type="file" id="${id_inputfile}" accept="application/pdf" class="nombreDocumento hidden" name="file_documento_adjunto[]" value="0">
                </div>
            </div>  
        `);

        $(`#${id_inputfile}`).click(); // le damos click al input file agregado para que el usuario seleccione un archivo
        
        // mostramos la estructura del documento solo si se selecciona uno
        // si no se selecciona la estructura del documento queda oculto en la lista
        // Nota: un input vacio no da problemas ya que no llega al controlador porque por el request solo se van los input file que tiene un archivo seleccionado
        $(`#${id_inputfile}`).change(function(e){


            // obtenemos y mostramos el nombre del documento seleccionado
            var nombreDocSelc = $(`#${id_inputfile}`)[0].files[0].name;

            // validamos la extencion del documeto
                var tipoDocSalec = nombreDocSelc.split('.').pop(); // obtenemos la extención del documento
                if(arrFormatos[`${tipoDocSalec}`] != true){
                    alertNotificar(`El formato del documento .${tipoDocSalec} no esta permitido`, "error");
                    // eliminamos el documento oculto
                    $("#lista_documentos_adjuntos").find(".hidden_documento").remove();
                    return;
                
                }
            
            //verificamos si no es pdf
            if(tipoDocSalec!="pdf" && tipoDocSalec!="PDF"){
                $("#"+id_documento_adjunto).find('.btn_visualizar').remove();
            }

            // obtenemos el tamaño del documento 
                var tamArchivo = $(`#${id_inputfile}`)[0].files[0].size; // obtenemos el tamaño del archivo
                var tamArchivo = ((tamArchivo/1024)/1024);
                if(tamArchivo>tamMaxDoc){
                    alertNotificar(`Solo se permite adjuntar documentos con un tamaño máximo de ${tamMaxDoc}MB`, "error");
                    // eliminamos el documento oculto
                    $("#lista_documentos_adjuntos").find(".hidden_documento").remove();
                    return;
                }

            // visualizamos el documento
                if(tipoDocSalec=="pdf" || tipoDocSalec=="PDF"){
                    $('#VistaPreviaMesj').attr('hidden',true);
                    $('#VistaPreviaDoc').attr('hidden',false);
                    $('#VistaPreviaDoc').html(`<iframe src="${URL.createObjectURL(e.target.files[0])}" style="width:100%; height: 400px;" frameborder="0"></iframe>`);
                    $('#sinVistaPrevia').hide();
                }else{ // no es un PDF
                    $('#VistaPreviaDoc').html("");
                    $('#VistaPreviaDoc').prop("hidden", true);
                    $('#sinVistaPrevia').show();
                }

            // limpiamos los input de la modal
                $("#modal_codigo_docAdj").val("");
                $("#modal_descripcion_docAdj").val("");

            // vista previa del documento
            $("#modalVistaPreviaDocumento").modal("show");

            $("#modal_id_documento_adjunto").val(id_documento_adjunto);
            $("#modal_nameFile").val(nombreDocSelc);

            $("#modal_nombreDocumentoSeleccionado").html(nombreDocSelc); // mostramos el nombre del documento en la modal

        });

    }

    // funcion que visualiza un documento seleccionado
    // boton de la modal
    function agregarDocumento(){

        var id_documento_adjunto = $("#modal_id_documento_adjunto").val();
        var nameFile = $("#modal_nameFile").val();

        var codigo_docAdj = $("#modal_codigo_docAdj").val();
        var descripcion_docAdj = $("#modal_descripcion_docAdj").val();
        var idtipo_documento_encrypt = $("#cmb_tipo_documento_docAdj").val();

        var descripcion_tipo_documento_adj = $(`#cmb_tipo_documento_docAdj option[value="${idtipo_documento_encrypt}"]`).html();

        // ---------------------------------------------------------------------------------
        if(codigo_docAdj=="" || codigo_docAdj==null || descripcion_docAdj=="" || descripcion_docAdj==null){ // si no se ingresa el nombre del documento
            alertNotificar('Ingrese toda la información antes de agregar el documento','error');
            return;
        }

        if(idtipo_documento_encrypt==null && $("#idtipo_documento_editar").val()==0){
            alertNotificar("Ya no hay documentos por crear","default"); // verificamos que aya documentos
            cancelarDocumento();
            return;
        }

        // quitamos el tipo de documento de la lista
            $(`.cmb_tipo_documento option[value="${idtipo_documento_encrypt}"]`).remove(); // quitamos la opton del tipo de documento creado
            $(".cmb_tipo_documento").trigger("chosen:updated"); // actualizamos el combo
        
        // quitamos de la lista de documentos requeridos
            $("#tipo_requerido_"+idtipo_documento_encrypt.substr(0, 40)).hide();
            var num_tipo_requerido = $("#documentos_requeridos").children('code:visible').length;
            $("#num_documentos_req").html(num_tipo_requerido);
            if(num_tipo_requerido == 0){
                $("#content_documentos_requeridos").hide();
                $("#num_documentos_req").hide();
            }

        // para mostrar en la vista
            var infoDoc = (`
                <b class="separardor">CÓDIGO:</b> <span class="separador_i">${codigo_docAdj}</span>
                <b class="separardor">DESCRIPCIÓN: </b> <span class="separador_i">${descripcion_docAdj}</span>
                <b class="separardor">TIPO DOCUMENTO: </b> <span class="tipo_documento_adj">${descripcion_tipo_documento_adj}</span>
            `);

        // para enviar por el request
            var infoDocEnviar = (`
                <input type="hidden" name="input_id_tipo_documento_adjunto[]" value="${idtipo_documento_encrypt}" class="idtipo_documento_adj">
                <input type="hidden" name="input_codigo_documento_adjunto[]" value="${codigo_docAdj}">
                <input type="hidden" name="input_descripcion_documento_adjunto[]" value="${descripcion_docAdj}">
            `);

        // CARGAMOS TODA LA INFORMACIÓN DEL DOCUMENTO
            $("#"+id_documento_adjunto).find('.nameFile').html(`<b>${nameFile}</b>`)
            $("#"+id_documento_adjunto).find('.infoDoc').html(infoDoc);
            $("#"+id_documento_adjunto).find('.infoDocEnviar').append(infoDocEnviar); // con append para no borrar el input file

        // mostramos la estructura del documento seleccionado con toda su información
            $(`#${id_documento_adjunto}`).removeClass("hidden_documento");
            $(`#${id_documento_adjunto}`).removeClass("file_delete"); // quitamos la clase para que no se borre al realizar el submit
            $(`#${id_documento_adjunto}`).addClass("active_documento"); // clase para compar si hay documentos en la lista (si no hay ocultamos la lista)
        
        // mostramos la lista de documentos adjuntos
            $("#cont_lista_documentos_adjuntos").show(200);
            $("#modal_id_documento_adjunto").val(""); // limpiamos en input de ingreso de nombre del documento 
            $("#modal_nameFile").val("");
        
        // ocultamos la modal
            $("#modalVistaPreviaDocumento").modal("hide");

    }

    // funcion para no agregar el documento seleccionado
    function cancelarDocumento(){
        var id_documento_adjunto = $("#modal_id_documento_adjunto").val();
        $(`#${id_documento_adjunto}`).remove();
        $("#modalVistaPreviaDocumento").modal("hide");
    }

    // función que desplega una modal para visualizar el documento
    function visualizarDocumentoAdjunto(btn){
        var input = $(btn).parent().find(".nombreDocumento"); // obtenemos el input file que contiene el archivo seleccionado
        $('#content_visualizarDocumento').html(`<iframe src="${URL.createObjectURL($(input)[0].files[0])}" style="width:100%; height: 400px;" frameborder="0"></iframe>`);
        $(".modal_visualizarDocumento").modal();
    }

    // fucion para eliminar la estrucutura de un documento (ED) adjuntado en la lista
    function quitarDocumentoAdjunto(btn){

        swal({
            title: "",
            text: "¿Está seguro que desea quitar el documento adjunto?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si, quitarlo!",
            cancelButtonText: "No, cancela!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if (isConfirm){ // si dice que quiere eliminar
            
                var borrar = $(btn).parent(); // obtenemos el div que vamos a quitar
                $(borrar).removeClass("active_documento"); // quitamos la clase que confirma que el documento esta visible
                $(borrar).hide(200);// ocultamos la ED que se va a eliminar

                // agregamos el tipo de tramite a los combos ---------------------------------------

                    var idtipo_documento = $(borrar).find(".idtipo_documento_adj").val();
                    var descripcion_tipo_documento = $(borrar).find(".tipo_documento_adj").html();

                    // agregamos al combo de tipo de documento el tipo del documento eliminado
                    $(".cmb_tipo_documento").append(`
                        <option value="${idtipo_documento}">${descripcion_tipo_documento}</option>
                    `);
                    $(".cmb_tipo_documento").trigger("chosen:updated"); // actualizamos el combo

                    //mostramos en la lista de documentos requeridos (solo si existen)
                        var num_tipo_requerido = $("#documentos_requeridos").children('code').length;
                        if(num_tipo_requerido>0){ //flujo definido
                            $("#tipo_requerido_"+idtipo_documento.substr(0, 40)).show();
                            $("#content_documentos_requeridos").show();
                            num_tipo_requerido = $("#documentos_requeridos").children('code:visible').length;
                            $("#num_documentos_req").html(num_tipo_requerido);
                            $("#num_documentos_req").show();
                        }

                // ------------------------------------------------------------------------------------

                // esperamos que termine la animacion para borrar dicha ED
                setTimeout(function(){ 
                    $(borrar).remove(); // borramos el html de la ED
                    // buscamos cuantas ED estan en la lista
                    var numDocAdj = $("#lista_documentos_adjuntos").find(".active_documento").length;
        
                    if(numDocAdj<=0){ // si no hay ninguna ED ejecutamos
                        $("#cont_lista_documentos_adjuntos").hide(200);// ocultar el contenedor de la lista de documentos adjuntos
                    }
                }, 250);


            }
            sweetAlert.close();   // ocultamos la ventana de pregunta
        });

    }

    // evento para quitar los documentos adjuntos que se cancelaron
    $("#btn_guardar_borrador").click(function(){
        //quitamos  los documentos adjuntos que se cancelaron
        $("#lista_documentos_adjuntos").find(".hidden_documento").remove();
    });

// ======================= /FUNCIONES PARA ADJUNTAR DOCUMENTO ===================================