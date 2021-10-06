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

<script src="{{asset('vendors/pnotify/dist/pnotify.js')}}"></script>
<script src="{{asset('vendors/pnotify/dist/pnotify.buttons.js')}}"></script>


    @include('tramitesDepartamentales.gestionEstructuraDocumento.registrarEstructuraDocumento')
    @include('tramitesDepartamentales.gestionEstructuraDocumento.listaEstructuraDocumento')
    @include('tramitesDepartamentales.gestionEstructuraDocumento.modalNuevoAnio')



<script type="text/javascript">
    $(document).ready(function () {
        $('.collapse-link').click();
        $('.datatable_wrapper').children('.row').css('overflow','inherit !important');
        $('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0','overflow-x':'inherit'});
    });
</script>
     
<!-- Datatables -->
<script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
{{-- <script src="../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script> --}}
<script src="{{asset('/js/TramiteDepartamental/gestionEstructuraDocumento.js')}}"></script>
@endsection