@extends('layout.body')
@section('css')
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
    #tableanak {
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

    .custom-btn {
        background-color: transparent;  /* Awalnya transparan */
        border: none; /* Hilangkan border */
        color: black; /* Warna teks hitam */
        transition: background-color 0.3s, color 0.3s; /* Efek transisi */
    }

    .custom-btn:focus, 
    .custom-btn:active {
        background-color: blue !important; /* Warna biru saat diklik */
        color: white !important; /* Teks berubah putih saat diklik */
        border: none; /* Pastikan tetap tanpa border */
    }
    .ui-datepicker {
        z-index: 1051 !important;
    }


</style>
@endsection
@section('content')

@php
    date_default_timezone_set('Asia/Jakarta'); // Set zona waktu ke WIB
    $hour = date('H');

    if ($hour >= 5 && $hour < 11) {
        $greeting = 'Selamat Pagi';
    } elseif ($hour >= 11 && $hour < 15) {
        $greeting = 'Selamat Siang';
    } elseif ($hour >= 15 && $hour < 18) {
        $greeting = 'Selamat Sore';
    } else {
        $greeting = 'Selamat Malam';
    }
@endphp

<div class="az-content-label mg-b-5">{{ $greeting }}, {{$employee->name}}  </div>
<br>
<div class="az-content-breadcrumb">
    <!-- <span>tambah Pengajuan</span> -->
    
    @if(count($dataanak) < 2)
        <button type="button" class="btn btn-sm btn-outline-light custom-btn" id="btn-add" data-id="{{ Crypt::encryptString($employee->id) }}">
            <i class="typcn typcn-document-add"></i> Tambah Pendaftaran Baru
        </button>
    @endif


</div>

@if($dataanak->isEmpty())
    <div class="table-card text-center">
        <p class="text-muted">Anda belum mengajukan tabungan pendidikan</p>
    </div>
@else
    @foreach ($dataanak as $anak)
        <div class="table-card">
            <div class="row grid-margin">
                <div class="col">
                    <div class="col-12 position-relative">
                        <a id="logsavings"
                            class="btn btn-sm btn-outline-info custom-btn btn-rounded-3"
                            style="font-size: 14px;"
                            data-id="{{ $anak->id }}">
                            <b>{{ $anak->nama }}</b>
                        </a>

                        @php
                            $saldo = $anak->latestTransaction;
                        @endphp

                        @if ($saldo)
                            <a id="reqApprove"
                                data-id="{{ $anak->id }}"
                                class="btn btn-sm btn-outline-success custom-btn btn-rounded-3"
                                style="top: 10px; right: 10px; padding: 8px 12px; font-size: 13px;">
                                Ajukan Pencairan
                            </a>
                        @endif

                        @php
                            $latestApproval = $anak->approval->last();
                        @endphp

                        {{-- Tombol Hapus hanya untuk admin --}}
                        @if($latestApproval && $latestApproval->status == 2 && auth()->user()->role == 'adm')
                            <a id="delete"
                                data-id="{{ $anak->id }}"
                                class="btn btn-outline-danger custom-btn btn-rounded-3 position-absolute"
                                style="top: 10px; right: 50px; padding: 8px 12px; font-size: 16px;">
                                <i class="typcn typcn-trash" style="font-size: 18px;"></i>
                            </a>
                        @endif

                        {{-- Tombol Edit --}}
                        <a id="edit"
                            data-id="{{ $anak->id }}"
                            class="btn btn-outline-warning custom-btn btn-rounded-3 position-absolute"
                            style="top: 10px; right: 10px; padding: 8px 12px; font-size: 16px;">
                            <i class="typcn typcn-pencil" style="font-size: 18px;"></i>
                        </a>
                    </div>
                    <br>
                    <table id="tableanak" class="table">
                        <thead>
                            <tr>
                                <th class="text-align: right;">Saldo Awal</th>
                                <th class="text-align: right;">Credit</th>
                                <th class="text-align: right;">Saldo Berjalan</th>
                                <th class="text-align: right;">Debit</th>
                                <th class="text-align: right;">Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr>
                                    <td class="text-align: right;">{{ number_format($anak->latestTransaction->previous_balance ?? 0, 2) }}</td>
                                    <td class="text-align: right;">{{ number_format($anak->latestTransaction->credit ?? 0, 2) }}</td>
                                    <td class="text-align: right;">{{ number_format($anak->latestTransaction->running_balance ?? 0, 2) }}</td>
                                    <td class="text-align: right;">{{ number_format($anak->latestTransaction->debit ?? 0, 2) }}</td>
                                    <td class="text-align: right;">{{ number_format($anak->latestTransaction->final_balance ?? 0, 2) }}</td>
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <br>
    @endforeach
