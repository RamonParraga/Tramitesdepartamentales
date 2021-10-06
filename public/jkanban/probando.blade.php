
@extends('layouts.service')
@section('contenido')


    <link href="{{asset('jkanban/css/jkanban.css')}}" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet"/>


    <div class="row" id="administador_departamentos">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2> <i class="fa fa-edit"></i> Registro de departamento</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">


                        <div id="myKanban" style="color: black"></div>
                        <button id="addDefault">Agregar Columna</button>
                        <br/>
                        <button id="addToDo">Agregar Actividad</button>
                        <br/>
                        <button id="removeBoard">Eliminar Columna</button>
                        <br/>
                        <button id="removeElement">Eliminar Actividad</button>


                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('jkanban/js/jkanban.js')}}"></script>

    <script>
        var anchoColumna = "200px";
        var KanbanTest = new jKanban({
            element: "#myKanban",
            gutter: "2px", //espacion entre columnas
            widthBoard: anchoColumna, // ancho de cada columna
            buttonContent: "-", // texto boton por defecto (no acepta codigo html)
            itemHandleOptions:{
                enabled: false, // habilitar de deshabilitar el icono de la edicion del diagrama
                customCssIconHandler:"fa fa-user"
            },
            click: function(el) { // al darle click a una actividad
                console.log("Trigger on all items click!");
                console.log(el); // acceso al elemento de la actividad
            },
            dropEl: function(el, target, source, sibling){ // funcion que se ejecuta al mover una fila
                console.log(target.parentElement.getAttribute('data-id'));
                console.log(el, target, source, sibling)
                console.log("Moviendo");
            },
            buttonClick: function(btn_plus, boardId) { // click al boton mas
                //btn_plus : instancion al boton
                //boardId : dataId del la columna

                var columnaActual = $(`div[data-id='${boardId}']`);
                var tituloCol = $(columnaActual).find(".kanban-title-board").eq(0).html(); // obtenemos el titulo de la columna (de momento no usado)

                if($(btn_plus).hasClass("content-hidden")){ // mostramos el contenido

                    // mostramos todo el contenido del la columna
                    $(columnaActual).find(".kanban-title-board").eq(0).show(); // ocultamos la cebecera
                    $(columnaActual).find(".kanban-drag").eq(0).show(); // ocultamos el contenedor de actividades
                    var heightContent = $(".kanban-container").height();
                    $(columnaActual).css({
                        "height": `${heightContent}px`,
                        "width": anchoColumna // el tamaño por defecto del acolunma
                    });

                    $(btn_plus).removeClass("content-hidden");
                    $(btn_plus).html("<i class='fa fa-minus'></i>");

                }else{ // ocultamos el contenido

                    // ocultamos todo el contenido del la columna
                    $(columnaActual).find(".kanban-title-board").eq(0).hide(); // ocultamos la cebecera
                    $(columnaActual).find(".kanban-drag").eq(0).hide(); // ocultamos el contenedor de actividades
                    var heightContent = $(".kanban-container").height();
                    $(columnaActual).css({
                        "height": `${heightContent}px`,
                        "width":"auto"
                    });

                    $(btn_plus).addClass("content-hidden");
                    $(btn_plus).html("<i class='fa fa-plus'></i>");

                }

            },
            addItemButton: true,
            boards: [
            {
                id: "_todo",
                title: "Nombre del departamento 1",
                class: "success,good",
                dragTo: ["_todo"], /*definir el id al que puede mover actividades (por defecto a al mismo)*/
                item: [
                    {
                        id: "_test_delete",
                        title: "<i class='fa fa-edit'></i> Actividad d1 f1",
                        drag: function(el, source) {
                            console.log("START DRAG: " + el.dataset.eid);
                        },
                        dragend: function(el) {
                            console.log("END DRAG: " + el.dataset.eid);
                        },
                        drop: function(el) {
                            console.log("DROPPED: " + el.dataset.eid);
                        },
                        class:["actv","actv_info"]
                    },
                    {
                        title: "Actividad d1 f2",
                        click: function(el){
                            alert("click");
                        },
                        class:["actv"]
                    }
                ]
            },
            {
                id: "_working",
                title: "Sub dirección de tecnologias",
                class: "warning",
                dragTo: ["_working"], /*definir el id al que puede mover actividades (por defecto a al mismo)*/
                item: [
                    {title: "Actividad d2 f1", class:["actv","actv_info"]},
                    {title: "Actividad d2 f2", class:["actv","actv_danger"]}
                ]
            },
            {
                id: "_done",
                title: "Nombre del departamento 3",
                class: "warning",
                dragTo: ["_done"], /*definir el id al que puede mover actividades (por defecto a al mismo)*/
                item: [
                    {title: "Actividad d3 f1", class:["actv", "actv_warning"]},
                    {title: "Actividad d3 f2", class:["actv"]}
                ]
            }
            ]
        });

        var toDoButton = document.getElementById("addToDo");
        toDoButton.addEventListener("click", function() {
            KanbanTest.addElement("_todo", {
                title: "Actividad Agregada"
            });
        });

        var addBoardDefault = document.getElementById("addDefault");
        addBoardDefault.addEventListener("click", function() {
            KanbanTest.addBoards([
                {
                    id: "_default",
                    title: "Kanban Default",
                    item: [
                        {title: "Default Item", class:["actv"]},
                        {title: "Default Item 2", class:["actv"]},
                        {title: "Default Item 3", class:["actv"]}
                    ]
                }
            ]);
        });

        var removeBoard = document.getElementById("removeBoard");
        removeBoard.addEventListener("click", function() {
            KanbanTest.removeBoard("_done");
        });

        var removeElement = document.getElementById("removeElement");
        removeElement.addEventListener("click", function() {
            KanbanTest.removeElement("_test_delete");
        });

        var allEle = KanbanTest.getBoardElements("_todo");
        allEle.forEach(function(item, index) {
            //console.log(item);
        });

        $(document).ready(function(){
            $(".kanban-title-button").html("<i class='fa fa-minus'></i>"); // inicializamos el boton de la columna

            // dar un tamaño fijo a las columnas
            var tamGenContent = $(".kanban-container").height();
            // $(".kanban-board").css("height", tamGenContent+"px");
        
         
        });
    </script>



@endsection
