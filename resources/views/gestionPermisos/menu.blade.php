
    <form id="frm_menu" method="POST" action="{{url('gestionMenu')}}"  enctype="multipart/form-data"  class="form-horizontal form-label-left">
        {{csrf_field() }}
        <input id="method_menu" type="hidden" name="_method" value="POST">
        


        @if(session()->has('mensajePInfoMenu'))
            <script type="text/javascript">
                $(document).ready(function () {
                    new PNotify({
                        title: 'Mensaje de Información',
                        text: '{{session('mensajePInfoMenu')}}',
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
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre_menu">Nombre del Menu <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="nombre_menu" name="nombre_menu" placeholder="Menú" required="required" class="form-control col-md-7 col-xs-12">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icon_menu">Icono<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="icon_menu" name="icon_menu" class="inputIconoSeleccionado soloinfo form-control col-md-12 col-xs-12" required="required" placeholder="Ejemplo fa fa-circle-o" value="fa fa-circle-o" >
                <button type="button" id="icon_menu_btn" class="buttonIconoSeleccionado btn btn-primary"><i class="fa fa-circle-o"></i></button>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="submit" class="btn btn-success"><i class="fa fa-cloud-upload"></i> Guardar</button>
                <button type="button" id="btn_gestionmenucancelar" class="btn btn-warning hidden"><i class="fa fa-close"></i> Cancelar</button>
            </div>
        </div>
        <div class="ln_solid"></div>

    </form>

    <div class="table-responsive">
        <div class="row">
            <div class="col-sm-12">
                <table id="idtablamenu" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                    <thead>
                        <tr role="row">
                            <th class="sorting_desc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" aria-sort="descending" style="width: 10px;">Icono</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 259px;">Lista de Menú</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 10px;"></th>
                        </tr>
                    </thead>

                    <tbody id="tb_listaMenu">
                        @if(isset($listaMenu))
                            @foreach($listaMenu as $item)
                                <tr role="row" class="odd">
                                    <td class="sorting_1"><center><i class="{{$item->icono}}"></i></center></td>
                                    <td>{{$item->nombremenu}}</td>
                                    <td class="paddingTR">
                                        <center>
                                        <form method="POST" class="frm_eliminar" action="{{url('gestionMenu/'.encrypt($item->idmenu))}}"  enctype="multipart/form-data">
                                            {{csrf_field() }} <input type="hidden" name="_method" value="DELETE">
                                            <button type="button" onclick="menu_editar('{{encrypt($item->idmenu)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> Editar</button>
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


