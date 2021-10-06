 {{-- INPUT PARA GUARDAR LOS DATOS DE TODOS LOS DEPARTAMENTOS --}}
 <input type="hidden" id="input_listaDepartamentos" data-field-id="">
    
 <script type="text/javascript">
     var m_timer = null;


     function dibujarGraficoFlujo(listaDepartamentos){

        console.clear();
        console.log(listaDepartamentos);

        var tipoSelccionado = $("#gf_select_tipo_tramite option:selected").html();
        $("#tittleTipoTramite").html(tipoSelccionado);
  
        ResizePlaceholder();
        $("#contenedor_grafico_flujo").hide();

        var options = new primitives.orgdiagram.Config(); 
        var items = []; // arreglo de departamentos a dibujar

        // console.log(listaDepartamentos);

        $.each(listaDepartamentos, function (index, nodoFlujo){ 
            //mostramos el contenedor del grafico del flujo
            // $("#contenedor_grafico_flujo").removeClass("hidden"); // quitamos la clase que oculta el div
            $("#contenedor_grafico_flujo").show();

            
            var Depa_parend = null;
            var Titulo = "INICIO";
            var TituloColor = "Blue";
            var tipo_envio = "Para"; // vara especificar si es un envio directo o solo una copia
            var color_content_left = primitives.common.Colors.Green; // por defecto

            //verificamos si se esta enviando una copia
            if(nodoFlujo.tipo_envio=="C"){
                tipo_envio="Copia";
                color_content_left=primitives.common.Colors.LightGray;
            }

            // validamos por si el departamento padre no es nulo (osea si no es el primer nodo del flujo)
            if(nodoFlujo.flujo_padre!=null){
                Depa_parend = nodoFlujo.flujo_padre.idflujo;
                Titulo=""; // no mostramos nada
                TituloColor = "#F8F8F8";
            }else{ // si es el primero del nodo
                tipo_envio="";
            }

            // validamos por si el departamento es el ultimo del nodo (Donde finaliza el flujo)
            if(nodoFlujo.estado_finalizar==1){
                Depa_parend = nodoFlujo.flujo_padre.idflujo;
                Titulo="FINAL"; // no mostramos nada
                TituloColor = "Blue";
                tipo_envio="";
            }
    
            var flujoInfo = {
                tipo_envio: nodoFlujo.tipo_envio, 
                estado_finalizar: nodoFlujo.estado_finalizar,
                iddepartamento: nodoFlujo.departamento.iddepartamento,
                nombreDepartamento: nodoFlujo.departamento.nombre
            };
            // console.log(flujoInfo);

            items.push(new primitives.orgdiagram.ItemConfig({
                id: nodoFlujo.idflujo,
                dataId:JSON.stringify(flujoInfo),
                parent: Depa_parend,
                title:Titulo,
                description: nodoFlujo.departamento.nombre,
                groupTitle: tipo_envio,
                itemTitleColor: TituloColor,
                groupTitleColor: color_content_left
            }));

        });
        

        options.itemTitleFirstFontColor = primitives.common.Colors.White; // poner color a las letras del primer Titulo de todos
        options.pageFitMode = primitives.common.PageFitMode.None;
        options.items = items;
        options.cursorItem = 0;
        options.arrowsDirection = primitives.common.GroupByType.Children; // flechitas en las lineas

        jQuery("#graficoFlujo").orgDiagram(options);

        $(window).resize(function () {
            onWindowResize();
        });
    
     }

     function onWindowResize() {
         if (m_timer == null) {
             m_timer = window.setTimeout(function () {
                 ResizePlaceholder();
                 jQuery("#graficoFlujo").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
                 window.clearTimeout(m_timer);
                 m_timer = null;
             }, 300);
         }
     }

     function ResizePlaceholder() {
         //var bodyWidth = Math.round($(window).width() - 40);
         var bodyWidth ='100%';
        // var bodyHeight='100%';
         var bodyHeight = Math.round($(window).height());
         jQuery("#graficoFlujo").css(
             {
                 "width": bodyWidth + "px",
                 "height": bodyHeight + "px"
             });
     }

 </script>
