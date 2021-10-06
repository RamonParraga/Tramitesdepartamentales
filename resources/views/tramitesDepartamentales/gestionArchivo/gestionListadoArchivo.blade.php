@extends('layouts.service')
@section('contenido')


<!-- Custom Theme Style -->
<link href="../build/css/custom.min.css" rel="stylesheet">
<!-- Datatables -->
<link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
<!-- <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
<!-- PNotify -->
<link href="{{asset('vendors/pnotify/dist/pnotify.css')}}" rel="stylesheet">
<link href="{{asset('vendors/pnotify/dist/pnotify.buttons.css')}}" rel="stylesheet">
<link href="{{asset('vendors/pnotify/dist/pnotify.nonblock.css')}}" rel="stylesheet">


<link href="{{asset('vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">

<script src="{{asset('vendors/pnotify/dist/pnotify.js')}}"></script>
<script src="{{asset('vendors/pnotify/dist/pnotify.buttons.js')}}"></script>

<style type="text/css">
        .check_rqs{
            background-color: transparent;
            border: 1px solid #ccc;
            /* border-radius: 30px; */
            /* padding: top right bottom left */
            padding: 8px 5px 6px 15px;
            width: 100%;
        }
        .check_rqs label{
            margin-bottom: 0px !important;
            margin-right: 10px !important;
        }
        .check_rqs .icheckbox_flat-green{
            margin-right: 8px !important;
        }

        /* estilos solo para telefonos */
        @media screen and (max-width: 767px){
            .check_rqs{
                width: 100%;
            }
        }
    </style>

   <!-- iCheck -->
    <script src="{{asset('vendors/iCheck/icheck.min.js')}}"></script>

    
    @include('tramitesDepartamentales.gestionArchivo.actualizarArchivo')

    @include('tramitesDepartamentales.gestionArchivo.listaArchivo')

     {{-- modales para agregar tipos de documento y las actividades  --}}
    @include('tramitesDepartamentales.gestionArchivo.modales_lugar_bodega')



<script type="text/javascript">
        $(document).ready(function () {
            $("#id_tablagestion").DataTable({
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por pagina",
            "zeroRecords": "No se encontraron resultados en su busqueda",
            "searchPlaceholder": "Buscar registros",
            "info": "Mostrando registros de _START_ al _END_ de un total de  _TOTAL_ registros",
            "infoEmpty": "No existen registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
        }
    }); 
            $('.collapse-link').click();
            $('.datatable_wrapper').children('.row').css('overflow','inherit !important');
            $('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0','overflow-x':'inherit'});
        });
    </script>


     
<!-- Datatables -->
<script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
{{-- <script src="../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script> --}}
<script src="{{asset('/js/TramiteDepartamental/gestionArchivo.js')}}"></script>

@endsection