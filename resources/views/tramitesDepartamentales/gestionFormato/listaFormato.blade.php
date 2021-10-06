<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <br>
                <td class="paddingTR">
                    <center>                            
                        <button type="button" onclick="verFormato()" class="btn btn-sm btn-primary marginB0"><i class="fa fa-eye"></i> Vista previa</button>                 
                    </center>
                </td>
                <br>
                <div style="overflow-x: auto !important;">
                    <div class="row">
                        <div class="col-sm-12">
                            
                            <table class="table table-bordered table-td-th-center-vertical">
                                <thead>
                                    <tr role="row">                                       
                                        <th class="sorting" rowspan="1" colspan="1" style="width: 10px;"><center>USO</center></th>                            
                                        <th class="sorting" rowspan="1" colspan="1" >IMAGEN AGREGADA</th>                                        
                                    </tr>
                                </thead>
                                <tbody id="id_tablaformato">                                                                                                             
                                    <tr role="row"> 
                                        <td><strong><center>CABECERA</center></strong></td>                 
                                        <td> <img style="width: 500px;" src="{{$formatoDocumento->cabecera}}" alt=""></td>
                                    </tr>   
                                    <tr role="row">   
                                        <td><strong><center>PIE</center></strong></td>                 
                                        <td> <img style="width: 500px;" src="{{$formatoDocumento->pie}}" alt=""></td>
                                    </tr>                     
                                </tbody>
                            </table>            
                        </div>
                    </div>
                </div>

            </div>                        
        </div>
    </div>
</div>


