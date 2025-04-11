@extends('layout.body')
@section('css')
<link href="{{ asset('assets/master/lib/datatables.net-dt/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    /* Custom Styling */
    .table-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Efek shadow untuk kartu */
        border: 1px solid black; /* Menambahkan border hitam */
        width: 100%; /* Agar kartu selebar mungkin */
    }
    #example2 {
        width: 100% !important; /* Paksa tabel menjadi 100% */
    }
    .custom-link {
        text-decoration: none;  /* Hilangkan garis bawah */
        color: rgb(64,64,64);           /* Warna default hitam */
    }

    .custom-link:hover, .custom-link:focus {
        color: blue;            /* Warna biru saat hover atau klik */
        text-decoration: underline;
    }

</style>
@endsection
@section('content')
<div class="az-content-breadcrumb">
    <span>Master</span>
    <span>Users</span>
</div>

<div class="az-content-label mg-b-5"></div>

<div class="table-card">
    <div class="row grid-margin">
        <div class="d-sm-flex align-items-center wd-sm-400">
        <div class="form-group mb-0">
            <button type="button" class="btn btn-sm btn-primary" id="btn-add">
                <i class="typcn typcn-document-add"></i> User
            </button>
        </div>
        <div class="mx-sm-3 my-2 my-sm-0 border-start ps-sm-3">
            <select name="filterrole" id="filterrole" class="form-control select2-no-search">
                <option value="adm" selected>Admin</option>
                <option value="krw">Karyawan</option>
            </select>
        </div>
    </div>

    </div>
    <br>
    <table id="example2" class="table">
        <thead>
            <tr>
                <td>No</td>
                <td>Name</td>
                <td>Email</td>
                <td>Phone</td>
                <td>Status</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel"><span id="texttambah">Tambah</span> User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    @csrf
                    <input type="hidden" name="role" id="role">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="off" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button> <!-- Untuk tambah -->
                        <button type="submit" class="btn btn-primary" id="btn-update" style="display: none;">Update</button> <!-- Untuk edit -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- migrasi karyawan dengan excel -->
 <div class="modal fade" id="addMigrasiKrw" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Migrasi user karyawan dengan excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="employeemigrate">
                    <div class="form-group">
                        <label for="new_excel_file">Klik <a href="/export-user-employee">di sini</a> untuk mendapat data karyawan sebelum migrasi user</label>
                        <div class="row row-sm">
                            <div class="col-sm-5 col-md-6 col-lg-9">
                                <input type="file" class="custom-file-input" id="addFileMigrate" name="addFileMigrate">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitUserEmployee">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
 </div>
@endsection

