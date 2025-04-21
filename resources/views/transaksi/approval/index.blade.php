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
                    <input type="hidden" name="id_req" id="id_req" class="form-control" readonly>
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
                                    <!-- <input type="text" name="norek" id="norek" class="form-control" readonly> -->
                                    <label class="form-label"><a class="show-file" data-file="">Lihat File</a></label>
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
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Reason:</label>
                                    <input type="text" name="reason" id="reason" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="nominal" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nominal Yang Diajukan:</label>
                                    <input type="text" name="nominal_input" id="nominal_input" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Note:</label>
                                    <input type="text" name="note_input" id="note_input" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="nominalapprove" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nominal Yang Disetujui</label>
                                    <input type="text" name="nominal_setuju" id="nominal_setuju" class="form-control" readonly>
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
            { data: 'reason', name: 'reason' },
            { data: 'nominal', name: 'nominal', className: 'text-end',
                render: function(data) {
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(data);
                }
            },
            { data: 'created_at', name: 'created_at', className: 'text-center' },
            { data: 'isreimburst', name: 'isreimburst', className: 'text-center',
                render: function (data) {
                    return data == 1 ? 'Reimburse' : 'Cash Advance';
                }
            },
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
});


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

                // Perbarui data-file pada elemen <a class="show-file">
                if (response.file && response.file.trim() !== "") {
                    $('.show-file')
                        .attr('data-file', response.file)
                        .attr('href', "/upload/" + response.file) // Set langsung href untuk download
                        .attr('download', response.file) // Tambahkan atribut download
                        .text('Download File');
                } else {
                    $('.show-file')
                        .attr('data-file', '')
                        .removeAttr('href download') // Hapus href jika tidak ada file
                        .text('File Tidak Tersedia');
                }

                // Bersihkan tabel saldo sebelum menambahkan data baru
                 $('#tablesaldo tbody').empty();

                    // Periksa apakah ada transaksi
                    if (response.anak.transaction.length > 0) {
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
                        $('#submitBtn').hide().prop('disabled', true);
                        $('#status_approval').prop('disabled', true);
                    } else {
                        // Kalau bukan krw, cek status
                        if (response.status != 0) {
                            $('#submitBtn').hide();
                        } else {
                            $('#submitBtn').show().prop('disabled', false);
                        }
                    }

                    if (response.status == 1) {
                        $("#nominal").show()
                        $("#nominalapprove").show()
                        let formattedTotal = formatNumber(response.nominal);
                        let formattedApprove = formatNumber(response.nominalapprove);
                        $("#nominal_input").val(formattedTotal);
                        $("#note_input").val(response.notes);
                        $("#nominal_setuju").val(formattedApprove);
                    }else{
                        $("#nominal").hide()
                        $("#nominalapprove").hide()
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
            let status = $(this).val();
            let nominalDiv = $("#nominal");
            let nominalInput = $("#nominal_input");
            let noteInput = $("#note_input");

            if (status === "1") { // Jika pilih "Approve"
                nominalDiv.show();
                nominalInput.prop("required", true);
                noteInput.prop("required", true);
            } else { // Jika pilih "Reject" atau lainnya
                nominalDiv.hide();
                nominalInput.prop("required", false).val(""); // Kosongkan field
                noteInput.prop("required", false).val("");
            }
        });
    });

    $('#nominal_input').maskNumber({integer: true});

     function cleanNumber(value) {
        if (!value) return "0"; // Jika kosong, kembalikan "0"
        return value.replace(/[^\d]/g, ''); // Hapus semua karakter selain angka
    }
    
    function formatNumber(value) {
        return parseInt(value).toLocaleString('id-ID'); // Format ribuan, hapus desimal
    }
    
    function cleanNumber(value) {
        if (!value) return "0"; // Jika kosong, kembalikan "0"
        return value.replace(/[^\d]/g, ''); // Hapus semua karakter selain angka
    }

    $('#editForm').submit(function(e){
        e.preventDefault();
        var anak_id = $('#id_anak').val();
        var req_id = $('#id_req').val();
        var status_approve = $('#status_approval').val();
        var note = $('#reason').val();
        var nominal = cleanNumber($('#nominal_input').val());
        var notes = $('#note_input').val();
        $.ajax({
            url: "/tabungan/inbox/update/" + req_id,
            type: "PUT", // Gunakan POST
            data: {
                _token          : "{{ csrf_token() }}",
                _method         : "PUT", // Laravel mengenali ini sebagai PUT
                id_anak         : anak_id,
                id_req          : req_id,
                status          : status_approve,
                notes           : note,
                nominal_input   : nominal,
                note_input      : notes
            },
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
                    text: "Terjadi kesalahan pada server!",
                    icon: "error"
                });
                console.log(xhr.responseText); // Debugging
            }
        });
    })

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