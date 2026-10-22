@extends('layout.body')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">

<style>
    .filter-card {
        background-color: var(--yph-card);
        padding: 20px;
        border-radius: var(--yph-radius);
        box-shadow: var(--yph-shadow);
        border: 1px solid var(--yph-border);
        margin-bottom: 20px;
    }
    .table-container-card {
        background-color: var(--yph-card);
        padding: 20px;
        border-radius: var(--yph-radius);
        box-shadow: var(--yph-shadow);
        border: 1px solid var(--yph-border);
        width: 100%;
    }
    .table th, .table td {
        vertical-align: middle;
        white-space: nowrap;
    }
    .input-group-text {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        border-radius: 10px 0 0 10px !important;
        color: #64748b;
    }
    .form-control {
        border-color: #cbd5e1;
        border-radius: 0 10px 10px 0 !important;
    }
    .form-control:focus {
        border-color: var(--yph-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .form-section label {
        font-weight: 600;
        font-size: 13px;
        color: #475569;
        margin-bottom: 4px;
    }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <div class="az-content-breadcrumb mb-1">
            <span>Tabungan</span>
            <span>Approval Pendaftaran</span>
        </div>
        <h4 class="mb-0" style="font-weight: 800; color: #0f172a;">Approval Pendaftaran</h4>
    </div>
</div>

<div class="filter-card">
    <div class="row g-3 align-items-end">
        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
            <label for="start_date" class="form-label" style="font-weight: 600; font-size: 13px; color: #475569;">Tanggal Awal:</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="typcn typcn-calendar-outline"></i>
                </span>
                <input type="text" id="start_date" class="form-control fc-datepicker" placeholder="YYYY-MM-DD">
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
            <label for="end_date" class="form-label" style="font-weight: 600; font-size: 13px; color: #475569;">Tanggal Akhir:</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="typcn typcn-calendar-outline"></i>
                </span>
                <input type="text" id="end_date" class="form-control fc-datepicker" placeholder="YYYY-MM-DD">
            </div>
        </div>

        <div class="col-md-4 col-sm-12">
            <button id="filter-btn" class="btn btn-fintech-primary w-100" style="min-height: 40px; padding: 8px 16px;">
                <i class="typcn typcn-zoom"></i> Filter Data
            </button>
        </div>
    </div>
</div>

<div class="table-container-card">
    <div class="table-responsive">
        <table id="tabledata" class="table table-striped table-hover text-center w-100 mb-0">
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
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header text-white" style="background: var(--yph-primary-gradient);">
                <h5 class="modal-title font-weight-bold" id="editModalLabel">
                    <i class="fas fa-edit mr-2"></i> Data Pengajuan Tabungan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
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

        const userRole = "{{ Auth::user()->role }}"

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
            fixedColumns: $(window).width() >= 992 ? { left: 2 } : false,
            ajax: {
                url: '{!! url()->current() !!}',
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { 
                    data: 'anak.karyawan.name',
                    name: 'anak.karyawan.name',

                    render: function (data, type, row) {
                        if (userRole === 'adm') {
                            const employeeId = row.anak.karyawan.id

                            return `
                            <a href="/employee/${employeeId}" style="text-decoration:none; color:inherit; cursor:pointer;">
                                ${data}
                            </a>
                            `
                        }
                    }
                },
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