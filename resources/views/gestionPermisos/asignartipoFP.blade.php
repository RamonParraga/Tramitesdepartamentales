
    <!-- bootstrap-daterangepicker -->
    <link href="../vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <!-- bootstrap-datetimepicker -->
    <link href="../vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    @if(session()->has('mensajePInfoAsignarTipoFP'))
        <script type="text/javascript">
            $(document).ready(function () {
                new PNotify({
                    title: 'Mensaje de Información',
                    text: '{{session('mensajePInfoAsignarTipoFP')}}',
                    type: '{{session('estadoP')}}',
                    hide: true,
                    delay: 2000,
                    styling: 'bootstrap3',
                    addclass: ''
                });
            });
        </script> 
    @endif


<form id="frm_contenedor_asignarTipo" method="POST" action="{{url('asignarTipoFPFuncionario')}}"  enctype="multipart/form-data" class="form-horizontal form-label-left" style="display:none;">
    {{csrf_field() }}


  <div class="form-group">
        <input type="text" class="hidden" name="ATFP_idusuario" id="ATFP_idusuario" value=""  required="required">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icon_gestione">Funcionario Público</label>
        <div class="col-md-7 col-sm-7 col-xs-12">
            <div class="conte_selec" style="user-select: text;">
                <label class="titulo_conten">Datos Personales</label>
                <div>
                    <label class="titulo_conten">CIU:</label>
                    <label class="texto_conten" id="ciu_seleccionado">No seleccionado</label>
                </div>
                <div>
                    <label class="titulo_conten">Cedula:</label>
                    <label class="texto_conten" id="cedula_seleccionada">No seleccionado</label>                
                </div>
                <div>
                    <label class="titulo_conten">Nombre:</label>
                    <label class="texto_conten" id="nombre_seleccionado">No seleccionado</label>                
                </div>
                <div>
                    <label class="titulo_conten">E-mail:</label>
                    <label class="texto_conten" id="email_seleccionado">No seleccionado</label>                
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio" for="icon_gestione">Tipo FP:</span>
        </label>
        <div class="col-md-7 col-sm-6 col-xs-12">
            <div class="chosen-select-conten" id="div_ATFP_tipoFP">
                <select data-placeholder="Seleccione una tipo de usuario" name="ATFP_tipoFP" id="ATFP_tipoFP" required="required" class="chosen-select form-control" tabindex="5">
                <option value=""></option>
                    @if(isset($listatipousuarioFP))
                        @foreach($listatipousuarioFP as $item)
                            <option class="opcionATFP_tipoFP" value="{{$item->idtipoFP}}">{{$item->descripcion}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>


    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio" for="icon_gestione">Departamento:</span>
        </label>
        <div class="col-md-7 col-sm-6 col-xs-12">
            <div class="chosen-select-conten">
                <select data-placeholder="Seleccione un departamento" name="ATFP_departamento" id="ATFP_departamento" required="required"   class="chosen-select form-control" tabindex="5">
                    <option value=""></option>
                    @isset($listaDepartamentos)
                        @foreach ($listaDepartamentos as $departamento)
                            <option class="opcionATFP_departamento" value="{{$departamento->iddepartamento}}">{{$departamento->nombre}}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio">Fecha de Inicio: </label>
        <div class="col-md-7 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback" >
            <input type="text" name="fecha_inicio" class="form-control has-feedback-left" id="single_cal3" placeholder="Seleccione la fecha">
            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
            <span id="inputSuccess2Status4" class="sr-only">(success)</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12 campo_obligatorio">Fecha de Fin: </label>
        <div class="col-md-7 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback" >
            <input type="text" name="fecha_fin" class="form-control has-feedback-left" id="single_cal4" value="<?php echo(date("m/d/Y", strtotime('+4 year', strtotime(date("m/d/Y"))))) ?>" placeholder="First Name">
            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
            <span id="inputSuccess2Status4" class="sr-only">(success)</span>
        </div>
    </div>
    

    {{-- INPUT CHECK PARA DEFINIR UN USUARIO COMO JEFE DE DEPARTAMETO --}}
    <div class="form-group" style="user-select: none;">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="">Rol Interno:</label>
        <div class="col-md-7 col-sm-6 col-xs-12 ">
            <div class="chosen-select-conten">
                <select name="cmb_rol_interno" id="cmb_rol_interno" class="form-control chosen-select">
                    <option value="{{encrypt('N')}}">Sin rol interno</option>
                    <option value="{{encrypt('S')}}">Secretario(a) del departamento</option>
                    <option value="{{encrypt('J')}}">Jefe del departamento</option>
                </select>
            </div>
            {{-- <label class="label_finaliar_fujo " for="check_jefe_departamento">
                <input type="checkbox" id="check_jefe_departamento" name="check_jefe_departamento" class="flat td_seleccionado"> <strong>Jefe del departamento</strong>
            </label> --}}
        </div>                               
    </div>

    <div class="form-group">
        <div class="col-md-7 col-sm-6 col-xs-12 col-md-offset-3">
            <button type="submit" class="btn btn-success"><i class="fa fa-cloud-upload"></i> Guardar</button>
            <button type="button" id="btn_gestionasignarTipoFPcancelar" class="btn btn-warning hidden"><i class="fa fa-close"></i> Cancelar</button>
        </div>
    </div>
    
</form>


{{-- TABLA QUE CONTIENE TODOS LOS TIPOS DE USUARIOS ASIGNADOS AL USUARIO SELECCIONADO --}}
<div id="div_tipoUsuariosAsignados" class="hidden form-horizontal form-label-left">
    <div class="form-group" style="margin-bottom: 5px; margin-top: 20px;">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="departamento_padre"></label>
        <div id ="" class="col-md-7 col-sm-6 col-xs-12">
            <p for="" style="float: left; margin-right: 10px; margin-bottom: 0px;">
                <i class="fa fa-align-left"></i> Tipos asignados al usuario seleccionado
            </p>
            <hr style="margin-top: 10px; margin-bottom: 0; ">
        </div>
    </div> 

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icon_gestione"></label>
        <div class="col-md-7 col-sm-6 col-xs-12">
            <div class="table-responsive div_scroll_doc_act" style="width: 100%; max-height: 250px !important;">
                <table class="table table-striped jambo_table bulk_action">
                <thead>
                    <tr class="headings">

                    <th class="column-title" rowspan="1" colspan="1" width="1" ><center>N#</center></th>
                    <th class="column-title">Tipo Usuario</th>
                    <th class="column-title">Departamento</th>
                    <th class="column-title"></th>
                    <th class="column-title" rowspan="1" colspan="1" width="1" ></th>
                    </tr>
                </thead>

                <tbody id="tb_tipoUsuarioAsignados">
                    {{-- EL CONTENIDO SE CARGA CON JQUERY AL SELECCIONAR UN USUARIO --}}
                </tbody>
                </table>
            </div>            
        </div>
    </div>
</div>


<div class="ln_solid"></div>

<div class="table-responsive">
    <div class="row">
        <div class="col-sm-12">
            <table id="datatable-keytable" class="table table-striped table-bordered dataTable no-footer table-td-th-center-vertical" role="grid" aria-describedby="datatable_info">
                <thead>
                <tr role="row">
                    <th class="sorting_desc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" aria-sort="descending" style="width: 10px;">CIU</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 10px;">Cedula</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 259px;">Funcionario Público</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 10px;">Tipo FP</th>
                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 10px;"></th>
                </tr>
                </thead>


                <tbody id="tb_listaMenu">
                    @if(isset($listaFuncionariosPublicos))
                        @foreach($listaFuncionariosPublicos as $user_tipoUsuario)
                        
                            @if($user_tipoUsuario->usuarios->estado!="E")
                                <tr role="row" class="odd">
                                    <td class="sorting_1"><center>{{$user_tipoUsuario->usuarios->ciu}}</center></td>
                                    <td class="alVertical">{{$user_tipoUsuario->usuarios->cedula}}</td>
                                    <td style="line-height: normal;">
                                        {{$user_tipoUsuario->usuarios->name}} <br>
                                        <span style="line-height: inherit; font-family: cursive;">{{$user_tipoUsuario->usuarios->email}}</span>
                                    </td>
                                    <td>                                
                                        @if (sizeof($user_tipoUsuario->usuarios->us001_tpofp)==0)
                                            NO ASIGNADO
                                        @else
                                            <ul style="margin-bottom: 0px; padding-left: 18px;">
                                                @foreach ($user_tipoUsuario->usuarios->us001_tpofp as $us001_tpofp)                                                
                                                    <li><b>{{$us001_tpofp->tipofp->descripcion}}</b> <span style="font-size: 11px; line-height: normal;">({{$us001_tpofp->departamento->nombre}})</span></li>                                                
                                                @endforeach
                                            </ul>
                                        @endif                                
                                    </td>
                                    <td class="paddingTR">
                                        <center style="min-width: 100px;">
                                            <form method="POST" class="frm_eliminar" action="{{url('registrarUsuario/'.encrypt($user_tipoUsuario->usuarios->idus001))}}"  enctype="multipart/form-data">
                                                {{csrf_field() }} <input type="hidden" name="_method" value="DELETE">                     
                                                <button type="button" onclick="asignarTipoFP_editar('{{encrypt($user_tipoUsuario->usuarios->idus001)}}')" class="btn btn-sm btn-primary marginB0"><i class="fa fa-edit"></i> Editar</button>                 
                                                <button type="button" class="btn btn-danger btn-sm marginB0" onclick="btn_eliminar(this)"><i class="fa fa-trash"></i> Eliminar</button>
                                            </form>
                                        </center>
                                    </td>
                                </tr>                                  
                            @endif
                      
                        @endforeach
                    @endif                              
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- bootstrap-daterangepicker -->
<script src="../vendors/moment/min/moment.min.js"></script>
<script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- bootstrap-datetimepicker -->    
<script src="../vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<!-- iCheck -->
<script src="../vendors/iCheck/icheck.min.js"></script>