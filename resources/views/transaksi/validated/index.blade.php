@extends('layout.body')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">

<style>
    .table-card {
        background-color: #fff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #dee2e6;
        width: 100%;
    }

    .table th, .table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .table th {
        background-color: #f8f9fa;
    }

    .input-group-text {
        background-color: #f0f0f0;
        border-right: 0;
    }

    .form-control {
        border-left: 0;
    }
</style>
@endsection

@section('content')
<div class="az-content-breadcrumb mb-3">
    <span>Tabungan</span>
    <span>Approval Pendaftaran</span>
</div>

<div class="mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Tanggal Awal:</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text">
                    <i class="typcn typcn-calendar-outline"></i>
                </span>
                <input type="text" id="start_date" class="form-control fc-datepicker" placeholder="Awal">
            </div>
        </div>

        <div class="col-md-3">
            <label for="end_date" class="form-label">Tanggal Akhir:</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text">
                    <i class="typcn typcn-calendar-outline"></i>
                </span>
                <input type="text" id="end_date" class="form-control fc-datepicker" placeholder="Akhir">
            </div>
        </div>

        <div class="col-md-auto">
            <button id="filter-btn" class="btn btn-primary btn-sm mt-2 rounded">
                <i class="typcn typcn-zoom"></i> Search
            </button>
        </div>
    </div>
</div>

<div class="table-responsive table-card">
    <table id="tabledata" class="table table-striped table-hover table-bordered text-center w-100">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Karyawan</th>
                <th>Companie</th>
                <th>Nama Anak</th>
                <th>Tanggal Pengajuan</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data akan dimuat di sini -->
        </tbody>
    </table>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit mr-2"></i> Data Pengajuan Tabungan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 py-3">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="id_anak">
                    <input type="hidden" id="id_program">
                    <input type="hidden" id="id_valid">
                    <input type="hidden" id="id_karyawan">

                    <!-- Informasi Data Anak -->
                    <div class="form-section mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Nama Anak</label>
                                <input type="text" name="namaanak" id="namaanak" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Tanggal Lahir</label>
                                <input type="text" name="tgl_lahir" id="tgl_lahir" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Orangtua dan Affco -->
                    <div class="form-section mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Nama Orangtua</label>
                                <input type="text" name="namaortu" id="namaortu" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Affco</label>
                                <input type="text" name="affco" id="affco" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Sekolah -->
                    <div class="form-section mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tingkat Sekolah</label>
                                <input type="text" name="tingkatsekolah" id="tingkatsekolah" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Nama Sekolah</label>
                                <input type="text" name="namasekolah" id="namasekolah" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- File Attachments -->
                    <div class="form-section mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label>File Surat Sekolah</label><br>
                                <a class="btn btn-sm btn-outline-info show-file" id="download_surat_sekolah" data-file="" target="_blank">
                                    <i class="fas fa-file-alt mr-1"></i> Lihat File
                                </a>
                            </div>
                            <div class="col-md-6">
                                <label>File FC KTP Karyawan</label><br>
                                <a class="btn btn-sm btn-outline-info show-file" id="download_fc_ktp" data-file="" target="_blank">
                                    <i class="fas fa-id-card mr-1"></i> Lihat File
                                </a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>File FC Raport</label><br>
                                <a class="btn btn-sm btn-outline-info show-file" id="download_fc_raport" data-file="" target="_blank">
                                    <i class="fas fa-book mr-1"></i> Lihat File
                                </a>
                            </div>
                            <div class="col-md-6">
                                <label>File FC Rek Sekolah</label><br>
                                <a class="btn btn-sm btn-outline-info show-file" id="download_fc_rek_skolah" data-file="" target="_blank">
                                    <i class="fas fa-university mr-1"></i> Lihat File
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Note -->
                    <div class="form-section mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Status Approval</label>
                                <select name="status_approval" id="status_approval" class="form-control">
                                    <option value="0">New</option>
                                    <option value="1">Approve</option>
                                    <option value="2">Reject</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Note</label>
                                <input type="text" name="note_input" id="note_input" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="text-right mt-4">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button id="submitBtn" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('assets/master/lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.min.js') }}"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/master/lib/jquery-ui/ui/widgets/datepicker.js') }}"></script>

