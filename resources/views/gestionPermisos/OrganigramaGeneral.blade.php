
<div class="table-responsive">
    <div class="row">
        <div class="col-sm-12">
            <table id="datatable-responsive" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                <thead>
                <tr role="row">
                    <th class="sorting_desc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" aria-sort="descending" style="width: 10px;">Nombre del Menú</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 259px;">Lista de gestiones asignadas al menú</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 259px;">Tipos FP asignados</th>
                </tr>
                </thead>


                <tbody id="tb_listaMenu">

                    @if(isset($listaMenuGestion))
                        @foreach($listaMenuGestion  as $m=> $menu)
                        @foreach($menu->gestion as $g=> $gestion)   
                            <tr role="row" class="odd">
                                @if($g==0)
                                    <td class="sorting_1 td_primario @if($m%2==0) td_fondo_p @else  td_fondo_s @endif"><i class="{{$menu->icono}}"></i> {{$menu->nombremenu}}</td>
                                @else 
                                    <td class="sorting_1 td_secundario @if($m%2==0) td_fondo_p @else  td_fondo_s @endif"><i class="{{$menu->icono}}"></i> {{$menu->nombremenu}}</td>
                                @endif
                                <td><i class="{{$gestion->icono}}"></i> {{$gestion->nombregestion}}</td>
                                <td>
                                    <ul style="margin-bottom: 0px;">
                                        @foreach($gestion->TipoFPGestion as $tipoFPGestion)
                                        <li>{{$tipoFPGestion->tipoFP->descripcion}}</li>
                                        @endforeach
                                    </ul>
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