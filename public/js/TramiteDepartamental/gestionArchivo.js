
function gestionarchivo_editar(id_gestion_archivo){
    //quito la clase hidden para mostrar el contenido del formulario de edicion
    $('#adm_edicion').removeClass('hidden');
    //oculto el contenido del listado de los tramites archivados
    $('#admlistado').addClass('hidden');
    $('#contenedor_bod').html('');
    $('#id_tablaprioridadtramite_editar').html('');
    $('.bodega').hide();
    // $('#tablaregistro').addClass('hidden');
  
    $('#tablaeditar').removeClass('hidden');
    

   
    vistacargando('M','Espere...'); // mostramos la ventana de espera
    $.get("listado/"+id_gestion_archivo+"/edit", function (data) {
    $("#id_tablaprioridadtramite").html("");
    //Mostramos los datos para editar
    console.log(data);
    // $('#id_nombre').val(data.nombre);

    //obtenemos el nombre del lugar de almacenamiento del archivo
     var bodegaTexto=data.resultado.seccion.sector.bodega.nombre+" "+data.resultado.seccion.sector.descripcion
     +" "+data.resultado.seccion.descripcion;
     //obtenemos el id del lugar de almacenamiento del archivo
    var idlugarBodega =data.resultado.id_seccion;
    

                //mostramos el contendor lugar de almacenamiento
                $(".modal_Bodega").modal("hide"); //ocultamos la modal para ver la animación
                // $("#area_listaBodega").hide(); //ocultamos para dar animación de entrada
                // $("#area_listaDocumentos").removeClass("hidden"); //quidamos clase que oculta el div
                $("#area_listaBodega").show(250); //damos animación de entrada

                //agregamos el lugar de almacenamiento de seleccionado
     $("#contenedor_bod").append(`
                    <div class="alert f_documento fade in" style="margin-bottom: 5px;">
                        <button type="button" class="close" onclick="quitar_lugar_bodega(this)"><span aria-hidden="true">×</span>
                        </button>
                        <strong><i class="fa fa-archive"></i></strong> ${bodegaTexto}


                        <input type="hidden" name="input_lugar_bod" value="${idlugarBodega}">

                    </div>
                `);





    var carpetavalor=(data.resultado.folder);
    $('#carpeta').val(data.resultado.folder);
    $('#codigotramite').val(data.resultado.seccion.descripcion);
    $('#asunto').val(data.resultado.seccion.descripcion);
    console.log(data.resultado.seccion.descripcion);

    
    

        $('#id_tablaprioridadtramite_editar').append('<tr><td>' + 1 + '</td><td>' 
        + data.resultado.tramitedoc.codTramite +
     '</td><td>' + data.resultado.tramitedoc.asunto + '</td><td>' 
     + data.resultado.tramitedoc.observacion + '</td></tr>');
     

    
    vistacargando(); // ocultamos la ventana de espera
}).fail(function(){
    // si ocurre un error
    vistacargando(); // ocultamos la vista de carga
    alert('Se produjo un error al realizar la petición. Comuniquese el problema al departamento de tecnología');
});

    $('#method_gestionarchivo').val('PUT'); // decimo que sea un metodo put
    $('#id_frmgestionarchivo').prop('action',window.location.protocol+'//'+window.location.host+'/archivo/listado/'+id_gestion_archivo);
    $('#btn_gestionarchivo_cancelar').removeClass('hidden');

    $('html,body').animate({scrollTop:$('#adm_edicion').offset().top},400);
}

