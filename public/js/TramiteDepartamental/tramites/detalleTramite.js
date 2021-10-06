
    var disco = "disksServidorSFTPborradores"; // de momento es este

    // funcion para cargar los detalles de un tramite (recive el id del tramite encyptado)
    function mostrarDetalleTramite(iddetalle_tramite){

        //------- limpiamos la información --------------------
            $("#info_fecha_documento").html("-");
            $("#info_tipo_documento").html("-");
            $("#info_asunto").html("-");
            $("#info_codigo_documento").html("-");
            $("#info_de").html("-");
            $("#info_estado_documento").html("-");
            $("#info_para").html("-");
            $("#info_copia").html("-");
        //-------------------------------------------------------

        $("#tab_informacion_documento").click();
        $("#info_vista_previa_documento").html(""); //limpiamos la vistra previa del documento
        $("#tab_asociar_tramite").hide(); // ocultamos por defecto la pestaña de trámites asociados     

        vistacargando("M", "Espere...");
        
        $.get("/tramite/detalleTramite/"+iddetalle_tramite, function(retorno){
            
            vistacargando();

            if(retorno.detalle_tramite.idflujo==null){
                $("#tab_asociar_tramite").show(); // si no tiene un flujo definido mostramos la pestaña de trámites asociados 
            }

            cargarInformacionDocumento(retorno.detalle_tramite);

            //cargamos la ruta del boton descargar todos los documentos
            $("#btn_descargar_todos_documentos").attr("href", "/tramite/descargarDocumentos/"+retorno.tramite.idtramite_encrypt);
           
            if(!retorno.error){ // todo bien (success)

                var tramite = retorno.tramite;

                // ---- creamos los mensajes de los estados del tramite
                    var procedencia = "Interno";
                    if( tramite.procedencia == "EXT"){ procedencia = "Externo"; }

                    var resolucion = "TRÁMITE NO HA FINALIZADO AÚN";
                    if(tramite.finalizado==1){
                        resolucion = "TRÁMITE FINALIZADO";
                        // switch (tramite.estadoTramite) {
                        //     case "A":
                        //         resolucion = "TRÁMITE APROBADO";
                        //         break;
                        //     case "R":
                        //         resolucion = "TRÁMITE RECHAZADO";
                        //         break;
                        // }
                    }

                // ----- cargamos los detalles generales del tramite
                    $("#codigo_tramite").html(tramite.codTramite);
                    $("#dt_procedencia").html(procedencia);
                    $("#dt_codigoTramite").html(tramite.codTramite);
                    $("#dt_asunto").html(tramite.asunto);
                    $("#dt_tipo_tramite").html(tramite.tipo_tramite.descripcion);
                    $("#dt_origin").html(tramite.departamento_genera.nombre);
                    $("#dt_observacion").html(tramite.observacion);
                    $("#dt_resolucion").html(resolucion);
                    
                    $(".dt_info_final").hide(); // por defecto debe estar oculto
                    $("#dt_conclusion").html(""); //para agregar las concluciones
                    $("#dt_archivos_fisico").html("-");

                // ----- cargamos la ruta fisica de los archivos de trámite
                    if(tramite.gestion_archivo.length==0){
                        $("#dt_archivos_fisico").html("RUTA NO REGISTRADA");
                    }else{
                        tramite.gestion_archivo.forEach(archivo => {
                            $("#dt_archivos_fisico").html(`
                                
                                <b><i class="fa fa-archive"></i> BODEGA: </b> ${archivo.seccion.sector.bodega.nombre}<br>
                                <b><i class="fa fa-navicon"></i> SECTOR: </b> ${archivo.seccion.sector.descripcion}<br>
                                <b><i class="fa fa-outdent"></i> SECCIÓN: </b> ${archivo.seccion.descripcion}<br>
                                <b><i class="fa fa-suitcase"></i> CARPETA: </b> ${archivo.folder}
                                
                            `);
                        });
                    }
                
                // ----- cargamos los el recorrido de departamentos -------------------
                    $("#ht_codigoTramite").html(tramite.codTramite);
                    $("#timeline").html("");
                    $("#tbody_todos_documentos_tramite").html(""); // solo si hay documentos limpiamos

                // ------ cargamos el organigrama del historial de tramites -------------
                    cargarOrganigramaHistoriaTramites(iddetalle_tramite);
            

                //LIMPIAMOS LA MODAL DE ASOCIAR TRAMITE (lo hacemos desde aqui para evitar errores)

                    $(".tabla_asociados").find('tbody').html("");
                    var datatable = $(".tabla_asociados").DataTable({
                        dom: "<'row' <'form-inline'>> <rt> <'row'<'form-inline'  <'col-sm-6 col-md-6 col-lg-6'l> <'col-sm-6 col-md-6 col-lg-6'p>>>",
                        "destroy":true,
                        order: [[ 0, "desc" ]],
                        pageLength: 3,
                        "language": { url: '/json/datatables/spanish.json' }
                    });
                    datatable.clear();


            }else{
                alert("error");
            }

        }).fail(function(){
            vistacargando();
        });
    }


    //función para cargar el organigrama de todo el recorrido del trámite
    function cargarOrganigramaHistoriaTramites(iddetalle_tramite){

        $.get("/tramite/getAllDetalleTramiteAsociados/"+iddetalle_tramite, function(retorno){

            if(retorno.error){

                $("#flujo_proceso").html(`<center>
                    <h2 class="codDoc_asociado" style="margin-bottom: 20px;"> 
                        <i class="fa fa-meh-o" style= "font-size: 22px;"></i> NO SE PUDO OBTENER EL HISTORIAL DE TRÁMITES 
                    </h2>
                </center>`);

                $("#tbody_todos_documentos_tramite").html(`<tr> <td colspan="5"><center>No hay documentos</center></td></tr>`);

                return;
            }

            var listaDetallesTramite = retorno.listaDetalles;
        
            var dataOrganigrama = [];
            var contarDocumentos = 0; // para contar cuandos documentos se agregaron a la tabla

            $.each(listaDetallesTramite, function(dt, detalle_tramite){

                // creamos el mensaje del estado del detalle_tramite
                    var estado_detalle_tramite = "";
                    var title_color = "";
                    if(detalle_tramite.aprobado==0){
                        estado_detalle_tramite = "EN ELABORACIÓN"; 
                    }else{ // si ya lo aprobo el jefe
                        switch (detalle_tramite.estado) {
                            case "T":
                                estado_detalle_tramite = "EN PROCESO";
                                title_color = "background: #00ab8a;"; //verde
                                break;
                            case "A":
                                estado_detalle_tramite = "ATENDIDO";
                                title_color = "background: #00ab8a;"; //verde
                                break;
                            case "F":
                                estado_detalle_tramite = "FINALIZADO";
                                title_color = "background: #0045ab;"; //azul
                                //cargamos la conclusion
                                $(".dt_info_final").show();
                                $("#dt_conclusion").append(`<span style="font-weight: 800;">(${detalle_tramite.departamento_origen.nombre}) <i class="fa fa-arrow-right"></i> </span> ${detalle_tramite.observacion}<br>`);
                                break;
                            case "D":
                                estado_detalle_tramite = "DENEGADO";
                                title_color = "background: #d43f3a;"; //rojo
                                //cargamos la conclusion
                                $(".dt_info_final").show();
                                $("#dt_conclusion").append(`<span style="font-weight: 800;">(${detalle_tramite.departamento_origen.nombre}) <i class="fa fa-arrow-right"></i> </span> ${detalle_tramite.observacion}<br>`);
                                break;
                        }                                
                    }

                    var contenido = (`
                        <div class="content_nodo">
                            <div class="content_title_detalle" style="${title_color}"><b>${detalle_tramite.departamento_origen.nombre}</b></div>
                            <div class="content_info_detalle">
                    
                                <p class="info_detalle" style="text-align: left;">
                                    <b><i class="fa fa-calendar"></i> ${detalle_tramite.fecha}</b>
                                    <br>
                                    ${detalle_tramite.asunto}
                                    <br>
                                    <b>Estado: </b> ${estado_detalle_tramite} 
                                </p>
                    
                                <button type="button" onclick="detalle_tramite_documentos('${detalle_tramite.iddetalle_tramite_encrypt}')" class="btn btn-xs btn-outline-primary" style="padding: 2px 8px;"><i class="fa fa-file-pdf-o"></i> Documentos</button>
                                
                            </div>
                        </div>
                        
                    `);

                    if(dt == 0){ // primer nodo del flujo
                        dataOrganigrama.push(
                            [{'v':`${detalle_tramite.iddetalle_tramite}`, 'f':contenido}, '', 'The President'],
                        );
                    }else{
                        dataOrganigrama.push(
                            [{'v':`${detalle_tramite.iddetalle_tramite}`, 'f':contenido}, `${detalle_tramite.iddetalle_tramite_padre}`, 'VP'],
                        );
                    }

                    if(detalle_tramite.aprobado == 1){ // solo si el tramite se envió
                        $.each(detalle_tramite.destino, function(des, destino){
                            if(destino.detalle_tramite_atendido.length == 0){ // destino no atendiso (no tiene un td_detalle_tramite registrado)

                                var mensaje = "ENVIADO PARA ATENDER";
                                var title = ""
                                var title_color = "background: #d68814;";  
                                var estado = "NO ATENDIDO";                  
                                if(destino.tipo_envio == "C"){
                                    mensaje = "ENVIADO COMO COPIA";
                                    title = `<br><b style="> <i class="fa fa-angle-double-right"></i> Copia <i class="fa fa-copy"></i></b>`;
                                    estado  = "RECIBIDO";
                                    title_color = "background: #3296ef;#3296ef";
                                }

                                var contenido2 = (`
                                    <div class="content_nodo">
                                        <div class="content_title_detalle" style="${title_color}">${destino.departamento.nombre}</div>
                                        <div class="content_info_detalle">
                                
                                            <p class="info_detalle" style="text-align: left;">
                                                <b><i class="fa fa-calendar"></i> ${detalle_tramite.fecha}</b>
                                                <br>
                                                ${mensaje}  ${title}
                                                <br>
                                                <b>Estado: </b> ${estado} 
                                            </p>                                                                                                            
                                        </div>                                            
                                    </div>

                                `);

                                dataOrganigrama.push(
                                    [{'v':`destino_${destino.iddestino}`, 'f':contenido2}, `${detalle_tramite.iddetalle_tramite}`, 'VP'],
                                );
                                
                            }
                        });
                    }

                //CARGAMOS LOS DOCUMENTOS DEL TRÁMITE

                $.each(detalle_tramite.documento, function (d, documento) {
                    
                    contarDocumentos++;

                    //verificamos por si es un documento que no se puede previsualizar
                    var boton_documento =`<button class="btn btn-info btn-sm btn-block" onclick="vista_previa_documento('${disco}','${documento.rutaDocumento}.${documento.extension}','TD')">
                                            <i class="fa fa-eye"></i> Visualizar
                                        </button>`;

                    if(documento.extension != "pdf" && documento.extension!="PDF"){
                    boton_documento = `<a class="btn btn-info btn-sm btn-block" href="/buscarDocumento/${disco}/${documento.rutaDocumento}.${documento.extension}" target="_blank">
                                            <i class="fa fa-download"></i> Descargar
                                        </a>`;
                    }

                    //ponemos el color para diferenciar si es un documento principal o un adjunto
                    var colorFila = 'bg-warning';
                    var nivelDoc = '<span class="label lable_estado label-danger">Adjunto</span>';
                    var doc_icono = `<svg style="font-size: 20px;" class="bi bi-arrow-return-right" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.146 5.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L12.793 9l-2.647-2.646a.5.5 0 010-.708z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M3 2.5a.5.5 0 00-.5.5v4A2.5 2.5 0 005 9.5h8.5a.5.5 0 000-1H5A1.5 1.5 0 013.5 7V3a.5.5 0 00-.5-.5z" clip-rule="evenodd"/></svg>`;

                    if(documento.tipo_creacion=="E"){
                        colorFila = 'bg-success';
                        nivelDoc = '<span class="label lable_estado label-success">Principal</span>';
                        doc_icono = `<svg style="font-size: 18px;" class="bi bi-chevron-double-right" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 01.708 0l6 6a.5.5 0 010 .708l-6 6a.5.5 0 01-.708-.708L9.293 8 3.646 2.354a.5.5 0 010-.708z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 01.708 0l6 6a.5.5 0 010 .708l-6 6a.5.5 0 01-.708-.708L13.293 8 7.646 2.354a.5.5 0 010-.708z" clip-rule="evenodd"/></svg>`;
                    }
                    
                    $("#tbody_todos_documentos_tramite").append(`
                        <tr class="${colorFila}">
                            <td>${doc_icono}</td>
                            <td>${detalle_tramite.departamento_origen.nombre}</td>
                            <td>${documento.tipo_documento.descripcion}</td>
                            <td>${documento.fechaCarga}</td>
                            <td>${documento.codigoDocumento}</td>
                            <td>${documento.descripcion}</td>
                            <td>${nivelDoc}</td>
                            <td>${boton_documento}</td>
                        </tr>
                    `);  

                });

            });

            if(contarDocumentos == 0){ // si no se agregan documentos
                $("#tbody_todos_documentos_tramite").html(`<tr> <td colspan="5"><center>No hay documentos</center></td></tr>`);
            }
            
            //codigo para cargar el organigrama
            setTimeout(() => { // esperamos a que la libreria cargue
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Name');
                data.addColumn('string', 'Manager');
                data.addColumn('string', 'ToolTip');
                data.addRows(dataOrganigrama);

                // Create the chart.
                var chart = new google.visualization.OrgChart(document.getElementById('flujo_proceso'));
                // Dibuje el gráfico, estableciendo la opción allowHtml en true para la información sobre herramientas
                chart.draw(data, {'allowHtml':true});
            }, 200);


        }).fail(function(){

            $("#flujo_proceso").html(`<center>
                <h2 class="codDoc_asociado" style="margin-bottom: 20px;"> 
                    <i class="fa fa-meh-o" style= "font-size: 22px;"></i> NO SE PUDO OBTENER EL HISTORIAL DE TRÁMITES 
                </h2>
            </center>`);

            $("#tbody_todos_documentos_tramite").html(`<tr> <td colspan="5"><center>No hay documentos</center></td></tr>`);

        });

    }

    //función que se ejecuta al abrir la pestaña de ver historial
    $("#historial_tramite").click(function(){
        ajutarContenidoOrganigrama("#flujo_proceso");        
    });

    //funcion que se ejecuta el cambiar de tamaño la ventana
    $(window).resize(function(){
        if($("#flujo_proceso").is(":visible")){ // ajustamos solo cuando es visible el organigrama
            ajutarContenidoOrganigrama("#flujo_proceso");
        }
    });

    //funciona para ajustarl el alto de los nodos del diagrama
    function ajutarContenidoOrganigrama(iddiagrama){

        setTimeout(() => { //esperamos que se cargue el diagrama
            var nodos_diagrama = $(`${iddiagrama}`).find('.google-visualization-orgchart-node');
            $.each(nodos_diagrama, function(index, nodo){
                var content_nodo = $(nodo).children('.content_nodo');
                var height_nodo = $(nodo).height();
                $(content_nodo).css({'height': height_nodo+"px", 'padding': '0'});
            });
        }, 200);

    }


    //funcion para cargar la iformacion del documento principal
    function cargarInformacionDocumento(detalle_tramite){

        //verificamos si es un detalle finalizado o no
        if(detalle_tramite.estado=="F" || detalle_tramite.tramite.finalizado==1){
            $("#a_datos_generales").click();
            $("#tab_informacion_documento").hide();
        }

        detalle_tramite.documento.forEach(documento => {
            if(documento.tipo_creacion=="E"){ //documento principal

                //detectamos el estado del documento
                var estado_documento = "EN TRÁMITE";
                if(documento.estado == "B"){ estado_documento="EN BORRADOR"; }
                
                //obtenemos los destino para y copia
                var para = ""; var copia = "";
                detalle_tramite.destino.forEach(destino => {
                    if (destino.tipo_envio =="P") {
                        para = para+`<li> <i class="fa fa-user"></i> ${destino.departamento.jefe_departamento[0].us001.name}  /  <i class="fa fa-bookmark"></i> ${destino.departamento.nombre} </li>`;
                    }else{
                        copia = copia+`<li> <i class="fa fa-user"></i> ${destino.departamento.jefe_departamento[0].us001.name}  /  <i class="fa fa-bookmark"></i> ${destino.departamento.nombre} </li>`;
                    }
                });

                //cargamos toda la informacion
                $("#info_fecha_documento").html(documento.fechaCarga);
                $("#info_tipo_documento").html(documento.tipo_documento.descripcion);
                $("#info_asunto").html(detalle_tramite.asunto);
                $("#info_codigo_documento").html(documento.codigoDocumento);
                $("#info_de").html(documento.us001_de.name);
                $("#info_estado_documento").html(estado_documento);
                // $("#info_de").html();
                $("#info_para").html(`<ul style="margin-bottom: 0px; padding-left: 20px;">${para}</ul>`);
                $("#info_copia").html(`<ul style="margin-bottom: 0px; padding-left: 20px;">${copia}</ul>`);


                //cargamos el documento
                spinnerCargando("#info_vista_previa_documento", "Obteniendo Documento");
                $.get(`/obtenerDocumento/${disco}/${documento.rutaDocumento}.${documento.extension}`, function(docB64){
                    var encabezado = '<hr style="margin: 10px 0px;"><p style="font-weight: 700; font-size: 18px;"><i class="fa fa-desktop"></i> Vista previa del documento</p>';
                    $("#info_vista_previa_documento").html(encabezado+" "+`<iframe id="iframe_document" src="data:application/pdf;base64,${docB64}" type="application/pdf" frameborder="0" style="width: 100%; height: 800px;"></iframe>`);    
                });
                return;
                
            }
        });
    }

    //funcion para ver los documentos de un nodo en el organigrama
    function detalle_tramite_documentos(iddetalle_tramite){
        
        $("#content_visualizarDocumento_depa").html("");
        $("#modal_detalle_tramite_documentos").modal("show");
        $("#tbody_detalle_tramite_documentos").html(`<tr> <td colspan="6" style="padding-left: 15px;"><center>${getSpinnerCargando('Cargando Información...')}</center></td></tr>`);
        
        $.get('/detalleTramite/obtenerDocumentos/'+iddetalle_tramite, function(retorno){

            $("#tbody_detalle_tramite_documentos").html(`<tr> <td colspan="6" ><center>No hay documentos</center></td></tr>`);

            if(!retorno.error){ // consulta exitos
                
                var detalle_tramite = retorno.resultado;

                if(detalle_tramite.documento.length>0) {
                    $("#tbody_detalle_tramite_documentos").html(""); // solo si hay documentos limpiamos
                }

                $.each(detalle_tramite.documento, function (d, documento){

                    //verificamos por si es un documento que no se puede previsualizar

                    var boton_documento =`<button class="btn btn-info btn-sm btn-block" onclick="vista_previa_documento('${disco}','${documento.rutaDocumento}.${documento.extension}','DP')">
                                            <i class="fa fa-eye"></i> Visualizar
                                        </button>`;

                    if(documento.extension != "pdf" && documento.extension!="PDF"){
                        boton_documento = `<a class="btn btn-info btn-sm btn-block" href="/buscarDocumento/${disco}/${documento.rutaDocumento}.${documento.extension}" target="_blank">
                                            <i class="fa fa-download"></i> Descargar
                                        </a>`;
                    }

                    //ponemos el color para diferenciar si es un documento principal o un adjunto
                    var colorFila = 'bg-warning';
                    var nivelDoc = '<span class="label lable_estado label-danger">Adjunto</span>';
                    if(documento.tipo_creacion=="E"){
                        colorFila = 'bg-success';
                        nivelDoc = '<span class="label lable_estado label-success">Principal</span>';
                    }

                    
                    $("#tbody_detalle_tramite_documentos").append(`
                        <tr class="${colorFila}">
                            <td>${documento.tipo_documento.descripcion}</td>
                            <td>${documento.fechaCarga}</td>
                            <td>${documento.codigoDocumento}</td>
                            <td>${documento.descripcion}</td>
                            <td>${nivelDoc}</td>
                            <td>${boton_documento}</td>
                        </tr>
                    `);                     
                });

            }else{
                alert("No se pudo realizar la petición");
            }
        });
    }

    //funcion para visualizar un documento 
    function vista_previa_documento(disco, nombreDoc, donde){
        
        if(donde == "TD"){  vistacargando("M", "Espere..."); }
        else{ spinnerCargando("#content_visualizarDocumento_depa", "Obteniendo Documento"); }

        $.get(`/obtenerDocumento/${disco}/${nombreDoc}`, function(docB64){
            vistacargando();
            if(donde == "TD"){
                $("#modal_vista_previa_documento").modal("show");
                $("#content_visualizarDocumento").html(`<iframe id="iframe_document" src="data:application/pdf;base64,${docB64}" type="application/pdf" frameborder="0" style="width: 100%; height: 450px;"></iframe>`);
            }else if(donde=="DP"){
                var encabezado = '<hr style="margin: 10px 0px;"><p style="font-weight: 700;"><i class="fa fa-desktop"></i> Vista previa del documento</p>';
                $("#content_visualizarDocumento_depa").html(encabezado+" "+`<iframe id="iframe_document" src="data:application/pdf;base64,${docB64}" type="application/pdf" frameborder="0" style="width: 100%; height: 350px;"></iframe>`);
            }
        }).fail(function(){
            vistacargando();
        });
    }



    














