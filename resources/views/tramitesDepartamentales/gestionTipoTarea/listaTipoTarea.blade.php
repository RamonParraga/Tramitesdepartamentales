<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div class="table-responsive">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="id_tablatipotarea" class="table table-striped table-bordered">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable-keytable" rowspan="1" colspan="1" aria-label="Funcionario Público: activate to sort column ascending"  aria-sort="descending">#</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Descripcion</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" >Estado</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending"></th>
                                        
                                    </tr>
                                </thead>
                                <tbody id="id_tablatipotarea">
                                    @if(isset($listatipotarea))
                                        @foreach ($listatipotarea as $cont=>$tipotarea)                                        
                                            <tr role="row">
                                                <td class="sorting_1">{{ $cont+1}}</td>
                                                <td>{{ $tipotarea->descripcion}}</td>
                                                <td>{{ $tipotarea->estado}}</td>
                                                <td class="paddingTR">
                                                    <center>                            
                                                        <button type="button" onclick="TipoTarea_editar('{{encrypt($tipotarea->idtipo_tarea)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> Editar</button>                 
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


