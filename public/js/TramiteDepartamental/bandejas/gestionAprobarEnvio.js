

function verTramite(iddetalle_tramite){

    $("#listaTramites_enElaboracion").hide(200);
    $("#contet_ver_tramite").show(200);
    mostrarDetalleTramite(iddetalle_tramite);

    $("#id_detalle_tramite_encrypt").val(iddetalle_tramite); // para tenerlo a la mano

    //cargar información a los botones
    $("#btn_rev_aprobar").attr('onclick', `aprobarTramite('${iddetalle_tramite}')`);

    //cargamos las rutas de los formularios
    $("#frm_enviaraRevision").prop('action', "/revisionTramite/enviaraRevisionDetalleTramite/"+iddetalle_tramite);
    $("#form_subir_documento_firmado").prop('action',"/revisionTramite/subirDocumentoFirmado/"+iddetalle_tramite);
}


function cerrarDetalleTramite(){
    $("#listaTramites_enElaboracion").show(200);
    $("#contet_ver_tramite").hide(200);

    //limpiamos la información de los botones 

}


// cargamos la tabla
$(document).ready(function () {

    $('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0','overflow-x':'inherit'});
   
    $("#tabla_tramites").DataTable({
        dom: ""
        +"<'row' <'form-inline' <'col-sm-6 inputsearch'f>>>"
        +"<rt>"
        +"<'row'<'form-inline'"
        +" <'col-sm-6 col-md-6 col-lg-6'l>"
        +"<'col-sm-6 col-md-6 col-lg-6'p>>>",
        pageLength: 10,
        "language": {
            "lengthMenu": 'Mostrar <select class="form-control input-sm">'+
                        '<option value="5">5</option>'+
                        '<option value="10">10</option>'+
                        '<option value="15">15</option>'+
                        '<option value="20">20</option>'+
                        '<option value="30">30</option>'+
                        '<option value="-1">Todos</option>'+
                        '</select> registros',
            "search": "<b><i class='fa fa-search'></i> Buscar: </b>",
            "searchPlaceholder": "Ejm: GADM-000-2020-N",
            "zeroRecords": "No se encontraron registros coincidentes",
            "infoEmpty": "No hay registros para mostrar",
            "infoFiltered": " - filtrado de MAX registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "paginate": {
                "previous": "Anterior",
                "next": "Siguiente"
            }
        }
    });

});