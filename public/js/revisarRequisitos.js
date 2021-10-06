
// GUARDAR LA REVISIÓN DE CADA UNO DE LOS REQUISITOS
$('#frm_enviarRevision').submit(function(e){
    e.preventDefault();
    vistacargando('M','Guardando...'); // mostramos la vista de carga

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var FrmData = new FormData(this);
    $.ajax({
        url: 'enviarRevision',
        method: 'POST',
        data: FrmData,
        dataType: 'json',
        contentType:false,
        cache:false,
        processData:false,
        success: function(NumRequisPend)   // Una función a ser llamada si la solicitud tiene éxito
        {
            // NumRequisPend: retorna el numero de requisitos que estan pendientes o que no estan correctos
            // se utliza para controlar cuando permitir subir los requisitos del FP
            vistacargando(); // ocultamos la vista de carga 
            mostrarAlerta('div_mensajeInfo','La revición se guardo con exito','success');

            if(NumRequisPend==0){  // si todos los requisitos estan correctos mostramos los requsitos del Funcionario publico
                cancelarEditarRevisionRequisitosC();
            }else{
                $('#btn_cancelarEditarRevisionRequisitosC').addClass('hidden'); // ocultamos el boton de cancelar, en el caso de que al modificar el estado de los requisitos se ponga con estado de incorrecto
            }
        },
        complete: function(){
            // si es completado
        },
        error: function(error){
            mostrarAlerta('div_mensajeInfo','Se produjo un error al realizar la petición. Comunique el problema al departamento de tecnología','danger');
            console.log(error);
            vistacargando(); // ocultamos la vista de carga
        }
    }); 
});

function verDocumentoRequisito(ruta,nombreRequisito){
   
    $('#modal_vista_requisito_doc').modal();
    $('#VistaPreviaRequisitoDoc').html(`<iframe id="iframe_requisito" class="iframeImg" src="${ruta}" style="width:100%; height: 500px;" frameborder="0"></iframe>`);
    
    // esperamos a que el iframe este cargado por completo
    $('#iframe_requisito').load(function(){ 
        // en el caso que el requisito sea una imagen es necesario agregar un ancho del 100%
        // la estructura del iframe es iframe > #document > html > body > (todo el contenido, en este caso accedemos a una imagen)
        var body = $(document.getElementById("iframe_requisito").contentWindow.document)[0].body; // obtenemos el body
        $(body).css('display','flex');
        var img = body.children[0]; // obtenemos la imagen del contenido del iframe
        $(img).css('width','100%');
    });
}

// evento para validar que todos los documentos esten subidos
function verificarDocumentosAgregados(){
    var numRequisitos=$('#tabla_tbody_RequisitosFP tr').length; // numero de requisitos que el FP tiene que subir
    var numeroArchivos=0; // inicializamos en cero  el numeor de archivos agregados
    $.each($('#tabla_tbody_RequisitosFP tr'), function (index, tr){
        numeroArchivos= numeroArchivos+$(tr).children('.tr_boton').children('input')[0].files.length; // obtenemos el numero de archivos que contiene cada input de la tabla de requisitos FP
        if(index+1 == numRequisitos){ // comprobamos que estemos en el ultimo tr para validar
            if(numeroArchivos==numRequisitos){ // si el numero de archivos es igual al numero de requisitos podemos finalizar la revisión
                $('#frm_finalizarRevicion').submit();
            }else{ // si el numero de archivos agregados no es igual al numero de requisitos es un error
                mostrarAlerta('mensajeInformacion','Antes de finalizar la revisión tiene que agregar todos los documentos','danger');
            }
        }
    });
    if(numRequisitos==0){ // si no tiene que subir documentos ejecutamos el formulario de finalizar Revisión
        $('#frm_finalizarRevicion').submit();
    }
}


function editarRevisionRequisitosC(){
    $('#div_contenedorRequisitosC').show(300);
    $('#div_contenedorRequisitosFP').hide(300);
    $('#div_btn_editarRevisionRequisitosC').hide(300);

    $('#btn_finalizarRevision').addClass('hidden');
    $('#btn_cancelarEditarRevisionRequisitosC').removeClass('hidden');
}

function cancelarEditarRevisionRequisitosC(){
    $('#div_contenedorRequisitosC').hide(300);
    $('#div_contenedorRequisitosFP').show(300);
    $('#div_btn_editarRevisionRequisitosC').show(300);

    $('#btn_finalizarRevision').removeClass('hidden');
    $('#btn_cancelarEditarRevisionRequisitosC').addClass('hidden');
}


