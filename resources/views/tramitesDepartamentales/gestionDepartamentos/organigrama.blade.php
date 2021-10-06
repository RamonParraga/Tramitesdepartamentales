
    {{-- INPUT PARA GUARDAR LOS DATOS DE TODOS LOS DEPARTAMENTOS --}}
    <input type="hidden" id="input_listaDepartamentos" data-field-id="{{$listaDepartamentos}}">
    
    <script type="text/javascript">
        var m_timer = null;

        jQuery(document).ready(function () {
            // lista de departamentos para graficar en el organigrama
            var listaDepartamentos =  $('#input_listaDepartamentos').data("field-id");
            dibujarOrgamigrama(listaDepartamentos,"",true);
        });

        function dibujarOrgamigrama(listaDepartamentos,contenedor,filtrar){
            ResizePlaceholder();

            var options = new primitives.orgdiagram.Config(); 
            var items = []; // arreglo de departamentos a dibujar
            var colorTitle = "Blue"; //orange los sin jefe

            $.each(listaDepartamentos, function (index, departamento) { 
           
                // verificamos si el departamento tiene jefe o no
                if(departamento.jefe_departamento.length>=1){//con jefe
                    colorTitle = "Blue";
                }else{ // sin jefe
                    colorTitle = "Orange";
                }

                if(filtrar && departamento.periodo.estado!="A"){ return; }
                items.push(new primitives.orgdiagram.ItemConfig({
                    id: departamento.iddepartamento,
                    parent: departamento.iddepartamento_padre,
                    title: departamento.abreviacion,
                    description: departamento.nombre,
                    groupTitle: "Depart",
                    itemTitleColor: colorTitle,
                    groupTitleColor: primitives.common.Colors.LightGray
                }));
            });

            options.itemTitleFirstFontColor = primitives.common.Colors.White; // poner color a las letras del primer Titulo de todos
            options.pageFitMode = primitives.common.PageFitMode.None;
            options.items = items;
            options.cursorItem = 0;
            options.arrowsDirection = primitives.common.GroupByType.Children; // flechitas en las lineas

            jQuery("#orgdiagram").orgDiagram(options);

            $(window).resize(function () {
                onWindowResize();
            });
        }

        function onWindowResize() {
            if (m_timer == null) {
                m_timer = window.setTimeout(function () {
                    ResizePlaceholder();
                    jQuery("#orgdiagram").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
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
            jQuery("#orgdiagram").css(
                {
                    "width": bodyWidth + "px",
                    "height": bodyHeight + "px"
                });
        }

    </script>
