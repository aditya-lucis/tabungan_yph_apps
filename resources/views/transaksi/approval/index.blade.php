@extends('layout.body')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">

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

</style>
@endsection
@section('content')
<div class="az-content-breadcrumb">
    <span>Tabungan</span>
    <span>Approval Pengajuan</span>
</div>

<div class="mb-3">
  <div class="row align-items-end">
    <div class="col-auto">
      <label for="start_date" class="form-label">Tanggal Awal:</label>
      <div class="input-group input-group-sm">
        <span class="input-group-text">
          <i class="typcn typcn-calendar-outline"></i>
        </span>
        <input type="text" id="start_date" class="form-control fc-datepicker" placeholder="Awal" style="max-width: 140px;">
      </div>
    </div>
    <div class="col-auto">
      <label for="end_date" class="form-label">Tanggal Akhir:</label>
      <div class="input-group input-group-sm">
        <span class="input-group-text">
          <i class="typcn typcn-calendar-outline"></i>
        </span>
        <input type="text" id="end_date" class="form-control fc-datepicker" placeholder="Akhir" style="max-width: 140px;">
      </div>
    </div>
    <div class="col-auto">
      <button id="filter-btn" class="btn btn-primary btn-sm mt-2 rounded"><i class="typcn typcn-zoom"></i> Search</button>
      @if ($user_role == 'adm')
        <button id="btn-excel" class="btn btn-success btn-sm mt-2 rounded"><i class="typcn typcn-download-outline"></i> Export Excel</button>
      @endif
    </div>
  </div>
</div>

