$(document).ready(function(){
    $('#id_tablatipodocumento').DataTable( {
           "language": {
               "lengthMenu": 'Mostrar <select class="form-control input-sm">'+
                           '<option value="5">5</option>'+
                           '<option value="10">10</option>'+
                           '<option value="20">20</option>'+
                           '<option value="30">30</option>'+
                           '<option value="40">40</option>'+
                           '<option value="-1">Todos</option>'+
                           '</select> registros',
               "search": "Buscar:",
               "zeroRecords": "No se encontraron registros coincidentes",
               "infoEmpty": "No hay registros para mostrar",
               "infoFiltered": " - filtrado de _MAX_ registros",
               "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
               "paginate": {
                   "previous": "Anterior",
                   "next": "Siguiente"
           }
       }
} );
});

//
//Gestion Tipo Documento
//
function TipoDocumento_editar(idtipodocumento){
    vistacargando('M','Espere...'); // mostramos la ventana de espera
    $.get("gestion/"+idtipodocumento+"/edit", function (data) {
    //Mostramos los datos para editar
    // console.log(data);
    $('#id_descripcion').val(data.descripcion);
    $('#id_abreviacion').val(data.abreviacion);
    $('#id_estructura').val(data.estructura);
    $('#id_secuencia').val(data.secuencia);
    $('#id_prioridad').val(data.prioridad);
    
    vistacargando(); // ocultamos la ventana de espera
}).fail(function(){
    // si ocurre un error
    vistacargando(); // ocultamos la vista de carga
    alert('Se produjo un error al realizar la petición. Comunique el problema al departamento de tecnología');
});

    $('#method_tipodocumento').val('PUT'); // decimo que sea un metodo put
    $('#id_frmtipodocumento').prop('action',window.location.protocol+'//'+window.location.host+'/tipodocumento/gestion/'+idtipodocumento);
    $('#btn_tipodocumentocancelar').removeClass('hidden');

    // $('html,body').animate({scrollTop:$('#administrador_permisos').offset().top},400);
}

$('#btn_tipodocumentocancelar').click(function(){
    $('#id_descripcion').val('');
    $('#id_abreviacion').val('');
    $('#id_estructura').val('');
    $('#id_secuencia').val('');

    $('#method_tipodocumento').val('POST'); // decimo que sea un metodo put
    $('#id_frmtipodocumento').prop('action',window.location.protocol+'//'+window.location.host+'/tipodocumento/gestion');
    $(this).addClass('hidden');
});

// function TipoDocumento_eliminar(btn){
//     if(confirm('¿Quiere eliminar el registro?')){
//         $(btn).parent('.frm_eliminar').submit();
//     }
// }


function TipoDocumento_eliminar(idtipo_documento){
    if(!confirm("Esta seguro que quiere eliminar el Tipo de Tramite")){
        return;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: '/tipodocumento/gestion/'+idtipo_documento,
        type: 'DELETE',
    });
    location.reload();
}
