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
    <span>Program</span>
</div>

<div class="az-content-label mg-b-5">Daftar Program Tabungan Pendidikan Persada Hati</div>
          <!-- <p class="mg-b-20">Responsive is an extension for DataTables that resolves that problem by optimising the table's layout for different screen sizes through the dynamic insertion and removal of columns from the table.</p> -->
<br>
          <div class="table-card">
            <div class="row grid-margin">
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add">
                        <i class="typcn typcn-plus"></i>
                        </button>
                    </div>
                  </div>
            <table id="example2" class="table">
              <thead>
                <tr>
                  <th class="wd-20p">No</th>
                  <th class="wd-25p">Jenjang Pendidikan</th>
                  <th class="wd-25p">Nominal</th>
                  <th class="wd-25p">Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          
         <!-- Modal Tambah Program -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Tambah Program</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="addForm">
                                @csrf
                                <div class="form-group">
                                    <label for="new_program_level">Jenjang Pendidikan</label>
                                    <input type="text" class="form-control" id="new_program_level" name="level" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="new_total_program">Nominal</label>
                                    <input type="text" class="form-control" id="new_total_program" name="total" value="0" autocomplete="off">
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


        <!-- Modal Edit -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Program</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm">
                                @csrf
                                <input type="hidden" id="program_id">
                                <div class="mb-3">
                                    <label for="program_level" class="form-label">Jenjang Pendidikan</label>
                                    <input type="text" class="form-control" id="program_level" name="program_level">
                                </div>
                                <div class="mb-3">
                                    <label for="program_total" class="form-label">Nominal</label>
                                    <input type="text" class="form-control" id="program_total" name="program_total">
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
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    var datatable = $('#example2').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ordering: true,
        ajax: {
            url: '{!! url()->current() !!}'
        },
        columns: [
            { "data": 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'level', name: 'level' },
            { 
                data: 'total', 
                name: 'total',
                render: function(data, type, row) {
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(data);
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false, width: '15%' }
        ]
    });

    // Klik tombol Tambah
    $('body').on('click', '#btn-add', function() {
        console.log("Tombol Tambah diklik"); // Debugging
        $('#new_program_level').val(''); // Bersihkan input
        $('#new_total_program').val(''); // Bersihkan input
        $('#addModal').modal('show'); // Tampilkan modal
    });

    $('#program_total').maskNumber({integer: true});
    $('#new_total_program').maskNumber({integer: true});

    function cleanNumber(value) {
        if (!value) return "0"; // Jika kosong, kembalikan "0"
        return value.replace(/[^\d]/g, ''); // Hapus semua karakter selain angka
    }
    
    function formatNumber(value) {
        return parseInt(value).toLocaleString('id-ID'); // Format ribuan, hapus desimal
    }



    document.getElementById('program_total').addEventListener('input', function (e) {
        let value = e.target.value.replace(/[^0-9.]/g, ''); // Hanya izinkan angka dan titik
        if (value.includes('.')) {
            let parts = value.split('.');
            parts[1] = parts[1].substring(0, 2); // Batasi maksimal 2 angka setelah koma
            value = parts.join('.');
        }
        e.target.value = value;
    });


    // Submit Form Tambah
    $('#addForm').submit(function(e) {
        e.preventDefault();

        var programLevel = $('#new_program_level').val();
        var programTotal = cleanNumber($('#new_total_program').val()); // Bersihkan angka sebelum dikirim

        $.ajax({
            url: "{{ route('program.store') }}", // Pastikan route ini benar
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                level: programLevel,
                total: programTotal
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Program berhasil ditambahkan!",
                        icon: "success"
                    });

                    $('#addModal').modal('hide');

                    // Bersihkan input field
                    $('#new_program_level').val('');
                    $('#new_total_program').val('');

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
                    text: "Terjadi kesalahan pada server!",
                    icon: "error"
                });
            }
        });
    });

    // Klik tombol edit
    $('body').on('click', '#edit', function() {
        var programId = $(this).data('id');

        // Ambil data berdasarkan ID
        $.ajax({
            url: "/program/" + programId + "/edit",
            type: "GET",
            success: function(response) {
                $('#program_id').val(response.id);
                $('#program_level').val(response.level);
                
                // Ubah format angka ke desimal
                let formattedTotal = formatNumber(response.total);
                $('#program_total').val(formattedTotal);

                $('#editModal').modal('show');
            }
        });
    });


    $('#editForm').submit(function(e) {
        e.preventDefault();
        var programId = $('#program_id').val();
        var programLevel = $('#program_level').val();
        var programTotal = cleanNumber($('#program_total').val());

        $.ajax({
            url: "/program/" + programId,
            type: "POST", // Gunakan POST
            data: {
                _token: "{{ csrf_token() }}",
                _method: "PUT", // Laravel mengenali ini sebagai PUT
                level: programLevel,
                total: programTotal
            },
            success: function(response) {
                Swal.fire({
                    title: "Berhasil!",
                    text: "Data berhasil diupdate!",
                    icon: "success"
                });

                $('#editModal').modal('hide');
                $('#example2').DataTable().ajax.reload();
            },
            error: function(xhr) {
                Swal.fire({
                    title: "Error!",
                    text: "Terjadi kesalahan pada server!",
                    icon: "error"
                });
                console.log(xhr.responseText); // Debugging
            }
        });

    });


});

</script>
@endsection