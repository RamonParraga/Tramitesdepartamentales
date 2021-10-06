<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div class="table-responsive">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="id_tablaprioridadtramite" class="table table-striped table-bordered">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable-keytable" rowspan="1" colspan="1" aria-label="Funcionario Público: activate to sort column ascending"  aria-sort="descending">#</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Descripcion</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" >Código</th>
                                        
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending"></th>
                                    </tr>
                                </thead>
                                <tbody id="id_tablaprioridadtramite">
                                    @if(isset($listaprioridadtramite))
                                        @foreach ($listaprioridadtramite as $cont=>$listaprioridadtramite)                                        
                                            <tr role="row">
                                                <td class="sorting_1">{{ $cont+1}}</td>
                                                <td>{{ $listaprioridadtramite->descripcion}}</td>
                                                <td>{{ $listaprioridadtramite->codigo}}</td>
                                                
                                                 <td class="paddingTR" style="text-align: center; vertical-align: middle;">
                                                    <center>  
                                                    <form method="POST" class="frm_eliminar" action="{{url('prioridadtramite/gestion/'.encrypt($listaprioridadtramite->idprioridad_tramite))}}"  enctype="multipart/form-data">
                                                        {{csrf_field() }} <input type="hidden" name="_method" value="DELETE">                          
                                                        <button type="button" onclick="PrioridadTramite_editar('{{encrypt($listaprioridadtramite->idprioridad_tramite)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> Editar</button>
                                                        <button type="button" class="btn btn-sm btn-danger marginB0" onclick="btn_eliminar_prioridadtramite(this)"><i class="fa fa-trash"></i> Eliminar</button>
                                                        </form>                    
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


