@extends('layout.body')

@section('css')
<link href="{{ asset('assets/master/lib/datatables.net-dt/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/select2/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/lightslider/css/lightslider.min.css') }}" rel="stylesheet">
<style>
    /* Custom Styling */
    .table-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid black;
        width: 100%;
    }
    .custom-link {
        text-decoration: none;
        color: rgb(64,64,64);
    }
    .custom-link:hover, .custom-link:focus {
        color: blue;
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="az-content-label mg-b-5">{{$User->name}}</div>
<div class="card bd-0">
    <div class="card-header bg-gray-400 bd-b-0-f pd-b-0">
        <div class="nav nav-tabs">
            <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabCont1">Profile</a>
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCont2">Password</a>
        </div>
    </div>
    <div class="card-body bd bd-t-0 tab-content">
        <div id="tabCont1" class="tab-pane fade show active">
            <div class="row">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <form id="formprofile">
                        @csrf
                        <div class="card card-body pd-40">
                            <div class="form-group mb-2">
                                <label class="az-content-label tx-11 tx-medium tx-gray-600">Nama User</label>
                                <input type="hidden" name="id_session" id="id_session" class="form-control" value="{{$User->id}}" required>
                                <input type="text" name="name" id="name" class="form-control" value="{{$User->name}}" required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="az-content-label tx-11 tx-medium tx-gray-600">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{$User->email}}" required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="az-content-label tx-11 tx-medium tx-gray-600">Email</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{$User->phone}}" required>
                            </div>
                            <button class="btn btn-az-primary btn-block" id="updateprofile">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="tabCont2" class="tab-pane fade">
            <div class="row">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <form id="formpassword">
                        @csrf
                        <div class="card card-body pd-40">
                            <div class="form-group mb-2">
                                <label class="az-content-label tx-11 tx-medium tx-gray-600">New Password</label>
                                <input type="hidden" name="id_cookie" id="id_cookie" class="form-control" value="{{$User->id}}" required>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            <div class="form-group mb-2">
                                <label class="az-content-label tx-11 tx-medium tx-gray-600">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                <small id="passwordError" class="text-danger" style="display: none;"></small>
                            </div>
                            <button class="btn btn-az-primary btn-block" id="updatepassword" disabled>Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/master/lib/lightslider/js/lightslider.min.js') }}"></script> 
<script src="{{ asset('assets/master/lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var tabLinks = document.querySelectorAll(".nav-tabs .nav-link");
        
        tabLinks.forEach(function(tab) {
            tab.addEventListener("click", function (e) {
                e.preventDefault();

                // Hapus kelas active dari semua tab
                tabLinks.forEach(function(t) {
                    t.classList.remove("active");
                });

                // Tambahkan active ke tab yang diklik
                this.classList.add("active");

                // Sembunyikan semua tab content
                document.querySelectorAll(".tab-pane").forEach(function(pane) {
                    pane.classList.remove("show", "active");
                });

                // Tampilkan tab content yang sesuai
                var target = this.getAttribute("data-bs-target");
                document.querySelector(target).classList.add("show", "active");
            });
        });
    });
</script>
<script>
    // Validasi password secara real-time
    function validatePasswords() {
        let password = $('#new_password').val();
        let confirmPassword = $('#confirm_password').val();
        let updatepassword = $('#updatepassword'); // Tombol submit

        // Jika salah satu input masih kosong, reset semua status
        if (password === '' || confirmPassword === '') {
            $('#new_password, #confirm_password').removeClass('is-invalid is-valid');
            $('#passwordError').hide();
            updatepassword.prop('disabled', true);
            return;
        }

        // Jika kedua input sudah diisi, baru jalankan validasi
        if (password !== confirmPassword) {
            $('#confirm_password, #new_password').addClass('is-invalid');
            $('#passwordError').text('Password tidak cocok').show();
            updatepassword.prop('disabled', true);
        } else {
            $('#confirm_password, #new_password').removeClass('is-invalid').addClass('is-valid');
            $('#passwordError').hide();
            updatepassword.prop('disabled', false);
        }
    }

    // Panggil fungsi validasi saat pengguna mengetik
    $('#new_password, #confirm_password').on('keyup', validatePasswords);

    $('#updateprofile').on('click', function(e){
        e.preventDefault();

        id = $('#id_session').val();

        $.ajax({
            url: "/users/" + id + "/update-profile",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                _method: "PUT",
                name: $('#name').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire("Berhasil!", response.message, "success")
                    .then(() => {
                        location.reload();
                    });
                }else{
                    Swal.fire("Gagal!", response.message || "Terjadi kesalahan!", "error");
                }
            },
            error: function(xhr){
                Swal.fire("Error!", "Terjadi kesalahan pada server!", "error");
                console.log(xhr.responseText); // Debugging
            }
        })

    })

    $('#updatepassword').on('click', function(e){
        e.preventDefault();
        id = $('#id_cookie').val();
        $.ajax({
            url: "/users/" + id + "/update-password",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                _method: "PUT",
                new_password: $('#new_password').val(),
                confirm_password: $('#confirm_password').val()
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire("Berhasil!", response.message, "success")
                    .then(() => {
                        location.reload();
                    });
                }else{
                    Swal.fire("Gagal!", response.message || "Terjadi kesalahan!", "error");
                }
            },
            error: function(xhr){
                Swal.fire("Error!", "Terjadi kesalahan pada server!", "error");
                console.log(xhr.responseText); // Debugging
            }
        })
    })

</script>
@endsection