$('.check_estadoR').click(function(e){
    if($(this).context.checked){ // si esta chekeado
        // mostramos el mensaje de Correcto
        $(this).siblings('.check_mensajeR').html('Corecto');
        // actualizamos el textarea de observaciones
        // accedemos al tr padre(tr_checkEstadoR) luego a su tipo el tr(tr_checkObservacionesR) que contiene el texarea con la observación
        txa_observacion=$(this).parents('.tr_checkEstadoR').siblings('.tr_checkObservacionesR').children('textarea');
        $(txa_observacion).val('Requisito Correcto');
        $(txa_observacion).addClass('soloinfo');
    }else{ // si no esta checkeado
        // mostramos el mensaje de Incorrecto
        $(this).siblings('.check_mensajeR').html('Incorrecto');
        // actualizamos el textarea de observaciones
        // accedemos al tr padre(tr_checkEstadoR) luego a su tipo el tr(tr_checkObservacionesR) que contiene el texarea con la observación
        txa_observacion=$(this).parents('.tr_checkEstadoR').siblings('.tr_checkObservacionesR').children('textarea');
        $(txa_observacion).val('');
        $(txa_observacion).removeClass('soloinfo');
    }

});



// GUARGAR LOS REQUISITOS POR DEL FUNCIONARIO PUBLICO

function subirRequisitoFPDocumento(btn){
    var inputfile = $(btn).siblings('.input_file');
    //obtenemos el nombre del archivo selecionado
    archivo="Seleccione un archivo";

    if(inputfile[0].files.length>0){ // si se a seleccionado un archivo
        archivo=(inputfile[0].files[0].name);
        $('#VistaPreviaMesjRequisitoFP').attr('hidden',true);
        $('#VistaPreviaRequisitoFP').attr('hidden',false);
        $('#VistaPreviaRequisitoFP').html(`<iframe src="${URL.createObjectURL(inputfile[0].files[0])}" style="width:100%; height: 400px;" frameborder="0"></iframe>`);

        $('#nombreRequisitoFPSeleccionado').val(archivo);
    }else{
        $('#VistaPreviaMesjRequisitoFP').attr('hidden',false);
        $('#VistaPreviaRequisitoFP').attr('hidden',true);
        $('#VistaPreviaRequisitoFP').html('');

        $('#nombreRequisitoFPSeleccionado').val("Seleccione un documento");
    }

    $('#label_subirDocumento').prop('for',inputfile[0].id);
    $('#modal_subir_documento_fp').modal();
}

function visualizarDocumentoSeleccionado(inputfile){

    //obtenemos el nombre del archivo selecionado
    archivo="Seleccione un archivo";
    if($(inputfile)[0].files.length>0){ // si se a seleccionado un archivo
        archivo=($(inputfile)[0].files[0].name);
        $('#VistaPreviaMesjRequisitoFP').attr('hidden',true);
        $('#VistaPreviaRequisitoFP').attr('hidden',false);
        $('#VistaPreviaRequisitoFP').html(`<iframe src="${URL.createObjectURL($(inputfile)[0].files[0])}" style="width:100%; height: 400px;" frameborder="0"></iframe>`);
        
        $('#nombreRequisitoFPSeleccionado').val(archivo);

        //cambiamos el estado del requisitoFP
        $(inputfile).parent('.tr_boton').siblings('.tr_EstadoDoc').html(
            '<center><i class="fa fa-check-square" style="font-size: xx-large; color: #26B99A;"></i></center>'
        );

        // cambiamos el texto del boton Agregar Documento
        $(inputfile).siblings('button').html('Cambiar Documento <i class="fa fa-edit">');
        $(inputfile).siblings('button').removeClass('btn-primary');
        $(inputfile).siblings('button').addClass('btn-info');
    }else{ // si no se selecciona un archivo
        $('#VistaPreviaMesjRequisitoFP').attr('hidden',false);
        $('#VistaPreviaRequisitoFP').attr('hidden',true);
        $('#VistaPreviaRequisitoFP').html('');

        $('#nombreRequisitoFPSeleccionado').val("Seleccione un documento");

        //cambiamos el estado del requisitoFP
        $(inputfile).parent('.tr_boton').siblings('.tr_EstadoDoc').html(
            '<center><i class="fa fa-square-o" style="font-size: xx-large; color: #26B99A;"></i></center>'
        );

        // cambiamos el texto del boton Agregar Documento
        $(inputfile).siblings('button').html('Agregar Documento <i class="fa fa-cloud-upload">');
        $(inputfile).siblings('button').removeClass('btn-info');
        $(inputfile).siblings('button').addClass('btn-primary');
    }

}


$('#selecRequisitoFP').change(function(e){
    console.log("cambiado");
    //obtenemos el nombre del archivo selecionado
    archivo="Seleccione un archivo";
    if(this.files.length>0){ // si se a seleccionado un archivo
        archivo=(this.files[0].name);
        $('#VistaPreviaMesjRequisitoFP').attr('hidden',true);
        $('#VistaPreviaRequisitoFP').attr('hidden',false);
        $('#VistaPreviaRequisitoFP').html(`<iframe src="${URL.createObjectURL(e.target.files[0])}" style="width:100%; height: 400px;" frameborder="0"></iframe>`);
        
        $('#nombreRequisitoFPSeleccionado').val(archivo);
    }else{ // si no se selecciona un archivo
        $('#VistaPreviaMesjRequisitoFP').attr('hidden',false);
        $('#VistaPreviaRequisitoFP').attr('hidden',true);
        $('#VistaPreviaRequisitoFP').html('');
        $('#nombreRequisitoFPSeleccionado').val("Seleccione un documento");
    }
    
});

// FIN