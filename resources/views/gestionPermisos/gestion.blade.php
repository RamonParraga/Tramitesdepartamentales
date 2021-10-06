
<form id="frm_gestion" method="POST" action="{{url('gestionGestion')}}"  enctype="multipart/form-data" class="form-horizontal form-label-left">
    {{csrf_field() }}
    <input id="method_gestion" type="hidden" name="_method" value="POST">


    @if(session()->has('mensajePInfoGestion'))
        <script type="text/javascript">
            $(document).ready(function () {
                new PNotify({
                    title: 'Mensaje de Información',
                    text: '{{session('mensajePInfoGestion')}}',
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
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre_gestion">Nombre de la Gestión<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input type="text" id="nombre_gestion" name="nombre_gestion"  placeholder="Ingrese el nombre de la gestión" required="required" class="form-control col-md-7 col-xs-12">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ruta_gestion">Ruta<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input type="text" id="ruta_gestion" name="ruta_gestion" required="required" placeholder="Ejemplo rutaejemplo" class="form-control col-md-7 col-xs-12">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icono_gestione">Icono<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input type="text" id="icon_gestion" name="icono_gestione" class="inputIconoSeleccionado form-control col-md-12 col-xs-12" required="required" placeholder="Ejemplo fa fa-circle-o" value="fa fa-circle-o">
            <button type="button" id="icono_gestione_btn" class="buttonIconoSeleccionado btn btn-primary"><i class="fa fa-circle-o"></i></button>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icono_gestione">Menú<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="chosen-select-conten">
                <select data-placeholder="Seleccione un menú"  name="gestion_selec_menu" id="gestion_selec_menu" required="required" class="chosen-select form-control" tabindex="5">
                    <option value=""></option>
                    @if(isset($listaMenu))
                        @foreach($listaMenu as $item) 
                            <option class="gestion_selec_menu" value="{{$item->idmenu}}">{{$item->nombremenu}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
            <button type="submit" class="btn btn-success"><i class="fa fa-cloud-upload"></i> Guardar</button>
            <button type="button" id="btn_gestiongestioncancelar" class="btn btn-warning hidden"><i class="fa fa-close"></i> Cancelar</button>
        </div>
    </div>
    <div class="ln_solid"></div>

</form>



<div class="table-responsive">
    <div class="row">
    <div class="col-sm-12">
            <table id="datatable-checkbox" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                <thead>
                <tr role="row">
                    <th class="sorting_desc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" aria-sort="descending">Icono</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Lista de Gestiones</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Ruta</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" >Menú</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" ></th>
                </tr>
                </thead>


                <tbody>
                    @if(isset($listagestion))
                        @foreach($listagestion as $item) 
                            <tr role="row" class="odd">
                                <td class="sorting_1"><center><i class="{{$item->icono}}"></i></center></td>
                                <td>{{$item->nombregestion}}</td>
                                <td>/{{$item->ruta}}</td>
                                <td>{{$item->nombremenu}}</td>                
                                <td class="paddingTR">
                                    <center>
                                    <form method="POST" class="frm_eliminar" action="{{url('gestionGestion/'.encrypt($item->idgestion))}}"  enctype="multipart/form-data">
                                        {{csrf_field() }} <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" onclick="gestion_editar('{{encrypt($item->idgestion)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> Editar</button>
                                        <button type="button" class="btn btn-sm btn-danger marginB0" onclick="btn_eliminar(this)"><i class="fa fa-trash"></i> Eliminar</button>
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

<!-- <br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> -->


