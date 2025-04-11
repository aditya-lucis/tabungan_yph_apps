<link href="{{ URL::asset('assets/master/lib/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/lib/typicons.font/typicons.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/css/datatables/dataTables.bs4.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/css/datatables/dataTables.bs4-custom.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/css/datatables/buttons.bs.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/css/azia.css') }}" rel="stylesheet">

<style>
    body::before {
        content: "";
        position: fixed; /* Agar tetap di tempat saat di-scroll */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("{{ URL::asset('assets/auth/img/thumb.jpg') }}");
        background-size: 700px; /* Sesuaikan ukuran watermark */
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        opacity: 0.1; /* Sesuaikan transparansi agar tidak mengganggu */
        z-index: -1; /* Pastikan di belakang konten */
    }
</style>


@yield('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">
