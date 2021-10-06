<form id="frm_asignarGestion" method="POST" action="{{url('asignarGestionTipo')}}"  enctype="multipart/form-data" class="form-horizontal form-label-left">
    {{csrf_field() }}
    <input id="method_asignarGestion" type="hidden" name="_method" value="POST">


    @if(session()->has('mensajePInfoAsignarGesion'))
        <script type="text/javascript">
            $(document).ready(function () {
                new PNotify({
                    title: 'Mensaje de Información',
                    text: '{{session('mensajePInfoAsignarGesion')}}',
                    type: '{{session('estadoP')}}',
                    hide: true,
                    delay: 2000,
                    styling: 'bootstrap3',
                    addclass: ''
                });
            });
        </script> 
    @endif



    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icon_gestione">Tipo Usuario<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="chosen-select-conten">
                <select data-placeholder="Seleccione una tipo de usuario" name="AGTFP_tipousuario" id="AGTFP_tipousuario" required="required" class="chosen-select form-control" tabindex="5">
                <option value=""></option>
                    @if(isset($listatipousuarioFP))
                        @foreach($listatipousuarioFP as $item)
                            <option class="opcion_tipoFP" value="{{$item->idtipoFP}}">{{$item->descripcion}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icon_gestione">Gestión<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="chosen-select-conten">
                <select data-placeholder="Seleccione una gestión" name="AGTFP_gestion" id="AGTFP_gestion" required="required" class="chosen-select form-control" tabindex="5">
                <option value=""></option>
                @if(isset($listaMenuGestion))
                    @foreach($listaMenuGestion as $menu)
                    <optgroup label="{{$menu->nombremenu}}">
                        @foreach($menu->gestion as $gestion)
                        <option class="opcion_gestion" value="{{$gestion->idgestion}}">{{$gestion->nombregestion}}</option>
                        @endforeach
                    <optgroup>
                    @endforeach
                @endif   
                </select>
            </div>
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
            <button type="submit" class="btn btn-success"><i class="fa fa-cloud-upload"></i> Guardar</button>
            <button type="button" id="btn_gestionasignargestioncancelar" class="btn btn-warning hidden"><i class="fa fa-close"></i> Cancelar</button>
        </div>
    </div>
</form>
<div class="ln_solid"></div>

<div class="table-responsive">
    <div class="row">
        <div class="col-sm-12">
            <table id="datatable-fixed-header" class="table table-striped table-bordered">
                <thead>
                <tr role="row">
                    <th class="sorting_desc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" aria-sort="descending" style="width: 157px;">Tipo FP</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 259px;">Gestiones Asignadas</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 117px;"></th>
                </tr>
                </thead>

                <tbody>
                    @if(isset($listaGestionesAsignadas))
                        @foreach($listaGestionesAsignadas  as $t=> $tipo)
                        @foreach($tipo->tipoFPGestion as $g=> $gestion)   
                            <tr role="row" class="odd">
                                <!-- <td @if($g==0) rowspan="{{sizeof($tipo->tipoFPGestion)}}" @else hidden @endif class="sorting_1">{{$tipo->descripcion}}</td> -->
                                @if($g==0)
                                    <td class="sorting_1 td_primario @if($t%2!=0) td_fondo_p @else  td_fondo_s @endif">{{$tipo->descripcion}}</td>
                                @else 
                                    <td class="sorting_1 td_secundario @if($t%2!=0) td_fondo_p @else  td_fondo_s @endif">{{$tipo->descripcion}}</td>
                                @endif
                                <td><i class="{{$gestion->gestion->icono}}"></i> {{$gestion->gestion->nombregestion}}</td>
                                <td class="paddingTR">
                                    <center>
                                        <form method="POST" class="frm_eliminar" action="{{url('asignarGestionTipo/'.encrypt($gestion->idtipoFP_gestion))}}"  enctype="multipart/form-data">
                                            {{csrf_field() }} <input type="hidden" name="_method" value="DELETE">
                                            <button type="button" onclick="asignarGestion_editar('{{encrypt($gestion->idtipoFP_gestion)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> Editar</button>
                                            <button type="button" class="btn btn-sm btn-danger marginB0" onclick="btn_eliminar(this)"><i class="fa fa-trash"></i> Eliminar</button>
                                        </form>
                                    </center>
                                </td>
                            </tr>
                        @endforeach
                        @endforeach
                    @endif           
                </tbody>
            </table>
        </div>
    </div>
</div>