<div class="table-responsive table-card">
    <table id="tabledata" class="table table-striped table-hover table-bordered align-middle" style="width:auto;">
        <thead class="table text-center">
            <tr>
                <th style="white-space: nowrap;">No.</th>
                <th style="white-space: nowrap;">Nama Karyawan</th>
                <th style="white-space: nowrap;">Companie</th>
                <th style="white-space: nowrap;">Nama Anak</th>
                <th style="white-space: nowrap;">Tujuan Pencairan</th>
                <th style="white-space: nowrap;">Nominal Yang Diajukan</th>
                <th style="white-space: nowrap;">Tanggal Pengajuan</th>
                <th style="white-space: nowrap;">Tipe Pencairan</th>
                <th style="white-space: nowrap;">Status</th>
                <th style="white-space: nowrap;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Data Pengajuan Pencairan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="id_anak">
                    <input type="hidden" name="id_req" id="id_req">
                    <input type="hidden" id="is_revised_owner" value="0">
                    <input type="hidden" id="owner_karyawan_id">
                    <!-- Informasi Data Anak -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nama Anak:</label>
                                <input type="text" name="namaanak" id="namaanak" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nama Sekolah:</label>
                                <input type="text" name="namasekolah" id="namasekolah" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nama Orangtua:</label>
                                <input type="text" name="namaortu" id="namaortu" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Affco:</label>
                                <input type="text" name="affco" id="affco" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="form-row mt-3">
                            <div class="form-group col-md-12">
                                <label>Rincian Pengajuan Dana</label>
                                <div id="rincianWrapper"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Tabel Saldo (Responsive) -->
                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                        <table id="tablesaldo" class="table table-bordered">
                            <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th class="text-right">Saldo Awal</th>
                                    <th class="text-right">Credit</th>
                                    <th class="text-right">Saldo Berjalan</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Saldo Akhir</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimasukkan di sini -->
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="norek" class="form-label"></label>
                                <div class="form-group">
                                    <label class="form-label">Nomor Rekening Pencairan:</label>
                                    <input type="text" name="norek" id="norek" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                            <label for="file" class="form-label"></label>
                            <div class="form-group">
                                <label class="form-label">File Dokumen Pencairan:</label>
                                <div id="pencairan-display" class="d-flex align-items-center">
                                    <a class="show-file me-3" data-file="" target="_blank">Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-ganti-file" 
                                            data-type="pencairan" style="display:none;">
                                        <i class="typcn typcn-upload"></i> Ganti File
                                    </button>
                                </div>
                                <!-- Form upload akan muncul di sini -->
                                <div id="pencairan-upload" class="mt-2" style="display:none;">
                                    <input type="file" id="new_file_pencairan" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted">Maksimal 5MB</small>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label class="form-label">File Fotocopy Raport:</label>
                                <div id="raport-display" class="d-flex align-items-center">
                                    <a class="show-file-raport me-3" data-file="" target="_blank">Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-ganti-file" 
                                            data-type="raport" style="display:none;">
                                        <i class="typcn typcn-upload"></i> Ganti File
                                    </button>
                                </div>
                                <!-- Form upload akan muncul di sini -->
                                <div id="raport-upload" class="mt-2" style="display:none;">
                                    <input type="file" id="new_file_raport" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Maksimal 5MB</small>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="bankname"></label>
                                <div class="form-group">
                                    <label class="form-label">Bank Pencairan</label>
                                    <input type="text" name="bankname" id="bankname" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="accountbankname"></label>
                                <div class="form-group">
                                    <label class="form-label">Nama Pemilik Bank Pencairan</label>
                                    <input type="text" name="accountbankname" id="accountbankname" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="status_approval" class="form-label">Status Approval</label>
                                <select name="status_approval" id="status_approval" class="form-control">
                                    <option value="0">New</option>
                                    <option value="1">Approve</option>
                                    <option value="2">Reject</option>
                                    <option value="3">Awaiting</option>
                                    <option value="4">Revised</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Deskripsi Pembayaran:</label>
                                    <input type="text" name="reason" id="reason" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="loghistory" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="loghistorysel" class="form-label">Status Dokumen</label>
                                    <select name="loghistorysel" id="loghistorysel" class="form-control">
                                        <option value="">Select</option>
                                        <option value="On Proses Verifikasi Dokumen">On Proses Verifikasi Dokumen</option>
                                        <option value="Waiting Approval Dewan Pengawas">Waiting Approval Dewan Pengawas</option>
                                        <option value="Waiting Approval Dewan Pembina">Waiting Approval Dewan Pembina</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="nominal" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nominal Yang Disetujui:</label>
                                    <input type="text" name="nominal_input" id="nominal_input" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Note:</label>
                                    <input type="text" name="note_input" id="note_input" class="form-control" placeholder="Pengajuan Pencairan Tahap: ">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="cancelreason" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label id="reasonLabel" class="form-label">Alasan Ditolak:</label>
                                    <input type="text" name="note_reject" id="note_reject" class="form-control" placeholder="Alasan Ditolak: ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seeLogApproveHistory" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Log History Status Pengajuan Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table id="tablogsavings" class="table table-bordered">
                        <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <td>No. </td>
                                <td>Status</td>
                                <td>Descript</td>
                                <td>Date</td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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

    $(document).ready(function () {
        // Terapkan datepicker
        $(".datepicker").datepicker({
            dateFormat: 'mm/dd/yy', // Ubah ke MM/DD/YYYY
            changeMonth: true,
            changeYear: true
        });

        getDefaultDates();
    });



    var table = $('#tabledata').DataTable({
        paging: false,
        lengthChange: false,
        searching: true,
        info: true,
        scrollX: true,
        scrollCollapse: true,
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
            { data: 'reason', name: 'reason' },
            { data: 'nominal', name: 'nominal', className: 'text-end',
                render: function(data) {
                    return new Intl.NumberFormat('en-EN', { minimumFractionDigits: 2 }).format(data);
                }
            },
            { data: 'created_at', name: 'created_at', className: 'text-center' },
            { data: 'isreimburst', name: 'isreimburst', className: 'text-center',
                render: function (data) {
                    return data == 1 ? 'Reimburse' : 'Cash Advance';
                }
            },
            { data: 'status', name: 'status', className: 'text-center',
                render: function (data, type, row) {
                    let statusText = '', statusClass = '';
                    switch (data) {
                        case 0: statusText = 'New'; statusClass = 'badge-primary'; break;
                        case 1: statusText = 'Approved'; statusClass = 'badge-success'; break;
                        case 2: statusText = 'Rejected'; statusClass = 'badge-danger'; break;
                        case 3: statusText = 'Awaiting'; statusClass = 'badge-info'; break;
                    }
                    return `<a id="see-log" data-id="${row.id}" class="badge ${statusClass} p-2 rounded">${statusText}</a>`;
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center', width: '10%' }
        ]
    });

    // Filter ketika tombol ditekan
    $('#filter-btn').click(function () {
        table.ajax.reload();
    });
});

    // tombol log history approval
    $('body').on('click', '#see-log', function () {
        var id = $(this).data('id');
        $.ajax({
            url: `/tabungan/loghistory/${id}`,
            type: 'GET',
            success: function (response) {
                $('#seeLogApproveHistory').modal('show')
                $('#tablogsavings tbody').empty()

                if (response.length > 0) {
                    $.each(response, function (index, approval) {
                        let statusText = '';
                        let statusClass = '';

                        switch (approval.status) {
                            case 1:
                                statusText = 'Approved'
                                statusClass = 'badge bg-success text-white'
                                break
                            case 2:
                                statusText = 'Rejected';
                                statusClass = 'badge bg-danger text-white'
                                break;
                            case 3:
                                statusText = 'Awaiting';
                                statusClass = 'badge bg-info text-white'
                                break;
                            default:
                                statusText = 'Unknown';
                                statusClass = 'badge bg-secondary text-white'
                                break
                        }

                        let formattedDate = new Date(approval.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        })

                        $('#tablogsavings tbody').append(`
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td><span class="badge ${statusClass} p-2 rounded">${statusText}</span></td>
                                <td>${approval.descript ?? '-'}</td>
                                <td>${formattedDate}</td>
                            </tr>
                        `)
                    })
                }else{
                    $('#tablogsavings tbody').append(`
                        <tr>
                            <td colspan="4" class="text-center">No Record</td>
                        </tr>
                    `)
                }
            }
        })
    })

    // tombol approval
    $('body').on('click', '#edit', function () {
        var approvalId = $(this).data('id')
        const userRole = '{{ $user_role }}';
        const userEmployeeId = '{{ $id_employee }}';

        // ambil data berdasarkan ID
        $.ajax({
            url: "/tabungan/inbox/" + approvalId,
            type: 'GET',
            success: function (response) {
                $('#id_req').val(response.id);
                $('#id_anak').val(response.anak.id);
                $('#namaanak').val(response.anak.nama);
                $('#namasekolah').val(response.anak.nama_sekolah);
                $('#namaortu').val(response.anak.karyawan.name);
                $('#affco').val(response.anak.karyawan.company.name);
                $('#status_approval').val(response.status);
                $('#reason').val(response.reason);
                let formattedTotal = formatNumber(response.nominal);
                $('#nominal_input').val(formattedTotal);
                $('#rincianWrapper').empty()
                totalajuan = 0
                rincianText =''

                $('#owner_karyawan_id').val(response.anak.karyawan.id || 0);

                const isOwnerRevised = (
                    response.status == 4 && 
                    userRole === 'krw' && 
                    parseInt(userEmployeeId) === parseInt(response.anak.karyawan.id || 0)
                );

                $('#norek, #bankname, #accountbankname').prop('readonly', true);
                $('#reason').prop('readonly', false);

                // Hide semua tombol ganti file
                $('.btn-ganti-file').hide();
                $('#pencairan-upload, #raport-upload').hide();
                $('#pencairan-display, #raport-display').show();

                if (isOwnerRevised) {
                    $('#norek, #bankname, #accountbankname').prop('readonly', false);
                    $('.btn-ganti-file').show();
                }

                if (response.norek != null && response.norek != '') {
                    $('#norek').val(response.norek);
                }else{
                    $('#norek').val("Tidak ada rekening");
                }
                
                if (response.bankname != null && response.bankname != '') {
                    $('#bankname').val(response.bankname);
                }else{
                    $('#bankname').val("Tidak ada Bank Pencairan");
                }
                
                if (response.accountbankname != null && response.accountbankname != '') {
                    $('#accountbankname').val(response.accountbankname);
                }else{
                    $('#accountbankname').val("Tidak ada Nama Pemilik Rekening Bank");
                }

                $('#is_revised_owner').val(isOwnerRevised ? 1 : 0);
                
                if (response.reqpprovaldetail && response.reqpprovaldetail.length > 0) {
                    if (isOwnerRevised) {
                        response.reqpprovaldetail.forEach(function (item) {
                            totalajuan += parseFloat(item.nominal || 0);

                            $('#rincianWrapper').append(`
                                <div class="form-row rincian-item mb-2 editable-row">
                                    <div class="col-md-7">
                                        <input type="text" class="form-control rincian-text" 
                                            value="${item.rincian}" placeholder="Deskripsi rincian">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control text-right rincian-nominal" 
                                            value="${parseFloat(item.nominal).toLocaleString('en-EN')}" placeholder="0">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-rincian">
                                            <i class="typcn typcn-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `);
                        });

                        // Tombol Tambah Rincian
                        $('#rincianWrapper').append(`
                            <button type="button" id="btn-tambah-rincian" class="btn btn-sm btn-success mb-3">
                                <i class="typcn typcn-plus"></i> Tambah Rincian
                            </button>
                        `);

                    } else {
                        // ==================== MODE READONLY ====================
                        let jumlahItem = response.reqpprovaldetail.length;
                        response.reqpprovaldetail.forEach(function (item, index) {
                            let formattedNominal = parseFloat(item.nominal).toLocaleString('en-EN');
                            let rincianText = (jumlahItem > 1) ? `${index + 1}. ${item.rincian}` : item.rincian;

                            totalajuan += parseFloat(item.nominal || 0);

                            $('#rincianWrapper').append(`
                                <div class="form-row rincian-item mb-2">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" value="${rincianText}" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control text-right" value="${formattedNominal}" readonly>
                                    </div>
                                </div>
                            `);
                        });
                    }

                    // Baris TOTAL (selalu ditampilkan)
                    let formattedTotal = totalajuan.toLocaleString('en-EN');
                    $('#rincianWrapper').append(`
                        <div class="form-row rincian-item mb-2 total-row">
                            <div class="col-md-8">
                                <input type="text" class="form-control font-weight-bold" value="Total:" readonly>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="total_rincian" class="form-control text-right font-weight-bold" 
                                    value="${formattedTotal}" readonly>
                            </div>
                        </div>
                    `);
                }else{
                    $('#rincianWrapper').append(`
                        <p class="text-muted">Tidak ada rincian pengajuan dana.</p>
                    `)
                }

                // Perbarui data-file pada elemen <a class="show-file">
                if (response.file && response.file.trim() !== "") {
                    $('.show-file')
                        .attr('data-file', response.file)
                        .attr('href', "/upload/" + response.file) // Set langsung href untuk download
                        .attr('target', '_blank') // Tambahkan atribut download
                        .text('Download File');
                } else {
                    $('.show-file')
                        .attr('data-file', '')
                        .removeAttr('href download') // Hapus href jika tidak ada file
                        .text('File Tidak Tersedia');
                }
                
                if (response.anak.fc_raport && response.anak.fc_raport.trim() !== "") {
                    $('.show-file-raport')
                        .attr('data-file', response.anak.fc_raport)
                        .attr('href', "/upload/" + response.anak.fc_raport) // Set langsung href untuk download
                        .attr('target', '_blank') // Tambahkan atribut download
                        .text('Download File');
                } else {
                    $('.show-file-raport')
                        .attr('data-file', '')
                        .removeAttr('href download') // Hapus href jika tidak ada file
                        .text('File Tidak Tersedia');
                }

                // Bersihkan tabel saldo sebelum menambahkan data baru
                 $('#tablesaldo tbody').empty();

                    // Periksa apakah ada transaksi
                    if (response.anak.transaction.length > 0) {

                        response.anak.transaction.sort(function(a, b) {
                            return new Date(a.created_at) - new Date(b.created_at);
                        });

                        $.each(response.anak.transaction, function (index, transaction) {
                            $('#tablesaldo tbody').append(`
                                <tr>
                                    <td class="text-right">${transaction.previous_balance.toLocaleString()}</td>
                                    <td class="text-right">${transaction.credit.toLocaleString()}</td>
                                    <td class="text-right">${transaction.running_balance.toLocaleString()}</td>
                                    <td class="text-right">${transaction.debit.toLocaleString()}</td>
                                    <td class="text-right">${transaction.final_balance.toLocaleString()}</td>
                                    <td>${transaction.notes}</td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#tablesaldo tbody').append(`
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada transaksi</td>
                            </tr>
                        `);
                    }

                    // Cek jika role karyawan dan punya id_employee, sembunyikan dan disable tombol
                    if (userRole === 'krw' && userEmployeeId) {
                        if (isOwnerRevised) {
                            // ← KARYAWAN PEMILIK + REVISED → BOLEH SUBMIT
                            $('#submitBtn').show().prop('disabled', false);
                            $('#status_approval').prop('disabled', true);   // tidak boleh ubah status
                            $('#note_reject').prop('disabled', true);      // boleh edit alasan revisi
                        } else {
                            // karyawan biasa / bukan pemilik → tidak boleh apa-apa
                            $('#submitBtn').hide().prop('disabled', true);
                            $('#status_approval').prop('disabled', true);
                            $('#note_reject').prop('disabled', true);
                        }

                        if (response.status == 3) {
                            $("#loghistorysel").prop('disabled', true);
                        }

                    } else {
                        // Kalau bukan krw, cek status
                        if (response.status == 0 || response.status == 3) {
                            $('#submitBtn').show().prop('disabled', false);
                        } else {
                            $('#submitBtn').hide();
                        }
                    }

                    if (response.status == 1) {
                        $("#nominal").show()
                        $("#loghistory").hide()
                        $("#cancelreason").hide()
                        let formattedTotal = formatNumber(response.nominal);
                        let formattedApprove = formatNumber(response.nominalapprove);
                        $("#nominal_input").val(formattedApprove);
                        $("#note_input").val(response.notes);
                    }else if(response.status == 2){
                        $("#cancelreason").show()
                        $("#note_reject").val(response.notes);
                        $("#nominal").hide()
                        $("#loghistory").hide()
                        $("#reasonLabel").text("Alasan Ditolak:")
                        $("#note_reject").attr("placeholder", "Alasan Ditolak: ")
                    }else if (response.status == 3){
                        $("#loghistory").show()
                        $("#cancelreason").hide()
                        $("#nominal").hide()
                        $("#loghistorysel").val(response.latestreqpprovallog.descript)
                    }else if (response.status == 4){
                        $("#cancelreason").show()
                        $("#note_reject").val(response.notes);
                        $("#nominal").hide()
                        $("#loghistory").hide()
                        $("#reasonLabel").text("Apa Yang Perlu Direvisi")
                        $("#note_reject").attr("placeholder", "Apa Yang Perlu Direvisi: ")
                    }else{
                        $("#cancelreason").hide()
                        $("#nominal").hide()
                        $("#loghistory").hide()
                    }

                $('#editModal').modal('show');

                // Bersihkan tabel saldo sebelum menambahkan data baru
            }
        })
    })

    $(document).ready(function () {
        // Saat modal ditutup
        $('#editModal').on('hidden.bs.modal', function () {
            $("#nominal").hide(); // Sembunyikan div nominal
            $("#nominal_input").val("").prop("required", false); // Kosongkan input nominal
            $("#note_input").val("").prop("required", false); // Kosongkan input note
            $("#status_approval").val("0").trigger("change"); // Reset dropdown status ke 'New'
        });

        // Saat status approval berubah
        $("#status_approval").change(function () {
            let status = $(this).val()
            let nominalDiv = $("#nominal")
            let nominalInput = $("#nominal_input")
            let noteInput = $("#note_input")
            let loghistory = $("#loghistory")
            let cancelreason = $("#cancelreason")

            if (status === "1") {
                nominalDiv.show()
                loghistory.hide()
                cancelreason.hide()
                nominalInput.prop("required", true)
                noteInput.prop("required", true)
            } else if (status === "3") {
                loghistory.show()
                nominalDiv.hide()
                cancelreason.hide()
                nominalInput.prop("required", false).val("")
                noteInput.prop("required", false).val("")
            } 
            // === BAGIAN BARU: Reject & Revised pakai 1 div ===
            else if (status === "2" || status === "4") {
                cancelreason.show()
                nominalDiv.hide()
                loghistory.hide()
                nominalInput.prop("required", false).val("")
                noteInput.prop("required", false).val("")

                if (status === "2") {
                    $("#reasonLabel").text("Alasan Ditolak:")
                    $("#note_reject").attr("placeholder", "Alasan Ditolak: ")
                } else { // Revised
                    $("#reasonLabel").text("Apa Yang Perlu Direvisi")
                    $("#note_reject").attr("placeholder", "Apa Yang Perlu Direvisi: ")
                }
            } 
            else {
                cancelreason.hide()
                nominalDiv.hide()
                loghistory.hide()
                nominalInput.prop("required", false).val("")
                noteInput.prop("required", false).val("")
            }
        });
    });

    $('#nominal_input').maskNumber({integer: true});

     function cleanNumber(value) {
        if (!value) return "0"; // Jika kosong, kembalikan "0"
        return value.replace(/[^\d]/g, ''); // Hapus semua karakter selain angka
    }
    
    function formatNumber(value) {
        return parseInt(value).toLocaleString('en-EN'); // Format ribuan, hapus desimal
    }
    
    function cleanNumber(value) {
        if (!value) return "0"; // Jika kosong, kembalikan "0"
        return value.replace(/[^\d]/g, ''); // Hapus semua karakter selain angka
    }

    function calculateTotalRincian() {
        let total = 0;
        $('.rincian-nominal').each(function () {
            let val = cleanNumber($(this).val()) || 0;
            total += parseFloat(val);
        });
        $('#total_rincian').val(total.toLocaleString('en-EN'));
    }

    $('body').on('click', '#btn-tambah-rincian', function () {
        let newRow = `
            <div class="form-row rincian-item mb-2 editable-row">
                <div class="col-md-7">
                    <input type="text" class="form-control rincian-text" placeholder="Deskripsi rincian">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control text-right rincian-nominal" placeholder="0">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-rincian">
                        <i class="typcn typcn-trash"></i>
                    </button>
                </div>
            </div>`;
        
        $('.total-row').before(newRow);
    
        // Apply mask ke nominal baru
        $('.rincian-nominal:last').maskNumber({integer: true});
    })

    // Hapus baris
    $('body').on('click', '.btn-remove-rincian', function () {
        $(this).closest('.editable-row').remove();
        calculateTotalRincian();
    });

    // Otomatis hitung ulang total saat nominal diubah
    $('body').on('input', '.rincian-nominal', function () {
        calculateTotalRincian();
    });

    $('#editForm').submit(function(e){
        e.preventDefault();

        var anak_id       = $('#id_anak').val();
        var req_id        = $('#id_req').val();
        var status_approve = $('#status_approval').val();
        var note          = $('#reason').val();           // deskripsi pembayaran
        var nominal       = cleanNumber($('#nominal_input').val());
        var notes         = "";

        if (status_approve == 1) {
            notes = $('#note_input').val();
        } else if (status_approve == 2 || status_approve == 4) {
            notes = $('#note_reject').val();
        }

        // Cek apakah ini karyawan pemilik + Revised
        const isRevisedOwner = $('#is_revised_owner').val() == 1;

        let url = isRevisedOwner 
            ? "/tabungan/inbox/revised/" + req_id 
            : "/tabungan/inbox/update/" + req_id;

        let dataToSend;

        if (isRevisedOwner) {
            // ==================== PAKAI FormData (karena ada file + rincian) ====================
            dataToSend = new FormData();

            dataToSend.append('_token', "{{ csrf_token() }}");
            dataToSend.append('_method', "PUT");
            dataToSend.append('id_anak', anak_id);
            dataToSend.append('id_req', req_id);
            dataToSend.append('status', status_approve);
            dataToSend.append('notes', note);                    // reason
            dataToSend.append('norek', $('#norek').val());
            dataToSend.append('bankname', $('#bankname').val());
            dataToSend.append('accountbankname', $('#accountbankname').val());
            dataToSend.append('nominal_input', nominal);
            dataToSend.append('note_input', notes);

            // Rincian yang diedit karyawan
            let index = 0;
            $('.editable-row').each(function () {
                let desc = $(this).find('.rincian-text').val().trim();
                let nom  = cleanNumber($(this).find('.rincian-nominal').val());

                if (desc && nom > 0) {
                    dataToSend.append(`rincian_details[${index}][rincian]`, desc);
                    dataToSend.append(`rincian_details[${index}][nominal]`, nom);
                    index++;
                }
            });

            // File baru (kalau dipilih)
            if ($('#new_file_pencairan')[0].files.length > 0) {
                dataToSend.append('new_file_pencairan', $('#new_file_pencairan')[0].files[0]);
            }
            if ($('#new_file_raport')[0].files.length > 0) {
                dataToSend.append('new_file_raport', $('#new_file_raport')[0].files[0]);
            }

            // Setting AJAX untuk FormData
            $.ajax({
                url: url,
                type: "POST",           // penting! FormData harus pakai POST
                data: dataToSend,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: response.message || "Revisi berhasil disimpan.",
                        icon: "success"
                    });
                    $('#editModal').modal('hide');
                    $('#tabledata').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        title: "Error!",
                        text: xhr.responseJSON?.message || "Terjadi kesalahan!",
                        icon: "error"
                    });
                }
            });

        } else {
            // ==================== ADMIN / UPDATE BIASA (pakai object biasa) ====================
            dataToSend = {
                _token        : "{{ csrf_token() }}",
                _method       : "PUT",
                id_anak       : anak_id,
                id_req        : req_id,
                status        : status_approve,
                notes         : note,
                nominal_input : nominal,
                note_input    : notes
            };

            if ($('#loghistorysel').length) {
                dataToSend.loghistorysel = $('#loghistorysel').val();
            }

            $.ajax({
                url: url,
                type: "PUT",
                data: dataToSend,
                success: function(response) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Data berhasil diupdate!",
                        icon: "success"
                    });
                    $('#editModal').modal('hide');
                    $('#tabledata').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        title: "Error!",
                        text: xhr.responseJSON?.message || "Terjadi kesalahan!",
                        icon: "error"
                    });
                }
            });
        }
    });

    $('#btn-excel').on('click', function () {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        let url = "{{ route('approval.export') }}";
        let params = [];

        if (startDate) params.push('start_date=' + encodeURIComponent(startDate));
        if (endDate) params.push('end_date=' + encodeURIComponent(endDate));

        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        window.location.href = url;
    });

    // Tombol Ganti File → ganti tampilan jadi form upload
    $('body').on('click', '.btn-ganti-file', function () {
        const type = $(this).data('type');

        if (type === 'pencairan') {
            $('#pencairan-display').hide();
            $('#pencairan-upload').show();
        } else if (type === 'raport') {
            $('#raport-display').hide();
            $('#raport-upload').show();
        }
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