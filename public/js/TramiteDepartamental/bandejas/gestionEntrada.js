
    function verTramite(iddetalle_tramite,btn){
        $(btn).tooltip("hide"); // ocultamos el tooltip del boton precionado
        $("#listaTramites_entrada").hide(200);
        $("#contet_ver_tramite").show(200);
        mostrarDetalleTramite(iddetalle_tramite); // reutilizada de otro jquery

        //cargamos en la vista general los botones atender y terminar
        var td_botones = $(btn).parent().siblings('.botones_para');
        $("#botones_para").html("");
        $.each(td_botones, function(index, td_boton){
            var boton =  $(td_boton).children();
            addBoton = "";
            if(index==0){
                addBoton = `<a href="${boton.attr("href")}" class="btn btn-warning btn_regresar" ><i class="fa fa-edit"></i> Atender</a>`;
                //boton para devolver un trámite
                iddetalle_tramite_encrypt = $(td_boton).attr('data-detalle');
                addBoton = addBoton+`<button class="btn btn-outline-danger btn_regresar" onclick="devolverTramite('${iddetalle_tramite_encrypt}')"><i class="fa fa-history"></i> Devolver</button>`;
                addBoton = addBoton+`<a href="/detalleTramite/denegarTramite?iddetalle_tramite=${iddetalle_tramite_encrypt}" class="btn btn-danger btn_regresar" style="color: #fff"><i class="fa fa-thumbs-o-down"></i> Denegar</button>`;
            }else{
                addBoton = `<a href="${$(boton).attr("href")}" class="btn btn-success btn_regresar" style="color: #fff"><i class="fa fa-thumbs-o-up"></i> Terminar</button>`;
            }
            $("#botones_para").append(addBoton);

        });
    }

    function cerrarDetalleTramite(){
        $("#listaTramites_entrada").show(200);
        $("#contet_ver_tramite").hide(200);
    }

    // funciones para devolver un trámite

        function devolverTramite(iddetalle_tramite_encrypt){
            $("#modal_detalle_devolver").modal("show");
            $("#frm_devolverTramite").attr("action", `/detalleTramite/devolverTramite/${iddetalle_tramite_encrypt}`);
            $("#textarea_detalle_revision").val("");
        }

        $("#frm_devolverTramite").submit(function(e){
            e.preventDefault();

            var ruta = $(this).attr("action");
            var FrmData = new FormData(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            vistacargando('M','Devolviendo...'); // mostramos la ventana de espera

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
                        // refrescamos la tabla de trámites
                        cerrarDetalleTramite();
                        filtratTramiteEntrada();
                        $("#modal_detalle_devolver").modal("hide");
                    }

                    vistacargando(); // ocultamos la ventana de espera 
                    
                },
                error: function(error){
                    // alertNotificar(","error");
                    vistacargando(); // ocultamos la ventana de espera
                }
            }); 
        });

    // filtrar los tramies de entrada por selección
    $(".cmb_filtrarTramite").change(function(e){
        filtratTramiteEntrada();
    });

    // funcion para filtrar los tramies de entrada
    function filtratTramiteEntrada(){
        vistacargando("M", "Buscando...");
        var iddepartamento = $("#cmb_departamento").val();
        var idtipo_tramite = $("#cmb_tipoTramite").val();
        vistacargando("M", "Filtrando...");
        $.get(`/gestionBandeja/filtrarEntrada/${iddepartamento}/${idtipo_tramite}`, function(retorno){

            // no cargamos la los tramites de entrada si la tabla está oculta
            if(!$("#listaTramites_entrada").is(":visible")){
                return;
            }

            // cargamos la imformacion del lenguaje
            var datatable = {
                placeholder: "Ejm: GADM-000-2020-N"
            }

            // cargamos los datos a la tabla
            
            var tablatramite = $('#tabla_tramites').DataTable({
                dom: ""
                +"<'row' <'form-inline' <'col-sm-6 inputsearch'f>>>"
                +"<rt>"
                +"<'row'<'form-inline'"
                +" <'col-sm-6 col-md-6 col-lg-6'l>"
                +"<'col-sm-6 col-md-6 col-lg-6'p>>>",
                "destroy":true,
                order: [[ 2, "desc" ]],
                pageLength: 10,
                sInfoFiltered:false,
                language: datatableLenguaje(datatable),
                data: retorno.resultado,
                columnDefs: [
                    {  className: "sorting_1 todo_mayus", targets: 0 },
                    {  className: "todo_mayus", targets: 1 },
                    {  className: "todo_mayus", targets: 2 },
                    {  className: "todo_mayus", targets: 3 },
                    {  className: "todo_mayus", targets: 4 },
                    {  className: "bg-warning", targets: 5 },
                    {  className: "ocultar", targets: 6 },
                    {  className: "ocultar", targets: 7 }
                ],
                columns:[
                    {data: "detalle_tramite.tramite.prioridad.descripcion" },
                    {data: "detalle_tramite.tramite.codTramite" },
                    {data: "detalle_tramite.tramite.fechaCreacion" },
                    {data: "detalle_tramite.departamento_origen.nombre" },
                    {data: "detalle_tramite.asunto" },
                    {data: "detalle_tramite.departamento_origen.nombre" },
                    {data: "detalle_tramite.departamento_origen.nombre" },
                    {data: "detalle_tramite.departamento_origen.nombre" },
                ],
                "rowCallback": function( row, destino, index ){
                    
                    var mostrarTerminar = false;
                    var iddepaLog = $("#iddepaLog").val();

                    if(destino.detalle_tramite.flujo == null){ // flujo no definido
                      if(destino.tipo_envio == "P"){
                        mostrarTerminar = true;
                      }
                    }else{ // flujo definido

                        $.each(destino.detalle_tramite.flujo.flujo_hijo, function(fh, flujo_hijo){
                            if(flujo_hijo.iddepartamento == iddepaLog){
                                if(flujo_hijo.tipo_flujo == "G" && flujo_hijo.estado_finalizar == 1){
                                    mostrarTerminar = true;
                                }
                            }
                        });

                    }
        
                    // $('td', row).eq(0).html(`<center>${index+1}</center>`);// primer fila
                    $('td', row).eq(5).html(`<button type="button" onclick="verTramite('${destino.detalle_tramite.iddetalle_tramite_encrypt}',this)" class="btn btn-sm btn-info" style="margin-bottom: 0;"><i class="fa fa-eye"></i> Ver Detalle</button>`);

                    //botones solo permitidos para destinos 'PARA'
                    if(destino.tipo_envio =="P"){
                        $('td', row).eq(6).html(`<a href="/detalleTramite/atenderDetalleTramite?iddetalle_tramite=${destino.detalle_tramite.iddetalle_tramite_encrypt}" class="btn btn-sm btn-warning" style="margin-bottom: 0;"><i class="fa fa-edit"></i></a>`);
                        $('td', row).eq(6).addClass('botones_para');
                        $('td', row).eq(6).attr('data-detalle', destino.detalle_tramite.iddetalle_tramite_encrypt); // para cargar el boton de "DEVOLVER" trámite

                        if(mostrarTerminar == true){ // boton terminar trámite
                            $('td', row).eq(7).html(`<a href="/detalleTramite/terminarTramite?iddetalle_tramite=${destino.detalle_tramite.iddetalle_tramite_encrypt}" class="btn btn-sm btn-success" style="margin-bottom: 0;"><i class="fa fa-thumbs-o-up"></i></a>`);                
                            $('td', row).eq(7).addClass('botones_para');                               
                        }else{
                            $('td', row).eq(7).html('<i class="fa fa-ban icon_stop"></i>');
                        }
                 
                    }else{
                        $('td', row).eq(6).html('<i class="fa fa-ban icon_stop"></i>');
                        $('td', row).eq(7).html('<i class="fa fa-ban icon_stop"></i>');
                    }


                }                                
            });

            // quitamos la clase solo "bg-warning" y "todo_mayus" solo en la cabecera
            var columnas = $(tablatramite.table().header()).children('tr').find('th');
            $(columnas).removeClass('bg-warning');
            $(columnas).removeClass('todo_mayus');

            vistacargando();
            console.warn("Bandeja de entrada actualizada");

        });

        vistacargando();   
    }

    $(document).ready(function () {
        var timeout = timeRefresh*1000; // convertimos a milisegundos
        setTimeout(() => {
            setInterval(filtratTramiteEntrada, timeout);
        }, (3000));
    });