<script>
    $(document).ready(function () {
        $('.fc-datepicker').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true
        });
        function getDefaultDates() {
            let today = new Date();
            let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            let lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

            // Format MM/DD/YYYY
            let firstDate = $.datepicker.formatDate('mm/dd/yy', firstDay);
            let lastDate = $.datepicker.formatDate('mm/dd/yy', lastDay);

            $('#start_date').val(firstDate);
            $('#end_date').val(lastDate);
        }

        // Terapkan datepicker
        $(".datepicker").datepicker({
            dateFormat: 'mm/dd/yy', // Ubah ke MM/DD/YYYY
            changeMonth: true,
            changeYear: true
        });

        getDefaultDates();

        var table = $('#tabledata').DataTable({
            paging: false,
            lengthChange: false,
            searching: true,
            info: true,
            scrollX: true,
            scrollCollapse: true,
            scrollY: '400px',
            autoWidth: false,
            responsive: false,
            processing: true,
            serverSide: true,
            ordering: true,
            fixedColumns: {
                left: 4
            },
            ajax: {
                url: '{!! url()->current() !!}',
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'anak.karyawan.name', name: 'anak.karyawan.name' },
                { data: 'anak.karyawan.company.name', name: 'anak.karyawan.company.name' },
                { data: 'anak.nama', name: 'anak.nama' },
                { data: 'created_at', name: 'created_at', className: 'text-center' },
                { data: 'status', name: 'status', className: 'text-center',
                    render: function (data) {
                        let statusText = '', statusClass = '';
                        switch (data) {
                            case 0: statusText = 'New'; statusClass = 'badge-primary'; break;
                            case 1: statusText = 'Approved'; statusClass = 'badge-success'; break;
                            case 2: statusText = 'Rejected'; statusClass = 'badge-danger'; break;
                        }
                        return `<span class="badge ${statusClass} p-2 rounded">${statusText}</span>`;
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center', width: '10%' }
            ]
        });

        // Filter ketika tombol ditekan
        $('#filter-btn').click(function () {
            table.ajax.reload();
        });

        // tombol approval
        $('body').on('click', '#edit', function (){
            var id = $(this).data('id')
            console.log(id)
            const userRole = '{{ $user_role }}';
            console.log(userRole)

            // ambil data berdasarkan ID
            $.ajax({
                url: "/validate/" + id + "/edit",
                type: 'GET',
                success: function (response) {
                    var tgl_lahir = new Date(response.anak.tgl_lahir)
                    var formattedDate = tgl_lahir.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                    $('#namaanak').val(response.anak.nama);
                    $('#tgl_lahir').val(formattedDate);
                    $('#status_approval').val(response.status);
                    $("#note_input").val(response.notes);
                    $('#namaortu').val(response.anak.karyawan.name);
                    $('#tingkatsekolah').val(response.anak.program.level);
                    $('#affco').val(response.anak.karyawan.company.name);
                    $('#namasekolah').val(response.anak.nama_sekolah);
                    $('#id_anak').val(response.anak.id);
                    $('#id_program').val(response.anak.program.id);
                    $('#id_karyawan').val(response.anak.karyawan.id);
                    $('#id_valid').val(id);

                    if (response.status == 1) {
                        $('#submitBtn').hide();
                    } else {
                        $('#submitBtn').show().prop('disabled', false);
                    }

                    // Path untuk folder upload
                    let basePath = "/upload/";

                    function setDownloadLink(id, filename) {
                        if (filename){
                            $("#" + id).attr("href", basePath + filename).show();
                        }else{
                            $("#" + id).hide();
                        }
                    }

                    setDownloadLink("download_surat_sekolah", response.anak.surat_sekolah);
                    setDownloadLink("download_fc_ktp", response.anak.fc_ktp);
                    setDownloadLink("download_fc_raport", response.anak.fc_raport);
                    setDownloadLink("download_fc_rek_skolah", response.anak.fc_rek_sekolah);

                    $('#editModal').modal('show');
                }
            })

        })

        // submit approval
        $('#submitBtn').on('click', function(e) {
            e.preventDefault();
            var id = $('#id_valid').val();

            $.ajax({
                url: "/validate/" + id,
                type: "POST",
                data: {
                    _token      :    "{{ csrf_token() }}",
                    _method     :    "PUT",
                    status      :    $('#status_approval').val(),
                    notes       :    $("#note_input").val(),
                    id_anak     :    $('#id_anak').val(),
                    id_karyawan :    $('#id_karyawan').val(),
                    id_program  :    $('#id_program').val()
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Berhasil!", response.message, "success");
                        $('#editModal').modal('hide');
                        $('#tabledata').DataTable().ajax.reload();
                    } else {
                        Swal.fire("Gagal!", response.message);
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Terjadi kesalahan pada server!", "error");
                    console.log(xhr.responseText); // Debugging
                }
            })
        })
    })
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