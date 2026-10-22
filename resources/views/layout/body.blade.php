@include('layout.header')
<body>
@include('layout.navbar')
    <div class="az-content pd-y-20 pd-lg-y-30 pd-xl-y-40">
      <div class="container">
        <div class="az-content-body pd-lg-l-40 d-flex flex-column">
        @yield('content')
        </div><!-- az-content-body -->
      </div><!-- container -->
    </div><!-- az-content -->

    <footer class="app-footer">
      <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between py-2">
        <div class="text-center text-md-left mb-2 mb-md-0">
          <span class="text-muted" style="font-size: 13px;">
            &copy; {{ date('Y') }} <strong>Yayasan Persada Hati</strong>. All rights reserved.
          </span>
        </div>
        <div class="text-center text-md-right">
          <span class="text-muted" style="font-size: 13px;">
            <strong style="color: var(--yph-primary);">Aditya Lucis Caelum</strong>
          </span>
        </div>
      </div>
    </footer>
@include('layout.footer')