@section('script')
<script src="{{ asset('assets/master/lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net-dt/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/select2/js/select2.min.js') }}"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // Inisialisasi DataTable dengan filter default 'adm'
        var datatable = $('#example2').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: true,
            ajax: {
                url: '{!! url()->current() !!}',
                data: function (d) {
                    d.role = $('#filterrole').val() || 'adm'; // Default role 'adm'
                }
            },
            columns: [
                { "data": 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        let statusClass = data ? 'text-success' : 'text-danger';
                        let statusText = data ? 'Aktif' : 'Non-Aktif';

                        return `<a href="javascript:void(0);" class="toggle-status ${statusClass}" data-id="${row.id}" data-status="${data}">
                                    ${statusText}
                                </a>`;
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '15%' }
            ]
        });

        // Event listener untuk filter dropdown
        $('#filterrole').on('change', function () {
            let selectedRole = $(this).val();

            if (selectedRole === 'krw') {
                // Jika role = 'krw', sembunyikan tombol User dan tampilkan tombol "Tambah User Dengan Excel"
                $('#btn-add').hide();
                if ($('#btn-add-excel').length === 0) {
                    $('#btn-add').after(`
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add-excel"> 
                            <i class="typcn typcn-document-add"></i> Migrasi User Karyawan 
                        </button>
                    `);
                }
            } else {
                // Jika role bukan 'krw', tampilkan kembali tombol "User" dan hapus tombol Excel
                $('#btn-add-excel').remove(); // Hapus tombol Excel jika ada
                $('#btn-add').show(); // Pastikan tombol User muncul kembali
            }

            datatable.ajax.reload(); // Reload tabel saat filter berubah
        });


        // Select2 untuk dropdown filter
        $('.select2-no-search').select2({
            minimumResultsForSearch: Infinity,
            placeholder: 'Choose one'
        });

        $('body').on('click', '#btn-add', function() {
            console.log("Tombol Tambah diklik"); // Debugging

            var filterrole = $('#filterrole').val();

            // Kosongkan input
            $('#name').val('');
            $('#email').val('');
            $('#phone').val('');
            $('#password').val('');
            $('#role').val(filterrole);

            // Reset ke mode tambah
            $('#texttambah').text('Tambah');
            $('#addModalLabel').text('Tambah User');

            $('#btn-save').show();   // Tampilkan tombol Simpan
            $('#btn-update').hide(); // Sembunyikan tombol Update

            $('#addModal').modal('show');
        });

        // Klik tombol edit
        $('body').on('click', '#edit', function() {
            var id = $(this).data('id');

            $.ajax({
                url: "/users/" + id + "/edit",
                type: "GET",
                success: function(response) {
                    $('#name').val(response.name);
                    $('#email').val(response.email);
                    $('#phone').val(response.phone);
                    $('#role').val(response.role);
                    $('#password').val(''); // Kosongkan password agar user harus mengisi ulang

                    // Ubah ke mode Edit
                    $('#texttambah').text('Ubah');
                    $('#addModalLabel').text('Ubah User');

                    $('#btn-save').hide();   // Sembunyikan tombol Simpan
                    $('#btn-update').show(); // Tampilkan tombol Update

                    // Tambahkan input hidden untuk id_user jika belum ada
                    if ($('#id_user').length === 0) {
                        $('#addForm').prepend('<input type="hidden" id="id_user" name="id_user" value="' + response.id + '">');
                    } else {
                        $('#id_user').val(response.id);
                    }

                    $('#password').removeAttr('required'); // Hapus required dari password

                    $('#addModal').modal('show');
                }
            });
        });

        
        $('#addModal').on('hidden.bs.modal', function () {
            $('#texttambah').text('Tambah'); // Kembali ke "Tambah User"
            $('#addModalLabel').text('Tambah User');
            $('.modal-footer .btn-primary').text('Simpan'); // Tombol kembali ke "Simpan"
            $('#id_user').remove(); // Hapus input hidden id_user agar tidak ada saat tambah user
        });


        // Submit Form Tambah
        $('#btn-save').on('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('users.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    password: $('#password').val(),
                    role: $('#role').val()
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Berhasil!", response.message, "success");
                        $('#addModal').modal('hide');
                        $('#example2').DataTable().ajax.reload();
                    } else {
                        Swal.fire("Gagal!", response.message || "Terjadi kesalahan!", "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Terjadi kesalahan pada server!", "error");
                }
            });
        });

        $('#btn-update').on('click', function(e) {
            e.preventDefault();

            var id = $('#id_user').val();

            $.ajax({
                url: "/users/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: "PUT",
                    name: $('#name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    password: $('#password').val(),
                    role: $('#role').val()
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Berhasil!", response.message, "success");
                        $('#addModal').modal('hide');
                        $('#example2').DataTable().ajax.reload();
                    } else {
                        Swal.fire("Gagal!", response.message || "Terjadi kesalahan!", "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Terjadi kesalahan pada server!", "error");
                    console.log(xhr.responseText); // Debugging
                }
            });
        });

        // Toggle status aktif/non-aktif
        $('body').on('click', '.toggle-status', function() {
            let Id = $(this).data('id');
            let currentStatus = $(this).data('status');

            $.ajax({
                url: `/users/${Id}/toggle-status`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: !currentStatus
                },
                success: function(response) {
                    Swal.fire("Berhasil!", response.message, "success");
                    $('#example2').DataTable().ajax.reload(); // Refresh tabel
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Gagal mengubah status.", "error");
                }
            });
        });

        // show
        $('body').on('click', '#show', function(){
            let Id = $(this).data('id');
            window.location.href = `/users/${Id}`;
        })

        $('body').on('click', '#btn-add-excel', function () {
            $('#addMigrasiKrw').modal('show')
        })

        $('#submitUserEmployee').on('click', function(e){
            e.preventDefault(); 
            var formData = new FormData();
            formData.append('addFileMigrate', $('#addFileMigrate')[0].files[0]); // Ambil file dari input
            formData.append('_token', '{{ csrf_token() }}'); // Tambahkan token CSRF

            $.ajax({
                url : "{{ route('importUserEmployee') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire("Berhasil!", response.message, "success"); // Notifikasi sukses
                    $('#addMigrasiKrw').modal('hide'); // Tutup modal
                    $('#example2').DataTable().ajax.reload(); // Refresh DataTable
                },
                error: function(xhr) {
                    Swal.fire("Gagal!", xhr.responseJSON?.message || "Terjadi kesalahan saat mengupload file.", "error");
                    console.log(xhr.responseText);
                }
            })
        });

    });

</script>

@if(session('success'))
    <script>
        Swal.fire({
        title: "Berhasil!",
        text: "{{ session('success') }}",
        icon: "success"
        });
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
        title: "Error!",
        text: "{{ session('error') }}",
        icon: "error"
        });
    </script>
@endif
@endsection