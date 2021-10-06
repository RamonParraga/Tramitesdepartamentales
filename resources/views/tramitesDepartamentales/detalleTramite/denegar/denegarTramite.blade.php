@extends('layouts.service')
@section('contenido')

    @isset($detalle_tramite)

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 10px;">
                <div class="x_panel" style="border-radius: 4px;">
                    <div class="x_title">
                        <h2> <b><i class="fa fa-edit"></i> Denegar Trámite</b> || {{$detalle_tramite->tramite->tipo_tramite->descripcion}} || {{$detalle_tramite->tramite->codTramite}}</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li style="float: right;"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="margin-top:0">
                        
                        <form id="frm_registrarDenegacion" action="{{url('detalleTramite/registrarDenegacion/'.encrypt($detalle_tramite->iddetalle_tramite))}}" method="POST"  enctype="multipart/form-data" autocomplete="off" class="form-horizontal form-label-left">
                            {{ csrf_field() }}
                            <input id="method_flujo" type="hidden" name="_method" value="POST">



                                <div class="form-group">                
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <button type="submit" class="btn btn-danger"> <i class="fa fa-suitcase"></i> Denegar el Trámite</button>
                                        <br>               
                                    </div>
                                </div>

                                <hr style="margin-top: 18px; margin-bottom: 10px;">

                                <div class="form-group">
                                    <label for="" class="col-md-12 col-sm-12 col-xs-12"> Ingrese un detalle</label>        
                                    <div class="col-md-12 col-sm-12 col-xs-12 ">
                                        <textarea type="text" id="detalle_denegacion" name="detalle_denegacion" placeholder="Ingrese una conclusión informativa sobre la denegación del trámite" rows="3" class="date-picker form-control col-md-7 col-xs-12 sinespecial" required="required" style="text-transform: uppercase;"></textarea>
                                        <span class="sinespecialMsj"></span>
                                    </div>
                                </div>

                                
                                <div role="tabpanel" data-example-id="togglable-tabs">
                                    <ul id="myTab" class="nav nav-tabs bar_tabs  nav_tabs_tramite" role="tablist" style="margin-top: 5px; margin-right: 20px;">
                                        <li role="presentation" class="active first_li">
                                            <a href="#tab_adjuntarDocumento" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true"><b>Adjuntar documentos</b></a>
                                        </li>
                                    </ul>
                                    <div id="myTabContent" class="tab-content">
                                        <div role="tabpanel" class="tab-pane fade active in" id="tab_adjuntarDocumento" aria-labelledby="home-tab">
                                            @include('tramitesDepartamentales.detalleTramite.denegar.adjuntarDocumentoDenegar')        
                                        </div>
                                    </div>
                                </div>
                                

                        </form>
                        
                    </div>
                </div>
            </div>
        </div>    
        
    @endisset

    <script src="{{asset('js/TramiteDepartamental/tramites/denegarTramite.js')}}"></script>


@endsection