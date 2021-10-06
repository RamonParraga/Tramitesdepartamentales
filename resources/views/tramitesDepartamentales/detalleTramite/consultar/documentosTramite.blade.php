
<style type="text/css">
    
    .lable_estado{
        padding: 5px 8px 5px 8px;
        font-size: 14px;
        display: block;
        text-transform: none;
        font-weight: 500;
        border-radius: .90em;
    }

</style>

<a id="btn_descargar_todos_documentos" href="" id="btn_descargar_todos_documentos" target="_blank" class="btn btn-outline-info" style="float: right; margin-bottom: 10px;"> <i class="fa fa-download"></i> Descargar Todos los Documentos</a>

<div class=" table-responsive" style="padding: 0;">
    <table style="color: black; margin-bottom: 0px;" class="table table-row-center-vertical table-bordered dataTable no-footer table-row-center-vertical" role="grid" aria-describedby="datatable_info">
        <thead>
            <tr role="row">                                                       
                <tr>
                    <th style="width: 1px;"></th>
                    <th>Departamento</th>
                    <th>Tipo Documento</th>
                    <th>Fecha</th>
                    <th>Código</th>
                    <th>Descripción</th> 
                    <th style="width: 10px;">Nivel</th>                       
                    <th style="width: 10px;">Ver</th>
                </tr>
            </tr>
        </thead>         
        
        <tbody id="tbody_todos_documentos_tramite">
            <tr>
                <td colspan="5">
                    <center>No hay documentos</center>
                </td>
            </tr>
            {{-- EL CONTENIDO SE CARGA CON JQUERY --}}
        </tbody>

    </table>                            
</div>


