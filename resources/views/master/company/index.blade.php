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
</style>
@endsection
@section('content')

<div class="az-content-breadcrumb">
    <span>Master</span>
    <span>Company</span>
</div>

<div class="az-content-label mg-b-5">Daftar <i>Affiliate Company</i> Persada Group</div>
          <!-- <p class="mg-b-20">Responsive is an extension for DataTables that resolves that problem by optimising the table's layout for different screen sizes through the dynamic insertion and removal of columns from the table.</p> -->
<br>
          <div class="table-card">
            <div class="row grid-margin">
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add">
                        <i class="typcn typcn-plus"></i> Company
                        </button>
                    </div>
                  </div>
            <table id="example2" class="table">
              <thead>
                <tr>
                  <th class="wd-20p">No</th>
                  <th class="wd-25p">Company</th>
                  <th class="wd-25p"></th>
                  <th class="wd-25p">Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          
          <!-- Modal Tambah Company -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Company</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form id="addForm">
                    @csrf
                    <div class="form-group">
                        <label for="new_company_name">Nama Perusahaan</label>
                        <input type="text" class="form-control" id="new_company_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="new_company_aliase"></label>
                        <input type="text" class="form-control" id="new_company_aliase" name="aliase" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
                </div>
            </div>
            </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Company</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            @csrf
                            <input type="hidden" id="company_id">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="company_aliase" class="form-label"></label>
                                <input type="text" class="form-control" id="company_aliase" name="company_aliase" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
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
                name: 'name'
            },
            {
                data: 'aliase',
                name: 'aliase',
                orderable: false
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

    // Klik tombol tambah
    $('body').on('click', '#btn-add', function() {
        console.log("Tombol Tambah diklik"); // Debugging
        $('#new_company_name').val(''); // Bersihkan input
        $('#addModal').modal('show'); // Tampilkan modal
    });

    // Klik tombol edit
    $('body').on('click', '#edit', function() {
        var companyId = $(this).data('id'); 

        // Ambil data perusahaan berdasarkan ID
        $.ajax({
            url: "/company/" + companyId + "/edit",
            type: "GET",
            success: function(response) {
                $('#company_id').val(response.id);
                $('#company_name').val(response.name);
                $('#company_aliase').val(response.aliase);
                $('#editModal').modal('show');
            }
        });
    });

    $('#editForm').submit(function(e) {
        e.preventDefault();

        var companyId = $('#company_id').val();
        var companyName = $('#company_name').val();
        var companyAliase = $('#company_aliase').val();

        $.ajax({
            url: "/company/" + companyId,
            type: "PUT",
            data: {
                _token: "{{ csrf_token() }}",
                name: companyName,
                aliase: companyAliase
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Data berhasil diupdate!",
                        icon: "success"
                    });

                    // Tutup modal dengan Bootstrap 5 instance
                    $('#editModal').modal('hide'); // Gunakan metode `hide()` yang benar

                    // Reload DataTable agar data terbaru muncul
                    $('#example2').DataTable().ajax.reload();
                } else {
                    Swal.fire({
                        title: "Gagal!",
                        text: "Terjadi kesalahan!",
                        icon: "error"
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    title: "Error!",
                    text: "Terjadi kesalahan pada server!",
                    icon: "error"
                });
            }
        });
    });

    $('#addForm').submit(function(e) {
        e.preventDefault();

        var companyName = $('#new_company_name').val();
        var companyAliase = $('#new_company_aliase').val();

        $.ajax({
            url: "{{ route('company.store') }}", // Pastikan route ini benar
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                name: companyName,
                aliase: companyAliase
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Company berhasil ditambahkan!",
                        icon: "success"
                    });

                    // Tutup modal
                    $('#addModal').modal('hide');

                    // Bersihkan input field
                    $('#new_company_name').val('');

                    // Reload DataTable agar data terbaru muncul
                    $('#example2').DataTable().ajax.reload();
                } else {
                    Swal.fire({
                        title: "Gagal!",
                        text: "Terjadi kesalahan!",
                        icon: "error"
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: "Error!",
                    text: "Terjadi kesalahan pada server!",
                    icon: "error"
                });
            }
        });
    });

    </script>
@endsection