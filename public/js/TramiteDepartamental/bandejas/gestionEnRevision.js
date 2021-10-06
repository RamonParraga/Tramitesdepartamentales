

function verTramite(iddetalle_tramite){
   
    $("#listaTramites_enElaboracion").hide(200);
    $("#contet_ver_tramite").show(200);
    mostrarDetalleTramite(iddetalle_tramite);

    //cargar información de los botones
    $("#btn_revision_corregir").prop('href', '/detalleTramite/editarDetalleTramite?iddetalle_tramite='+iddetalle_tramite);
    $("#btn_revision_eliminar").attr('onclick', `eliminarTramite('${iddetalle_tramite}',this)`);
}


function eliminarTramite(iddetalle_tramite,boton){

    swal({
        title: "",
        text: "¿Está seguro que desea eliminarlo?",
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancela!",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        
        if (isConfirm) { // si dice que quiere eliminar
            $(boton).parent('form').attr('action','/detalleTramite/eliminar/'+iddetalle_tramite);
            $(boton).parent('form').submit();          
        }

        sweetAlert.close();   // ocultamos la ventana de pregunta
    }); 

}



function cerrarDetalleTramite(){
    $("#listaTramites_enElaboracion").show(200);
    $("#contet_ver_tramite").hide(200);

    //quitar información de los botones
    $("#btn_revision_corregir").prop('href', '');

}