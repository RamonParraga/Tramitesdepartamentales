<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div class="table-responsive">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="id_tablabodega" class="table table-striped table-bordered">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable-keytable" rowspan="1" colspan="1" aria-label="Funcionario Público: activate to sort column ascending"  aria-sort="descending">#</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Nombre</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" >Tipo</th>

                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Área</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" >Ubicación</th>
                                        
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending"></th>
                                    </tr>
                                </thead>
                                <tbody id="id_tablaprioridadtramite">
                                    @if(isset($listaBodega))
                                        @foreach ($listaBodega as $cont=>$listabodega)                                        
                                            <tr role="row">
                                                <td class="sorting_1">{{ $cont+1}}</td>
                                                <td>{{ $listabodega->nombre}}</td>
                                                <td>{{ $listabodega->tipo}}</td>
                                                <td>{{ $listabodega->departamento['nombre']}}</td>      
                                                <td>{{ $listabodega->ubicacion}}</td>
                                                
                                                 <td class="paddingTR" style="text-align: center; vertical-align: middle;">
                                                    <center>  
                                                    <form method="POST" class="frm_eliminar" action="{{url('bodega/gestion/'.encrypt($listabodega->id_bodega))}}"  enctype="multipart/form-data">
                                                            {{csrf_field() }} <input type="hidden" name="_method" value="DELETE">                          
                                                        <button type="button" onclick="Bodega_editar('{{encrypt($listabodega->id_bodega)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> </button>

                                                         {{-- <button type="button" onclick="btn_eliminar_prioridadtramite('{{encrypt($listaprioridadtramite->idprioridad_tramite)}}')" class="btn btn-sm btn-danger marginB0"><i class="fa fa-trash"></i> </button> --}}

                                                         <button type="button" class="btn btn-sm btn-danger marginB0" onclick="btn_eliminar_bodega(this)"><i class="fa fa-trash"></i></button>
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