$('#btn_gestionarchivo_cancelar').click(function(){


    $('#tablaeditar').addClass('hidden');
   // $('#t').show();
    $('#tablaregistro').removeClass('hidden');
    $('#carpeta').val('');
    $('#id_ubicacion').val('');

    
    $('#id_frmgestionarchivo').prop('action',window.location.protocol+'//'+window.location.host+'/archivo/listado');
    $(this).addClass('hidden');
});



    //funcion para quitar un tipo de documento al dar a la X de cerrar
    function quitar_lugar_bodega(boton){
        
        $(boton).parent().hide(200); // ocultamos el tipo de documento
        setTimeout(function(){ // esperamos unos segundos para dar animacion

            $(boton).parent().remove();
            // comprobamos si hay o no tipo de documentos agregados
            // preguntamos si no hay tipos de documentos "div"

            if($("#contenedor_bod").find('div').length==0){
                //ocultamos el contenedor de tipo de documentos
                $('#area_listaBodega').hide(200);
                $('.bodega').show();

            }

            //mostramos en la modal el tipo de documento quitado
            var idTDeliminado=$(boton).siblings('input').val(); // id del tipo de documento quitado

            // mostramos en la modal el tipo de documento quitado
            $(`#li_lugar_bod_${idTDeliminado}`).show();

        }, 250);
        
    }

    //////////////////////////////////////////////////////////////////////////////////////
    //////////funcion 

 
   //cuando selecciono una bodega de la modal 
    $('input:checkbox[name=chk]').on('ifChecked', function() {
    $("#contenedor_bod").html("");
    $('.bodega').hide();


   
    

      // recorremos todos los input que tengan la clase 'td_seleccionado' ya que son los checked de la modal
        $(".td_seleccionado").each(function(index, input){

            //preguntamos solo por los que estan seleccionados con el checked
            if( $(input).prop('checked')){
                //obtenemos el texto del lugar de almacenamiento seleccionado
                var bodegaTexto=$(input).parents(".label_doc_act_select").text();
                //obtenemos el id del lugar de almacenamiento seleccionado
                var idlugarBodega = $(input).val();

                //ocultamos la modal
                $(".modal_Bodega").modal("hide"); //ocultamos la modal para ver la animación
                // $("#area_listaDocumentos").hide(); //ocultamos para dar animación de entrada
                // $("#area_listaDocumentos").removeClass("hidden"); //quidamos clase que oculta el div
                $("#area_listaBodega").show(250); //damos animación de entrada

                //agregamos el tipo de documento seleccionado al listado de documentos requeridos


                $("#contenedor_bod").append(`
                    <div class="alert f_documento fade in" style="margin-bottom: 5px;">
                        <button type="button" class="close" onclick="quitar_lugar_bodega(this)"><span aria-hidden="true">×</span>
                        </button>
                        <strong><i class="fa fa-archive"></i></strong> ${bodegaTexto}


                        <input type="hidden" name="input_lugar_bod" value="${idlugarBodega}">

                    </div>
                `);

                //deseleccionamos el tipo de documento y quitamos el efecto de checkeado
                $(input).iCheck('uncheck');
                $('input[type=checkbox]').ifChecked('uncheck');
                //$(input).prop("checked", false);
                 $(input).parent(".checked").removeClass("checked");
                //ocultamos el tipo de documento de la lista en la modal
                //$(input).parents("li").hide();

            }
        });


    
});
 

