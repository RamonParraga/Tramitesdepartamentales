<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div class="table-responsive">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="id_tablaestructuradocumento" class="table table-striped table-bordered">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable-keytable" rowspan="1" colspan="1" aria-label="Funcionario Público: activate to sort column ascending"  aria-sort="descending">#</th>
                                        <th class="sorting" rowspan="1" colspan="1">Año</th>
                                        <th class="sorting" rowspan="1" colspan="1">Departamento</th>
                                        <th class="sorting" rowspan="1" colspan="1">Abrev. Departamento</th>
                                        <th class="sorting" rowspan="1" colspan="1">Tipo Documento</th>
                                        <th class="sorting" rowspan="1" colspan="1">Abrev. Tipo Documento</th>
                                        <th class="sorting" rowspan="1" colspan="1">Estructura</th>  
                                        <th class="sorting" rowspan="1" colspan="1" style="width: 10px"></th>
                                        <th style=""></th>
                                    </tr>
                                </thead>
                                <tbody id="id_estructuradocumento">
                                    @if(isset($estructuradocumento))
                                        @foreach ($estructuradocumento as $cont=>$estructuradocumento)                                        
                                            <tr role="row">
                                                <td class="sorting_1">{{ $cont+1}}</td>
                                                <td>{{ $estructuradocumento->anio}}</td>
                                                <td>{{ $estructuradocumento->nombredepartamento}}</td>
                                                <td>{{ $estructuradocumento->abreviaciondepartamento}}</td>
                                                <td>{{ $estructuradocumento->descripciontipodocumento}}</td>
                                                <td>{{ $estructuradocumento->abreviaciontipodocumento}}</td>
                                                <td>{{ $estructuradocumento->gad.'-'.$estructuradocumento->abreviaciondepartamento .'-'.$estructuradocumento->anio.'-'.$estructuradocumento->secuencia_tipodocumento.'-'.$estructuradocumento->abreviaciontipodocumento  .'-'. $estructuradocumento->secuencia_estructuradocumento }}</td>
                                                <td style="text-align: center; vertical-align: middle; padding: 5px 8px;">
                                                    <button type="button" onclick="codigoDocEditar('{{encrypt($estructuradocumento->idestructura_documento)}}')" class="btn btn-sm btn-primary btn_lg" style="margin-bottom: 0px; width: 35px;"><i class="fa fa-edit"></i> </button>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle; padding: 5px 8px;">
                                                    <center>  
                                                        <form method="POST" class="frm_eliminar" action="{{url('estructuradocumento/gestion/'.encrypt($estructuradocumento->idestructura_documento))}}"  enctype="multipart/form-data">
                                                            {{csrf_field() }} <input type="hidden" name="_method" value="DELETE">                                                            
                                                            <button type="button"  onclick="btn_eliminar(this)" class="btn btn_lg btn-danger" style="margin-bottom: 0px; width: 35px;"><i class="fa fa-trash"></i> </button>  
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


