@include('layout.header')
<body>
@include('layout.navbar')
    <div class="az-content pd-y-20 pd-lg-y-30 pd-xl-y-40">
      <div class="container">
        <div class="az-content-body pd-lg-l-40 d-flex flex-column">
        @yield('content')
@php
    $year = date("Y");
@endphp
<!-- <div class="az-footer mg-t-auto"> -->
    <!-- <div class="container"> -->
    <!-- <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Yayasan Persada Hati {{$year}}</span> -->
    <!-- <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> Free <a href="https://www.bootstrapdash.com/bootstrap-admin-template/" target="_blank">Bootstrap admin templates</a> from Bootstrapdash.com</span> -->
    <!-- </div> -->
    <!-- container -->
<!-- </div> -->
<!-- az-footer -->
</div><!-- az-content-body -->
      </div><!-- container -->
    </div><!-- az-content -->
@include('layout.footer')