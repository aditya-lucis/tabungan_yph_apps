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
    <span>Employee</span>
</div>

<div class="az-content-label mg-b-5">Daftar karyawan Persada Group</div>
          <!-- <p class="mg-b-20">Responsive is an extension for DataTables that resolves that problem by optimising the table's layout for different screen sizes through the dynamic insertion and removal of columns from the table.</p> -->
<br>
          <div class="table-card">
            <div class="row grid-margin">
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add">
                        <i class="typcn typcn-document-add"></i> Karyawan
                        </button>
                        <a class="btn btn-sm btn-success" id="btn-excel">
                        <i class="typcn typcn-folder-add"></i> Tambah Data Karyawan Dengan Excel
                        </a>
                    </div>
                  </div>

            <table id="example2" class="table">
              <thead>
                <tr>
                  <th class="wd-20p">No</th>
                  <th class="wd-25p">Nama</th>
                  <th class="wd-25p">Affco</th>
                  <th class="wd-25p">Kontak</th>
                  <th class="wd-25p">Email</th>
                  <th class="wd-25p">Status</th>
                  <th class="wd-25p">Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

            <!-- Modal Tambah Excel -->
            <div class="modal fade" id="addModalExcel" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Upload File Excel Berisi Data Karyawan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="addFormEcel">
                                @csrf
                                <div class="form-group">
                                    <label for="new_excel_file">Silahkan klik <a href="/get-format-employee">di sini</a> untuk mendapat format data karyawan</label>
                                    <!-- <input type="file" class="custom-file-input" id="addFile" name="addFile"> -->
                                    <div class="row row-sm">
                                        <div class="col-sm-7 col-md-6 col-lg-9">
                                        <input type="file" class="custom-file-input" id="addFile" name="addFile">
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addAuthentikasi" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Autentikasi Karyawan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="addFormAuth">
                                @csrf
                                <input type="hidden" name="idkaryawan" id="idkaryawan">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    <small id="passwordError" class="text-danger" style="display: none;"></small>
                                </div>
                                <button type="submit" id="submitBtn" class="btn btn-primary" disabled>Update</button>
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
        var datatable = $('#example2').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: true,
            ajax: {
                url: '{!! url()->current() !!}'
            },
            columns: [{
                "data": 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'name',
                name: 'name',
                render: function(data, type, row) {
                    return `<a href="/employee/${row.id}/edit" class="custom-link">${data}</a>`;
                }
            },
            {
                data: 'company.name',
                name: 'company.name'
            },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'isactive',
                name: 'isactive',
                render: function(data, type, row) {
                    let statusClass = data ? 'text-success' : 'text-danger'; // Hijau jika aktif, merah jika non-aktif
                    let statusText = data ? 'Aktif' : 'Non-Aktif';

                    return `<a href="javascript:void(0);" class="toggle-status ${statusClass}" data-id="${row.id}" data-status="${data}">
                                ${statusText}
                            </a>`;
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                width: '15%'
            }
        ]
    });

    $('#new_company').select2({
        placeholder: "Pilih Affco",
        allowClear: true,
        width: '100%',
        minimumResultsForSearch: 0  // Paksa selalu muncul pencarian
    });

    // klik tombol autentikasi karyawan
    $('body').on('click', '#adminadd', function () {
        var employeeId = $(this).data('id');

        $.ajax({
            url: "/employee/" + employeeId + "/get",
            type: "GET",
            success: function(response){
                $('#idkaryawan').val(response.id);
                $('#addAuthentikasi').modal('show');
            }
        })
        .fail(function (xhr) {
            if (xhr.status === 400) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON.message, // Menampilkan pesan dari server
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan, silakan coba lagi!',
                });
            }
        });
    });

    // Validasi password secara real-time
    function validatePasswords() {
        let password = $('#new_password').val();
        let confirmPassword = $('#confirm_password').val();
        let submitBtn = $('#submitBtn'); // Tombol submit

        // Jika salah satu input masih kosong, reset semua status
        if (password === '' || confirmPassword === '') {
            $('#new_password, #confirm_password').removeClass('is-invalid is-valid');
            $('#passwordError').hide();
            submitBtn.prop('disabled', true);
            return;
        }

        // Jika kedua input sudah diisi, baru jalankan validasi
        if (password !== confirmPassword) {
            $('#confirm_password, #new_password').addClass('is-invalid');
            $('#passwordError').text('Password tidak cocok').show();
            submitBtn.prop('disabled', true);
        } else {
            $('#confirm_password, #new_password').removeClass('is-invalid').addClass('is-valid');
            $('#passwordError').hide();
            submitBtn.prop('disabled', false);
        }
    }

    // Panggil fungsi validasi saat pengguna mengetik
    $('#new_password, #confirm_password').on('keyup', validatePasswords);

    // RESET FORM SAAT MODAL DITUTUP
    $('#addAuthentikasi').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset(); // Reset semua input dalam form
        $('#new_password, #confirm_password').removeClass('is-invalid is-valid'); // Hapus warna outline
        $('#passwordError').hide(); // Sembunyikan pesan error
        $('#submitBtn').prop('disabled', true); // Nonaktifkan tombol submit lagi
    });

    // submit password autentikasi untuk karyawan
    $('#addFormAuth').submit(function(e){
        e.preventDefault();
        var karyawanId = $('#idkaryawan').val();
        var newpassword = $('#new_password').val();
        var confirmpassword = $('#confirm_password').val();

        $.ajax({
            url: "{{ route('authemployee') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                idkaryawan: karyawanId,
                new_password: newpassword,
                confirm_password: confirmpassword
            },
            success: function(response) {
                if (response.success) {  // Perbaikan: gunakan 'success' agar sesuai dengan response controller
                    Swal.fire({
                        title: "Berhasil!",
                        text: response.message,
                        icon: "success"
                    });

                    $('#addAuthentikasi').modal('hide');

                    // Bersihkan input field
                    $('#new_password').val('');
                    $('#confirm_password').val('');

                    // Reload DataTable
                    $('#example2').DataTable().ajax.reload();
                } else {
                    Swal.fire({
                        title: "Gagal!",
                        text: response.message || "Terjadi kesalahan!",
                        icon: "error"
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: "Error!",
                    text: xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan pada server!",
                    icon: "error"
                });
            }
        });
    });


    // Klik tombol Tambah
    $('body').on('click', '#btn-add', function() {
        console.log("Tombol Tambah diklik"); // Debugging
        window.location.href = "/employee/create";
    });


    // Klik tombol tambah Excel
    $('body').on('click', '#btn-excel', function() {
        console.log("Tombol Tambah Excel diklik"); // Debugging
        $('#addModalExcel').modal('show'); // Tampilkan modal 
    });

    $('#addFormEcel').submit(function(e) {
        e.preventDefault(); 

        var formData = new FormData();
        formData.append('file', $('#addFile')[0].files[0]); // Ambil file dari input
        formData.append('_token', '{{ csrf_token() }}'); // Tambahkan token CSRF

        $.ajax({
            url: "{{ route('import.excel') }}", // Pastikan rute sesuai
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire("Berhasil!", response.message, "success"); // Notifikasi sukses
                $('#addModalExcel').modal('hide'); // Tutup modal
                $('#example2').DataTable().ajax.reload(); // Refresh DataTable
            },
            error: function(xhr) {
                Swal.fire("Gagal!", "Terjadi kesalahan saat mengupload file.", "error");
                console.log(xhr.responseText); // Debugging
            }
        });
    });

    // Toggle status aktif/non-aktif
    $('body').on('click', '.toggle-status', function() {
        let employeeId = $(this).data('id');
        let currentStatus = $(this).data('status');

        $.ajax({
            url: `/employee/${employeeId}/toggle-status`,
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