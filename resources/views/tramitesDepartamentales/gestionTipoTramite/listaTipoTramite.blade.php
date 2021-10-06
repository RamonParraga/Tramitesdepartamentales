<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div class="table-responsive">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="id_tablatipotramite" class="table table-striped table-bordered">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable-keytable" rowspan="1" colspan="1" aria-label="Funcionario Público: activate to sort column ascending"  aria-sort="descending">Tramite</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Descripcion</th>                                       
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" >Tramite global</th>                    
                                        <th class="sorting" tabindex="0" aria-controls="datatable-fixed-header" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending"></th>                                        
                                    </tr>
                                </thead>
                                <tbody id="tb_listatipotramite">
                                    @if(isset($tipotramite))
                                        @foreach ($tipotramite as $tipotramite)                                        
                                            <tr role="row">
                                                <td class="sorting_1">{{ $tipotramite->tipo}}</td>
                                                <td>{{ $tipotramite->descripcion}}</td>                                               
                                                
                                                @if($tipotramite->tramite_global=='1')
                                                    <td>
                                                        <ul style="margin-bottom: 0px; padding-left: 18px;">
                                                            <li>{{$tipotramite->tramite_global='TODOS'}}</li>
                                                        </ul>
                                                    </td>
                                                @endif

                                                @if($tipotramite->tramite_global=='0')
                                                    <td>
                                                        <ul style="margin-bottom: 0px; padding-left: 18px;">                                                
                                                            @foreach($tipotramite['tipotramite_departamento'] as $key => $detalle)
                                                                <li>{{$detalle->departamentotramite->nombre}}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>                                                                                                
                                                @endif
                                                <td class="paddingTR" style="text-align: center; vertical-align: middle;">
                                                    <center>                            
                                                        <button type="button" onclick="TipoTramite_editar('{{encrypt($tipotramite->idtipo_tramite)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> </button>
                                                        <button type="button" onclick="TipoTramite_eliminar('{{encrypt($tipotramite->idtipo_tramite)}}')" class="btn btn-sm btn-danger marginB0"><i class="fa fa-trash"></i> </button>                                                                                                              
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


