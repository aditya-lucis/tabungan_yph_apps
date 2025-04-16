@extends('layout.body')
@section('css')
<link href="{{ asset('assets/master/lib/line-awesome/css/line-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/quill/quill.snow.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/quill/quill.bubble.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="az-content-breadcrumb">
    <span>Master</span>
    <span>Email Configuration</span>
</div>

<div class="row">
    <div class="col-md-10 col-lg-8 col-xl-7">
        <div class="card card-body pd-40">
            <form id="EmailForm">
                <div class="form-row">
                    <div class="form-group col-md-6 mb-2">
                        <label for="driver" class="az-content-label tx-11 tx-medium tx-gray-600">Mailer Driver</label>
                        <input type="text" class="form-control" name="driver" id="driver" value="{{ $email ? $email->driver : '' }}" required>
                    </div>
                    <div class="form-group col-md-6 mb-2">
                        <label for="host" class="az-content-label tx-11 tx-medium tx-gray-600">Mailer Host</label>
                        <input type="text" class="form-control" name="host" id="host" value="{{ $email ? $email->host : '' }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6 mb-2">
                        <label for="username" class="az-content-label tx-11 tx-medium tx-gray-600">Mailer Username</label>
                        <input type="text" class="form-control" name="username" id="username" value="{{ $email ? $email->username : '' }}" required>
                    </div>
                    <div class="form-group col-md-6 mb-2">
                        <label for="password" class="az-content-label tx-11 tx-medium tx-gray-600">Mailer Password</label>
                        <input type="password" class="form-control" name="password" id="password" value="{{ $email ? $email->password : '' }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6 mb-2">
                        <label for="port" class="az-content-label tx-11 tx-medium tx-gray-600">Mailer Port</label>
                        <input type="text" class="form-control" name="port" id="port" value="{{ $email ? $email->port : '' }}" required>
                    </div>
                    <div class="form-group col-md-6 mb-2">
                        <label for="encryption" class="az-content-label tx-11 tx-medium tx-gray-600">Mailer Encryption</label>
                        <input type="text" class="form-control" name="encryption" id="encryption" value="{{ $email ? $email->encryption : '' }}" required>
                    </div>
                </div>

                <br>
                <button type="button" class="btn btn-az-primary pd-x-30 mg-r-5" id="updateEmail">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#updateEmail').on('click', function(e){
        e.preventDefault();

        $.ajax({
            url: "{{ route('email-update') }}",
            type: "POST", // Ganti ke POST
            data: {
                _token      : "{{ csrf_token() }}",
                _method     : "PUT", // Spoofing method PUT
                driver      : $('#driver').val(),
                host        : $('#host').val(),
                username    : $('#username').val(),
                password    : $('#password').val(),
                port        : $('#port').val(),
                encryption  : $('#encryption').val()
            },
            success: function (response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            },
            error: function (xhr) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan data.',
                    icon: 'error'
                });
                console.log(xhr.responseText)
            }
        });

    })
</script>
@endsection