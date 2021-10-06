<form id="frm_tipoFP" method="POST" action="{{url('gestionTipoFP')}}"  enctype="multipart/form-data"  class="form-horizontal form-label-left">
    {{csrf_field() }}
    <input id="method_tipoFP" type="hidden" name="_method" value="POST">


    @if(session()->has('mensajePInfoTipoFP'))
        <script type="text/javascript">
            $(document).ready(function () {
                new PNotify({
                    title: 'Mensaje de Información',
                    text: '{{session('mensajePInfoTipoFP')}}',
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
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tipoFP">Tipo Funcionario Público<span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input type="text" id="nombre_tipoFP" name="nombre_tipoFP" required="required" class="form-control col-md-7 col-xs-12">
        </div>
    </div>



    <div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
            <button type="submit" class="btn btn-success"><i class="fa fa-cloud-upload"></i> Guardar</button>
            <button type="button" id="btn_gestiontipoFPcancelar" class="btn btn-warning hidden"><i class="fa fa-close"></i> Cancelar</button>
        </div>
    </div>
    <div class="ln_solid"></div>

</form>

<div class="table-responsive">
    <div class="row">
        <div class="col-sm-12">
            <table id="datatable-buttons" class="table table-striped table-bordered">
                <thead>
                <tr role="row">
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 259px;">Tipo de Funcionario Públicos</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 117px;"></th>
                </tr>
                </thead>


                <tbody>
                
                @if(isset($listatipousuarioFP))
                    @foreach($listatipousuarioFP as $item)
                        <tr role="row" class="odd">
                            <td>{{$item->descripcion}}</td>
                            <td class="paddingTR">
                                <center>
                                <form method="POST" class="frm_eliminar" action="{{url('gestionTipoFP/'.encrypt($item->idtipoFP))}}"  enctype="multipart/form-data">
                                    {{csrf_field() }} <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" onclick="tipoFP_editar('{{encrypt($item->idtipoFP)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> Editar</button>
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