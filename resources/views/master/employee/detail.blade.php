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

<div class="az-content-label mg-b-5">Tabungan Berjalan Yang Diajukan {{$employee->name}}</div>
<br>
<div class="az-content-breadcrumb">
    <!-- <span>tambah Pengajuan</span> -->
    
    @if(count($dataanak) < 2)
        <button type="button" class="btn btn-sm btn-outline-light custom-btn" id="btn-add" data-id="{{$employee->id}}">
            <i class="typcn typcn-document-add"></i> Tambah Pengajuan
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
                    <div class="col-12">
                        <b>{{ $anak->nama }}</b> <a id="reqApprove" data-id="{{ $anak->id }}" class="btn btn-sm btn-outline-success custom-btn btn-rounded-3" style="top: 10px; right: 10px; padding: 8px 12px; font-size: 13px;">Ajukan Pencairan</a>
                        <a id="edit" data-id="{{ $anak->id }}" 
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
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($anak->transaction as $key => $tabungan)
                                <tr>
                                    <td class="text-align: right;">{{ number_format($tabungan->previous_balance, 2) }}</td>
                                    <td class="text-align: right;">{{ number_format($tabungan->credit, 2) }}</td>
                                    <td class="text-align: right;">{{ number_format($tabungan->running_balance, 2) }}</td>
                                    <td class="text-align: right;">{{ number_format($tabungan->debit, 2) }}</td>
                                    <td class="text-align: right;">{{ number_format($tabungan->final_balance, 2) }}</td>
                                    <td>{{ $tabungan->notes }}</td>
                                </tr>
                                @endforeach
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
          </form>
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
                            <label for="namasekolah">Nama Sekolah</label>
                            <input type="text" name="namasekolah" id="namasekolah" class="form-control" placeholder="Nama Sekolah">
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
                            <label for="tempatlahir">Tempat Lahir</label>
                            <input type="text" name="tempatlahir" id="tempatlahir" class="form-control" placeholder="Tempat Lahir">
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
                            <label for="tgllahir">Tanggal Lahir</label>
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                </div>
                                <input type="text" name="tgllahir" id="tgllahir" class="form-control fc-datepicker" placeholder="Tanggal Lahir">
                            </div>
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
                    <h5 class="modal-title bg-success text-white p-2 rounded" id="editModalLabel">Riwayat Pencairan</h5>
                    <br>
                        <div class="table-responsive">
                            <table id="tablelog" class="table table-bordered">
                                <thead>
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

@endsection

@section('script')
<script src="{{ asset('assets/master/lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery-ui/ui/widgets/datepicker.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>

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

    $('body').on('click', '#btn-add', function() {
        var employeeId = $(this).data('id');
        window.location.href = `/pengajuan/${employeeId}`;
    });

    $('body').on('click', '#edit', function () {
        var anakId = $(this).data('id');
        $.ajax({
            url: '/get/pengajuan/' + anakId,
            type: "GET",
            success: function(response) {
                $('#idanak').val(response.id)
                $('#namaanak').val(response.nama)
                $('#namasekolah').val(response.nama_sekolah)
                $('#tempatlahir').val(response.tempat_lahir)
                // Format ulang tanggal sebelum ditampilkan di datepicker
                let tgl_lahir = new Date(response.tgl_lahir);
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

                setDownloadLink("download_surat_sekolah", response.surat_sekolah);
                setDownloadLink("download_fc_ktp", response.fc_ktp);
                setDownloadLink("download_fc_raport", response.fc_raport);
                setDownloadLink("download_fc_rek_skolah", response.fc_rek_sekolah);

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
                if (response.reqpproval.length > 0) {
                    $.each(response.reqpproval, function (index, reqpproval){

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
            // Ganti modal-body dengan form baru
            $('#reqApproveModal .modal-body').html(`
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tujuanPencairan">Masukan Tujuan Pencairan</label>
                        <input type="text" name="tujuan_pencairan" id="tujuanPencairan" class="form-control" placeholder="Tujuan Pencairan">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nominal">Masukan Nominal Yang Diajukan</label>
                        <input type="text" name="nominal" id="nominal" class="form-control" placeholder="0">
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
                            <option>Pilih</option>
                            <option value="0">Cash Advance</option>
                            <option value="1">Reimburse</option>
                        </select>
                    </div>
                </div>
            `);

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

    // Klik tombol req approve
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

    $('body').on('click', '#saveChangesBtn', function() {
        var formData = new FormData();
        formData.append('id_anak', $('#id_anak').val());
        formData.append('tujuan_pencairan', $('#tujuanPencairan').val());
        formData.append('nominal', $('#nominal').val().replace(/,/g, '')); // Hapus koma
        formData.append('norek', $('#norek').val());
        formData.append('bankname', $('#bankname').val());
        formData.append('accountbankname', $('#accountbankname').val());
        formData.append('isreimburst', $('#isreimburst').val() || 0); // Gunakan default jika kosong

        // Tambahkan file jika ada
        var fileInput = $('#filepencairan')[0].files[0];
        if (fileInput) {
            formData.append('filepencairan', fileInput);
        }

        formData.append('_token', '{{ csrf_token() }}'); // Laravel CSRF Token

        $.ajax({
            url: "{{ route('postreqapprovael') }}", // Sesuaikan dengan route Anda
            type: "POST",
            data: formData,
            processData: false, // Jangan proses data agar file dikirim dengan benar
            contentType: false, // Jangan set Content-Type, biarkan browser yang menentukannya
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: "Silahkan Lengkap Data Anda!!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        window.open("https://docs.google.com/spreadsheets/d/1dTxMegnt86Xf2fI-dCN5CMsq9bT4_IUV/edit?gid=94923237#gid=94923237", "_blank");
                        location.reload();
                    });
                }
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

    
    $(document).on("submit", "#form-edit", function (e) {
        e.preventDefault(); // Mencegah reload halaman

        let formData = new FormData(this); // Ambil semua data form, termasuk file

        $.ajax({
            url: "{{ route('pengajuan.update') }}", // Route Laravel
            type: "POST",
            data: formData,
            processData: false,  // Jangan ubah data
            contentType: false,  // Jangan set header secara otomatis
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: response.message,
                        icon: "success",
                        timer: 2000,
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