   
   <div id="content_DeParaInteresado" class="contentDePara" style="border: 1px solid #48aecd !important; border-radius: 3px;">

        <ul class="list-unstyled timeline">
            <li class="list-unstyled-li">
                <div class="block">
                    <div class="tags">
                        <a class="tag">
                        <span><strong>De</strong></span>
                        </a>
                    </div>
                    <div id="div_conteUsuarioOrigen">

                        <div class="block_content depEnviar_content">                                                                                                                        
                            <h2 class="title">
                                <i class="fa fa-user iconoTittle"></i>
                                <p> @guest
                                        No logueado
                                    @else
                                        {{-- {{ Auth::user()->name }}  --}}
                                        {{$jefeDepartamento->name}}
                                        <span class="labelInfoUser">/</span>
                                        <span class="labelInfoUser"><i class="fa fa-group"></i> {{departamentoLogueado()["tipoFP"]}}</span>
                                    @endguest
                                </p>                    
                            </h2>       
                        </div>                             

                    </div>

                </div>
            </li>
        </ul>

        {{--  AQUI AGREGAMOS LOS DEPARTAMENTOS A LOS QUE SERÁ ENVIADO EL TRAMITE --}}
        <ul class="list-unstyled timeline hidden" id="areaGeneralDepartamentoAgregados">
            <li class="list-unstyled-li">
                <div class="block">
                    <div class="tags">
                        <a class="tag">
                        <span><strong>Para</strong></span>
                        </a>
                    </div>
                    <div id="div_conteDepEnviar">

                    </div>

                </div>
            </li>
        </ul>
        {{--  /AQUI AGREGAMOS LOS DEPARTAMENTOS QUE A LOS QUE SERA ENVIADO EL TRAMITE --}}


        {{--  AQUI AGREGAMOS DONDE SERÁN ENVIADAS COPIAS DEL TRAMITE --}}
        <ul class="list-unstyled timeline hidden" id="areaGeneralDepartamentoCopias">
            <li class="list-unstyled-li">
                <div class="block">
                    <div class="tags">
                        <a class="tag">
                        <span><strong>Copia</strong></span>
                        </a>
                    </div>
                    <div id="div_conteCopiaEnviar">
                        {{-- AQUI AGREGAMOS DONDE SERÁN ENVIADAS COPIAS DEL TRAMITE --}}
                    </div>

                </div>
            </li>
        </ul>
        {{--  /AQUI AGREGAMOS LOS DEPARTAMENTOS QUE A LOS QUE SERA ENVIADO EL TRAMITE --}}


        {{--  AQUI AGREGAMOS LOS INTERESADOS DEL TRAMITE --}}
        <ul class="list-unstyled timeline hidden" id="areaGeneralInteresadosAgregados">
            <li class="list-unstyled-li">
                <div class="block">
                    <div class="tags">
                        <a class="tag">
                        <span><strong>Interes</strong></span>
                        </a>
                    </div>
                    <div id="div_conteInteresados">

                    </div>

                </div>
            </li>
        </ul>
        {{--  /AQUI AGREGAMOS LOS INTERESADOS DEL TRAMITE --}}

   </div>


   <div class="form-group">
        <label for="gt_numReferencia" class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12">Nº Referencia</label>
        <div class="col-md-10 col-sm-10 col-xs-12 colpr0">
            <input disabled="true" type="text" name="gt_numReferencia" id="gt_numReferencia" value="" class="date-picker form-control col-md-7 col-xs-12 sinespecial" required="required" style="text-transform: uppercase;">
            <span class="sinespecialMsj"></span>
        </div>
    </div>
   
    <div class="form-group">
        <label for="gt_asunto" class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12">Asunto<span class="required">*</span></label>
        <div class="col-md-10 col-sm-10 col-xs-12 colpr0">
            <input type="text" name="gt_asunto" id="gt_asunto" value="" placeholder="Ingrese el asunto del támite" class="date-picker form-control col-md-7 col-xs-12 sinespecial" required="required" style="text-transform: uppercase;">
            <span class="sinespecialMsj"></span>
        </div>
    </div>

    <div class="form-group">
        <label for="gt_observaciones" class="control-label alignTextLeft col-md-2 col-sm-2 col-xs-12">Observaciones<span class="required">*</span></label>
        <div class="col-md-10 col-sm-10 col-xs-12 colpr0">
            <textarea type="text" name="gt_observaciones" id="gt_observaciones" placeholder="Ingrese la observación del trámite" rows="3" class="date-picker form-control col-md-7 col-xs-12 sinespecial" required="required" style="text-transform: uppercase;"></textarea>
            <span class="sinespecialMsj"></span>            
        </div>
    </div>

    