@endif

<!-- Modal Ajukan Approval -->
<div id="reqApproveModal" class="modal">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-content-demo">
        <br>
        <form id="form-accept">
          @csrf
          <input type="hidden" name="id_anak" id="id_anak">
            <div class="modal-body">
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input type="checkbox" id="confirmCheck" class="form-check-input">
                <label for="confirmCheck" class="form-check-label">
                    I confirm that I have read and accept the terms and conditions and privacy policy.
                </label>
            </div>
            <button type="button" data-dismiss="modal" class="btn btn-outline-light">Cancel</button>
            <button type="button" id="saveChangesBtn" class="btn btn-indigo" disabled>Accept</button>
          </div>
          </form>
        </div>
    </div><!-- modal-dialog -->
</div><!-- modal -->

<!-- Modal Edit Data Anak -->
<div id="editAnak" class="modal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Detail Data Anak</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-edit">
                    @csrf
                    <h5 class="modal-title bg-success text-white p-2 rounded" id="editModalLabel">Biodata Anak</h5>
                    <br>
                    <p style="color: red;">*) File yang boleh di upload hanya file berbentuk gambar atau PDF</p>
                    <br>
                    <div class="row row-xs">
                        <div class="col-md-6">
                            <label for="namaanak">Nama Anak</label>
                            <input type="hidden" name="idanak" id="idanak">
                            <input type="text" name="namaanak" id="namaanak" class="form-control" placeholder="Nama Anak">
                        </div>
                        <div class="col-md-6">
                            <label>Surat Keterangan Sekolah</label>
                            <!-- <input type="file" class="form-control" name="surat_sekolah" id="surat_sekolah"> -->
                            <div class="input-group">
                                <input type="file" class="form-control col-md-9" name="surat_sekolah" id="surat_sekolah">
                                <a href="#" id="download_surat_sekolah" class="btn btn-sm btn-success col-md-3" target="_blank" style="display: none;">
                                    <i class="typcn typcn-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row row-xs">
                        <div class="col-md-6">
                            <label for="jenjang">Jenjang Pendidikan</label>
                            @if (auth()->user()->role != 'krw')
                                <select name="id_program" id="id_program" class="form-control">
                                    <option value="">Pilih Jenjang Pendidikan</option>
                                    @foreach($program as $prog)
                                        <option value="{{ $prog->id }}">{{ $prog->level }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" name="text_program" id="text_program" class="form-control" placeholder="Jenjang Pendidikan" readonly>
                                <input type="hidden" name="id_program" id="id_program">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>FC KTP Karyawan</label>
                            <div class="input-group">
                                <input type="file" class="form-control col-md-9" name="fc_ktp" id="fc_ktp">
                                <a href="#" id="download_fc_ktp" class="btn btn-sm btn-success col-md-3" target="_blank" style="display: none;">
                                    <i class="typcn typcn-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row row-xs">
                        <div class="col-md-6">
                            <label for="namasekolah">Nama Sekolah</label>
                            <input type="text" name="namasekolah" id="namasekolah" class="form-control" placeholder="Nama Sekolah">
                        </div>
                        <div class="col-md-6">
                            <label>FC Raport</label>
                            <div class="input-group">
                                <input type="file" class="form-control col-md-9" name="fc_raport" id="fc_raport">
                                <a href="#" id="download_fc_raport" class="btn btn-sm btn-success col-md-3" target="_blank" style="display: none;">
                                    <i class="typcn typcn-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row row-xs">
                        <div class="col-md-6">
                            <label for="tempatlahir">Tempat Lahir</label>
                            <input type="text" name="tempatlahir" id="tempatlahir" class="form-control" placeholder="Tempat Lahir">
                        </div>
                        <div class="col-md-6">
                            <label>FC Rek Sekolah</label>
                            <div class="input-group">
                                <input type="file" class="form-control col-md-9" name="fc_rek_skolah" id="fc_rek_skolah">
                                <a href="#" id="download_fc_rek_skolah" class="btn btn-sm btn-success col-md-3" target="_blank" style="display: none;">
                                    <i class="typcn typcn-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row row-xs">
                        <div class="col-md-6">
                            <label for="tgllahir">Tanggal Lahir</label>
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                </div>
                                <input type="text" name="tgllahir" id="tgllahir" class="form-control fc-datepicker" placeholder="Tanggal Lahir">
                            </div>
                        </div>
                    </div>
                    <br>
                    <h5 class="modal-title bg-success text-white p-2 rounded" id="editModalLabel">Riwayat Pencairan</h5>
                    <br>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table id="tablelog" class="table table-bordered">
                                <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1;">
                                    <tr>
                                        <th>No.</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Alasan Pencairan</th>
                                        <th>Status</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal log savings -->
 <div id="ModalLogSavings" class="modal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content modal-content-demo">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Detail Riwayat Tabungan <span id="childname"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table id="tablogsavings" class="table table-bordered">
                            <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <td class="text-right">Saldo Awal</td>
                                    <td class="text-right">Credit</td>
                                    <td class="text-right">Saldo Berjalan</td>
                                    <td class="text-right">Debet</td>
                                    <td class="text-right">Saldo Akhir</td>
                                    <td>Notes</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>

@endsection

@section('script')
<script src="{{ asset('assets/master/lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery-ui/ui/widgets/datepicker.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>

let maxRincian = 5
let rincianCount = 0

$(document).ready(function(){
    $('.fc-datepicker').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        appendTo: "#editAnak", // Memastikan datepicker muncul dalam modal
        beforeShow: function(input, inst) {
            $(inst.dpDiv).css({
                "z-index": 1051 // Lebih tinggi dari modal Bootstrap (default 1050)
            });
        }
    });
});

    $('#id_program').on('change', function() {
        $('#namasekolah').val('');
    });

    $('body').on('click', '#btn-add', function() {
        var employeeId = $(this).data('id');
        window.location.href = `/pengajuan/${employeeId}`;
    });
    
    $('body').on('click', '#delete', function() {
        var id = $(this).data('id');

        Swal.fire({
            title: "Yakin hapus?",
            text: "Data akan hilang permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/delete/anak/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        Swal.fire("Terhapus!", response.message, "success");
                        location.reload();
                    }
                });
            }
        });
    });

    $('body').on('click', '#edit', function () {
        var anakId = $(this).data('id');
        $.ajax({
            url: '/get/pengajuan/' + anakId,
            type: "GET",
            success: function(response) {
                $('#idanak').val(response.anakData.id)
                $('#namaanak').val(response.anakData.nama)
                $('#namasekolah').val(response.anakData.nama_sekolah)
                $('#tempatlahir').val(response.anakData.tempat_lahir)
                $('#id_program').val(response.anakData.id_program);

                if (response.user_role = 'krw') {
                    $('#text_program').val(response.anakData.program.level);   
                }

                // Format ulang tanggal sebelum ditampilkan di datepicker
                let tgl_lahir = new Date(response.anakData.tgl_lahir);
                let tgl_lahirfix = $.datepicker.formatDate('mm/dd/yy', tgl_lahir);
                $('#tgllahir').val(tgl_lahirfix);
                // Path untuk folder upload
                let basePath = "/upload/";

                function setDownloadLink(id, filename) {
                    if (filename) {
                        $("#" + id).attr("href", basePath + filename).show();
                    }else{
                        $("#" + id).hide();
                    }
                }

                setDownloadLink("download_surat_sekolah", response.anakData.surat_sekolah);
                setDownloadLink("download_fc_ktp", response.anakData.fc_ktp);
                setDownloadLink("download_fc_raport", response.anakData.fc_raport);
                setDownloadLink("download_fc_rek_skolah", response.anakData.fc_rek_sekolah);

                function formatTanggal(dateString) {
                    let date = new Date(dateString);
                    return new Intl.DateTimeFormat('id-ID', {
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric'
                    }).format(date);
                }

                $('#tablelog tbody').empty();
                // Periksa apakah ada transaksi
                if (response.anakData.reqpproval.length > 0) {

                    // Urutkan berdasarkan created_at desc
                    response.anakData.reqpproval.sort(function(a, b) {
                        return new Date(b.created_at) - new Date(a.created_at);
                    });

                    $.each(response.anakData.reqpproval, function (index, reqpproval){

                        let statusText = '';
                        let statusClass = '';

                        // Menyesuaikan status seperti di DataTables
                        if (reqpproval.status == 0) {
                            statusText = 'New';
                            statusClass = 'badge-primary'; // Biru
                        } else if (reqpproval.status == 1) {
                            statusText = 'Approved';
                            statusClass = 'badge-success'; // Hijau
                        } else if (reqpproval.status == 2) {
                            statusText = 'Rejected';
                            statusClass = 'badge-danger'; // Merah
                        }

                        $('#tablelog tbody').append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${formatTanggal(reqpproval.created_at)}</td>
                                <td>${reqpproval.notes}</td>
                                <td><span class="badge ${statusClass} p-2 rounded">${statusText}</span></td>
                                <td>${formatTanggal(reqpproval.created_at)}</td>
                            </tr>
                        `)
                    })
                }


                $('#editAnak').modal('show');
            }
        })
    })
    
    // Fungsi untuk format angka ke ribuan dengan koma
    function formatRibuan(angka) {
        return angka.replace(/\D/g, "") // Hapus semua yang bukan angka
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ","); // Tambahkan koma tiap ribuan
    }

    // Ketika checkbox diklik, ubah isi modal-body
    $('#confirmCheck').on('change', function () {
        if ($(this).is(':checked')) {

            // ✅ Buka Google Sheet di tab baru
            window.open(
                "https://docs.google.com/spreadsheets/d/1dTxMegnt86Xf2fI-dCN5CMsq9bT4_IUV/edit?gid=94923237#gid=94923237",
                "_blank"
            );

            // Ganti modal-body dengan form baru
            $('#reqApproveModal .modal-body').html(`
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tujuanPencairan">Masukan Deskripsi Pembayaran</label>
                        <input type="text" name="tujuan_pencairan" id="tujuanPencairan" class="form-control" placeholder="Tujuan Pencairan">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="filefcraport">File Foto Copy Raport</label>
                        <input type="file" name="filefcraport" id="filefcraport" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="filepencairan">Dokumen Pencairan</label>
                        <input type="file" name="filepencairan" id="filepencairan" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="bankname">Nama Bank Pencairan</label>
                        <input type="text" name="bankname" id="bankname" class="form-control" placeholder="Nama Bank Pencairan">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="norek">Nomor Rekening Pencairan</label>
                        <input type="text" name="norek" id="norek" class="form-control" placeholder="Nomor Rekening Pencairan">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="accountbankname">Nama Yang Tercantum di Rekening</label>
                        <input type="text" name="accountbankname" id="accountbankname" class="form-control" placeholder="Nama Rekening Pencairan">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="isreimburst">Type Pencairan</label>
                        <select name="isreimburst" id="isreimburst" class="form-control">
                            <option value="">Pilih</option>
                            <option value="0">Cash Advance</option>
                            <option value="1">Reimburse</option>
                        </select>
                    </div>
                </div>

                <!-- ✅ Area Rincian Dinamis -->
                <div class="form-row mt-3">
                    <div class="form-group col-md-12">
                        <label>Rincian Pengajuan Dana</label>
                        <div id="rincianWrapper"></div>

                        <button type="button" id="addRincianBtn" class="btn btn-sm btn-success mt-2">
                            + Tambah Rincian
                        </button>
                    </div>
                </div>

                <div class="form-row mt-3">
                    <div class="form-group col-md-6">
                        <label>Total Nominal Diajukan</label>
                        <input type="text" name="nominal" id="nominal" class="form-control" style="text-align: right;" readonly value="0">
                    </div>
                </div>
            `);

            rincianCount = 0
            addRincianRow()

            // Terapkan format ribuan ke input nominal
            $('#nominal').on('input', function () {
                this.value = formatRibuan(this.value);
            });

            $('#saveChangesBtn').prop('disabled', false);
        } else {
            // Kembalikan modal-body ke `termContent`
            $.ajax({
                url: "/reqapproval/" + $('#id_anak').val(),
                type: "GET",
                success: function (response) {
                    $('#reqApproveModal .modal-body').html(response.termContent);
                    $('#saveChangesBtn').prop('disabled', true);
                }
            });
        }
    });

    // close tombol req approve

    $('#reqApproveModal').on('hidden.bs.modal', function () {
        $('#confirmCheck').prop('checked', false);
        $('#saveChangesBtn').prop('disabled', true);
        $('#id_anak').val("");
    });

    // log savings
    $('body').on('click', '#logsavings', function () {
        var id = $(this).data('id');
        $.ajax({
            url: `/savings/${id}/log`,
            type: "GET",
            success: function (response) {
                $('#ModalLogSavings').modal('show');
                $('#childname').text(response[0].nama);
                
                 // Bersihkan tabel saldo sebelum menambahkan data baru
                 $('#tablogsavings tbody').empty();

                 if (response[1].length > 0) {
                    $.each(response[1], function (index, transaction) {
                        $('#tablogsavings tbody').append(`
                            <tr>
                                <td class="text-right">Rp. ${transaction.previous_balance.toLocaleString('en-US')}</td>
                                <td class="text-right">Rp. ${transaction.credit.toLocaleString('en-US')}</td>
                                <td class="text-right">Rp. ${transaction.running_balance.toLocaleString('en-US')}</td>
                                <td class="text-right">Rp. ${transaction.debit.toLocaleString('en-US')}</td>
                                <td class="text-right">Rp. ${transaction.final_balance.toLocaleString('en-US')}</td>
                                <td>${transaction.notes}</td>
                            </tr>
                        `)
                    })
                 }else{
                    $('#tablogsavings tbody').append(`
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada transaksi</td>
                        </tr>
                    `);
                 }
            }
        })
    })

    // Klik pengajuan pencairan
    $('body').on('click', '#reqApprove', function() {
        var reqId = $(this).data('id');

        // Ambil data berdasarkan ID
        $.ajax({
            url: "/reqapproval/" + reqId,
            type: "GET",
            success: function(response) {
                $('#id_anak').val(reqId);
                $('#reqApproveModal .modal-body').html(response.termContent);
                $('#reqApproveModal').modal('show');
            }
        });
    });

    // post pengajuan pencairan
    $('body').on('click', '#saveChangesBtn', function() {

        var formData = new FormData();

        formData.append('id_anak', $('#id_anak').val());
        formData.append('tujuan_pencairan', $('#tujuanPencairan').val());
        formData.append('nominal', $('#nominal').val().replace(/,/g, ''));
        formData.append('norek', $('#norek').val());
        formData.append('bankname', $('#bankname').val());
        formData.append('accountbankname', $('#accountbankname').val());
        formData.append('isreimburst', $('#isreimburst').val() || 0);

        // File
        if ($('#filepencairan')[0].files[0]) {
            formData.append('filepencairan', $('#filepencairan')[0].files[0]);
        }

        if ($('#filefcraport')[0].files[0]) {
            formData.append('filefcraport', $('#filefcraport')[0].files[0]);
        }

        // --- PERBAIKAN DISINI ---
        $('input[name="rincian[]"]').each(function (i) {
            formData.append(`rincian[${i}]`, $(this).val());
        });

        $('input[name="nominal_rincian[]"]').each(function (i) {
            formData.append(`nominal_rincian[${i}]`, $(this).val().replace(/,/g, '') || 0);
        });

        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: "{{ route('postreqapprovael') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: "Berhasil!",
                    text: response.message,
                    icon: "success"
                }).then(() => location.reload());
            },
            error: function(xhr) {
                Swal.fire({
                    title: "Error!",
                    text: xhr.responseJSON.message || "Terjadi kesalahan!",
                    icon: "error"
                });
            }
        });

    });


    // edit data anak
    $(document).on("submit", "#form-edit", function (e) {
        let formData = new FormData(this); // Ambil semua data form, termasuk file

        $.ajax({
            url: "{{ route('pengajuan.update') }}", // Route Laravel
            type: "POST",
            data: formData,
            processData: false,  
            contentType: false,  
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: response.message,
                        icon: "success",
                        timer: 8000,
                        showConfirmButton: false
                    });

                    $("#editAnak").modal("hide"); // Tutup modal
                    location.reload(); // Reload data tabel atau halaman
                }
            },
            error: function (xhr) {
                let errorMessage = "Terjadi kesalahan!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: "Gagal!",
                    text: errorMessage,
                    icon: "error"
                });
            }
        });
    });

    // ✅ Tombol tambah rincian
    $(document).on('click', '#addRincianBtn', function () {
        if (rincianCount < maxRincian) {
            addRincianRow();
        }
        if (rincianCount >= maxRincian) {
            $('#addRincianBtn').hide();
        }
    });

    // ✅ Tombol hapus rincian
    $(document).on('click', '[id^=removeRincianBtn]', function () {
        const rowId = $(this).data('row'); // ambil ID baris dari data-row
        $(`#rincianRow${rowId}`).remove();

        rincianCount--;
        renumberRincian();
        hitungTotal();

        if (rincianCount < maxRincian) {
            $('#addRincianBtn').show();
        }
    });

    function addRincianRow() {
        rincianCount++;
        let alphabet = String.fromCharCode(96 + rincianCount); // a, b, c, ...

        let row = `
            <div id="rincianRow${rincianCount}" class="form-row mb-2">
                <div class="col-md-1 d-flex align-items-center">
                    <strong>${alphabet}.</strong>
                </div>
                <div class="col-md-5">
                    <input type="text" id="rincian${rincianCount}" name="rincian[]" class="form-control" placeholder="Rincian...">
                </div>
                <div class="col-md-4">
                    <input type="text" id="nominalRincian${rincianCount}" name="nominal_rincian[]" style="text-align: right;" class="form-control" placeholder="0">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    ${rincianCount > 1 ? `<button type="button" id="removeRincianBtn${rincianCount}" data-row="${rincianCount}" class="btn btn-sm btn-danger">−</button>` : ''}
                </div>
            </div>
        `;

        $('#rincianWrapper').append(row);

        // Format ribuan + update total
        $(`#nominalRincian${rincianCount}`).on('input', function () {
            this.value = formatRibuan(this.value);
            hitungTotal();
        });
    }


    // ✅ Format angka ke ribuan
    function formatRibuan(angka) {
        return angka
            .replace(/\D/g, "")
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // ✅ Hitung total semua nominal
    function hitungTotal() {
        let total = 0;
        $('[id^=nominalRincian]').each(function () {
            let val = $(this).val().replace(/,/g, '');
            if (val) total += parseInt(val);
        });
        $('#nominal').val(total.toLocaleString('en-US'));
    }

    // ✅ Ulang urutan abjad setelah hapus
    function renumberRincian() {
        let rows = $('[id^=rincianRow]');
        let idx = 1;
        rows.each(function () {
            let alphabet = String.fromCharCode(96 + idx);
            $(this).find('strong').text(alphabet + '.');
            idx++;
        });
        rincianCount = rows.length;
    }

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