function filtrarArchivoportexto()
{
       var busqueda = $("#busqueda").val();
        if(busqueda === ''){
        alert("Ingrese una descripción");
        return false;
         }
         else{
        //console.log(busqueda);
       // var fin = $("#fin").val();
        vistacargando("M", "Filtrando...");
        $.get(`/archivo/listado/${busqueda}/filtrarportexto`, function(retorno){
            
            
//             var bodegaTexto=retorno.resultado[0].seccion.sector.descripcion;
// console.log(bodegaTexto);

          
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
                pageLength: 10,
                sInfoFiltered:false,
                language: datatableLenguaje(datatable),
                order: [[ 1, "desc" ]],
                data: retorno.resultado,
                columns:[
                    {data: "tramitedoc.codTramite" },
                    {data: "tramitedoc.asunto" },
                    {data: "tramitedoc.observacion" },
                    {data: "fecha_gestion" },
                    {data: "fecha_movimiento" },
                    {"render":
                     function ( data, type, row ) {
                    return (row.seccion.sector.bodega.nombre + ' - ' + row.seccion.sector.descripcion + ' - ' + row.seccion.descripcion );
                        }
                     }, 
                    
                    {data: "folder" },
                    {data: "folder"  },
                ],
                "rowCallback": function( row, gestion_archivo, index ){
        
                    //$('td', row).eq(0).html(`<center>${index+1}</center>`);// primer fila
                    $('td', row).eq(7).html(` <button type="button" onclick="gestionarchivo_editar('${gestion_archivo.id_gestion_archivo_encrypt}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> </button>`);               
                   
                }                                
            });

            
            vistacargando();
            console.warn("Bandeja de entrada actualizada");

        });

        
        }  
    }



 function filtratArchivoporfechas(){
        //vistacargando("M", "Buscando...");
        var inicio = $("#inicio").val();
        var fin = $("#fin").val();
        console.log(fin);

        if(inicio ==''){
        alert("Seleccione un rango de fecha");
        return false;
         }
        if(fin ==''){
        alert("Seleccione un rango de fecha");
        return false;
         }

        vistacargando("M", "Filtrando...");
        $.get(`/archivo/listado/${inicio}/${fin}/filtrar`, function(retorno){
            console.log(retorno);

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
                pageLength: 10,
                sInfoFiltered:false,
                language: datatableLenguaje(datatable),
                 order: [[ 1, "desc" ]],
                data: retorno.resultado,
                columns:[
                    {data: "tramitedoc.codTramite" },
                    {data: "tramitedoc.asunto" },
                    {data: "tramitedoc.observacion" },
                    {data: "fecha_gestion" },
                    {data: "fecha_movimiento" },
                    {"render":
                     function ( data, type, row ) {
                    return (row.seccion.sector.bodega.nombre + ' - ' + row.seccion.sector.descripcion + ' - ' + row.seccion.descripcion );
                        }
                     }, 
                    {data: "folder" },
                    {data: "folder"  },
                ],
                "rowCallback": function( row, gestion_archivo, index ){
        
                   
                    $('td', row).eq(7).html(` <button type="button" onclick="gestionarchivo_editar('${gestion_archivo.id_gestion_archivo_encrypt}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> </button>`);               
                    
                }                                
            });

           

            vistacargando();
            console.warn("Bandeja de entrada actualizada");

        });

        //vistacargando(); 
         
    }


   
   function filtrarArchivoporlugar()
{
   

     //vistacargando("M", "Buscando...");
        var lugar = $("#cmb_lugar").val();
        console.log(lugar);
          if(lugar === ''){
        alert("Seleccione una opción");
        return false;
         }
         else{
       // var fin = $("#fin").val();
        vistacargando("M", "Filtrando...");
        $.get(`/archivo/listado/${lugar}/filtrarporlugar`, function(retorno){
            

          
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
                pageLength: 10,
                sInfoFiltered:false,
                language: datatableLenguaje(datatable),
                 order: [[ 1, "desc" ]],
                data: retorno.resultado,
                columns:[
                    {data: "tramitedoc.codTramite" },
                    {data: "tramitedoc.asunto" },
                    {data: "tramitedoc.observacion" },
                    {data: "fecha_gestion" },
                    {data: "fecha_movimiento" },
                    {"render":
                     function ( data, type, row ) {
                    return (row.seccion.sector.bodega.nombre + ' - ' + row.seccion.sector.descripcion + ' - ' + row.seccion.descripcion );
                        }
                     }, 
                    
                    {data: "folder" },
                    {data: "folder"  },
                ],
                "rowCallback": function( row, gestion_archivo, index ){
        
                    
                    $('td', row).eq(7).html(` <button type="button" onclick="gestionarchivo_editar('${gestion_archivo.id_gestion_archivo_encrypt}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> </button>`);               
                    
                }                                
            });

          

            vistacargando();
            console.warn("Bandeja de entrada actualizada");

        });

        //vistacargando(); 
        }  
    }

    $('#check_ultmio').on('ifChecked', function(event){
   

     //vistacargando("M", "Buscando...");
        var ultimo = 'ultimo';
        console.log(ultimo);
         
       // var fin = $("#fin").val();
        vistacargando("M", "Filtrando...");
        $.get(`/archivo/listado/${ultimo}/filtrarporultimo`, function(retorno){
            

          
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
                pageLength: 10,
                sInfoFiltered:false,
                language: datatableLenguaje(datatable),
                 order: [[ 1, "desc" ]],
                data: retorno.resultado,
                columns:[
                    {data: "tramitedoc.codTramite" },
                    {data: "tramitedoc.asunto" },
                    {data: "tramitedoc.observacion" },
                    {data: "fecha_gestion" },
                    {data: "fecha_movimiento" },
                    {"render":
                     function ( data, type, row ) {
                    return (row.seccion.sector.bodega.nombre + ' - ' + row.seccion.sector.descripcion + ' - ' + row.seccion.descripcion );
                        }
                     }, 
                    
                    {data: "folder" },
                    {data: "folder"  },
                ],
                "rowCallback": function( row, gestion_archivo, index ){
        
                    
                    $('td', row).eq(7).html(` <button type="button" onclick="gestionarchivo_editar('${gestion_archivo.id_gestion_archivo_encrypt}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> </button>`);               
                    
                }                                
            });

          

            vistacargando();
            console.warn("Bandeja de entrada actualizada");

        });

        //vistacargando(); 
          
    });



  


    $('#check_fecha').on('ifChecked', function(event){
    $("#busqueda").val('');
    $('.option_lugar').prop('selected',false); // deseleccionamos las zonas seleccionadas
    $("#cmb_lugar").trigger("chosen:updated"); // actualizamos el combo de zonas

    $('#check_tramite').iCheck('uncheck');
    $('#check_ultmio').iCheck('uncheck');
    $('#check_lugar').iCheck('uncheck');
    
    $('#busquedafecha').removeClass('hidden');
    $('#busquedalugar').addClass('hidden');
    $('#busquedatexto').addClass('hidden');
    
     var tablatramite = $('#tabla_tramites').DataTable();
     tablatramite
     .clear()
     .draw();

     


  });

    $('#check_fecha').on('ifUnchecked', function(event){
    $('#busquedafecha').addClass('hidden');
    $("#inicio").val("");
    $("#fin").val("");
    
     var tablatramite = $('#tabla_tramites').DataTable();
     tablatramite
     .clear()
     .draw();

     


  });

    
    $('#check_tramite').on('ifChecked', function(event){

    $("#inicio").val("");
    $("#fin").val("");
    $('.option_lugar').prop('selected',false); // deseleccionamos las zonas seleccionadas
    $("#cmb_lugar").trigger("chosen:updated"); // actualizamos el combo de zonas

    
    $('#check_fecha').iCheck('uncheck');
    $('#check_lugar').iCheck('uncheck');
    $('#check_ultmio').iCheck('uncheck');
    $('#busquedatexto').removeClass('hidden');
    $('#busquedalugar').addClass('hidden');
     $('#busquedafecha').addClass('hidden');

    var tablatramite = $('#tabla_tramites').DataTable();
     tablatramite
     .clear()
     .draw();
     });

    
    $('#check_tramite').on('ifUnchecked', function(event){
    $('#busquedatexto').addClass('hidden');
    $("#busqueda").val('');
    var tablatramite = $('#tabla_tramites').DataTable();
     tablatramite
     .clear()
     .draw();

     


  });

     
     //$('#fecha_').reset();

    $('#check_lugar').on('ifChecked', function(event){
    $("#inicio").val("");
    $("#fin").val("");
    $("#busqueda").val('');
    $('#check_tramite').iCheck('uncheck');
    $('#check_fecha').iCheck('uncheck');
    $('#check_ultmio').iCheck('uncheck');
    $('#busquedalugar').removeClass('hidden');
    $('#busquedatexto').addClass('hidden');
    $('#busquedafecha').addClass('hidden');

      var tablatramite = $('#tabla_tramites').DataTable();
     tablatramite
     .clear()
     .draw();
    });


    $('#check_lugar').on('ifUnchecked', function(event){
    $('#busquedalugar').addClass('hidden');
    $('.option_lugar').prop('selected',false); // deseleccionamos las zonas seleccionadas
    $("#cmb_lugar").trigger("chosen:updated"); // actualizamos el combo de zonas
    
     var tablatramite = $('#tabla_tramites').DataTable();
     tablatramite
     .clear()
     .draw();

     


  });
   
    $('#check_ultmio').on('ifChecked', function(event){
    
    $('#check_tramite').iCheck('uncheck');
    $('#check_fecha').iCheck('uncheck');
    $('#check_lugar').iCheck('uncheck');
    $('#busquedalugar').addClass('hidden');
    $('#busquedatexto').addClass('hidden');
    $('#busquedafecha').addClass('hidden');

      var tablatramite = $('#tabla_tramites').DataTable();
     tablatramite
     .clear()
     .draw();
    });

   
    $('#check_ultmio').on('ifUnchecked', function(event){
    // $('#busquedalugar').addClass('hidden');
    // $('.option_lugar').prop('selected',false); // deseleccionamos las zonas seleccionadas
    // $("#cmb_lugar").trigger("chosen:updated"); // actualizamos el combo de zonas
    
     var tablatramite = $('#tabla_tramites').DataTable();
     tablatramite
     .clear()
     .draw();

     


  });