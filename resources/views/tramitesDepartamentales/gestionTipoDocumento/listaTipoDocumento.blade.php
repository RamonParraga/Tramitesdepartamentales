<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div class="table-responsive">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="id_tablatipodocumento" class="table table-striped table-bordered">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable-keytable" rowspan="1" colspan="1" aria-label="Funcionario Público: activate to sort column ascending"  aria-sort="descending">#</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Descripción</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" >Abreviación</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending">Estructura</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending">Secuencia</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending">Prioridad</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending"></th>
                                    </tr>
                                </thead>
                                <tbody id="id_tablatipodocumento">
                                    @if(isset($listatipodocumento))
                                        @foreach ($listatipodocumento as $cont=>$listatipodocumento)                                        
                                            <tr role="row">
                                                <td class="sorting_1">{{ $cont+1}}</td>
                                                <td>{{ $listatipodocumento->descripcion}}</td>
                                                <td>{{ $listatipodocumento->abreviacion}}</td>
                                                <td>{{ $listatipodocumento->estructura}}</td>
                                                <td>{{ $listatipodocumento->secuencia}}</td>
                                                <td>@if($listatipodocumento->prioridad =="0")Anexo  
                                                    @elseif ($listatipodocumento->prioridad =="1") Principal @endif </td>
                                                 <td class="paddingTR" style="text-align: center; vertical-align: middle;">
                                                    <center>                            
                                                        <button type="button" onclick="TipoDocumento_editar('{{encrypt($listatipodocumento->idtipo_documento)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> </button>

                                                         <button type="button" onclick="TipoDocumento_eliminar('{{encrypt($listatipodocumento->idtipo_documento)}}')" class="btn btn-sm btn-danger marginB0"><i class="fa fa-trash"></i> </button>                    
                                                    </center>

                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif                              
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>                        
        </div>
    </div>
</div>


