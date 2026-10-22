@extends('layout.body')
@section('css')
<style>
    /* Mobile-First Fintech Styling for Employee Detail */
    .employee-hero-header {
        background: white;
        border-radius: var(--yph-radius);
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid var(--yph-border);
        box-shadow: var(--yph-shadow);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    @media (min-width: 768px) {
        .employee-hero-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 24px;
        }
    }

    .fintech-child-card {
        background: white;
        border-radius: var(--yph-radius);
        border: 1px solid var(--yph-border);
        box-shadow: var(--yph-shadow);
        padding: 20px;
        margin-bottom: 22px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .fintech-child-card:hover {
        box-shadow: var(--yph-shadow-hover);
    }

    .child-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .child-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .child-avatar-pill {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #eff6ff;
        color: var(--yph-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
    }

    .balance-box {
        background: var(--yph-primary-gradient);
        color: white;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 8px 20px -3px rgba(37, 99, 235, 0.3);
        position: relative;
        overflow: hidden;
    }
    .balance-box::after {
        content: "";
        position: absolute;
        right: -30px;
        top: -30px;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }

    .balance-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        opacity: 0.85;
        margin-bottom: 4px;
    }

    .balance-value {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .balance-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }
    @media (min-width: 576px) {
        .balance-stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .stat-item-label {
        font-size: 10px;
        opacity: 0.8;
        text-transform: uppercase;
    }
    .stat-item-val {
        font-size: 13px;
        font-weight: 700;
    }

    .action-btn-row {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    @media (min-width: 576px) {
        .action-btn-row {
            flex-direction: row;
        }
    }

    .ui-datepicker {
        z-index: 1051 !important;
    }
</style>
@endsection
@section('content')

@php
    date_default_timezone_set('Asia/Jakarta');
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

<!-- Hero Greeting Header -->
<div class="employee-hero-header">
    <div>
        <span class="badge-fintech badge-fintech-primary mb-1">
            <i class="typcn typcn-briefcase"></i> {{ $employee->company->name ?? 'Karyawan' }}
        </span>
        <h4 style="font-weight: 800; margin: 4px 0; color: var(--yph-text-dark);">
            {{ $greeting }}, {{ $employee->name }} 👋
        </h4>
        <p class="text-muted mb-0" style="font-size: 13px;">Kelola simpanan dan pengajuan dana pendidikan anak Anda di sini.</p>
    </div>
    
    <div>
        @if(count($dataanak) < 2)
            <button type="button" class="btn btn-fintech-primary w-100" id="btn-add" data-id="{{ Crypt::encryptString($employee->id) }}">
                <i class="typcn typcn-plus"></i> Tambah Pendaftaran Anak
            </button>
        @else
            <span class="badge-fintech badge-fintech-success">
                <i class="typcn typcn-tick"></i> Kuota Maksimal 2 Anak Tercapai
            </span>
        @endif
    </div>
</div>

@if($dataanak->isEmpty())
    <div class="fintech-card text-center py-5">
        <div style="font-size: 48px; color: #94a3b8; margin-bottom: 12px;">
            <i class="typcn typcn-folder-open"></i>
        </div>
        <h5 style="font-weight: 700; color: #334155;">Belum Ada Pendaftaran Tabungan</h5>
        <p class="text-muted" style="max-width: 400px; margin: 0 auto 18px;">
            Anda belum mendaftarkan anak Anda ke program tabungan pendidikan. Segera daftarkan untuk mulai menabung masa depan mereka.
        </p>
        <button type="button" class="btn btn-fintech-primary" id="btn-add" data-id="{{ Crypt::encryptString($employee->id) }}">
            <i class="typcn typcn-plus"></i> Daftarkan Sekarang
        </button>
    </div>
@else
    @foreach ($dataanak as $anak)
        @php
            $saldo = $anak->latestTransaction;
            $initial = strtoupper(substr($anak->nama, 0, 1));
            $latestApproval = $anak->approval->last();
        @endphp

        <div class="fintech-child-card">
            <!-- Header Kartu Anak -->
            <div class="child-card-header">
                <div class="child-info">
                    <div class="child-avatar-pill">
                        {{ $initial }}
                    </div>
                    <div>
                        <h5 style="font-weight: 800; margin-bottom: 2px; color: var(--yph-text-dark);">
                            {{ $anak->nama }}
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-fintech badge-fintech-primary">
                                {{ $anak->program->level ?? 'Program Pendidikan' }}
                            </span>
                            @if($latestApproval)
                                @if($latestApproval->status == 1)
                                    <span class="badge-fintech badge-fintech-success"><i class="typcn typcn-tick"></i> Terverifikasi</span>
                                @elseif($latestApproval->status == 0)
                                    <span class="badge-fintech badge-fintech-warning"><i class="typcn typcn-time"></i> Menunggu Approval</span>
                                @elseif($latestApproval->status == 2)
                                    <span class="badge-fintech badge-fintech-danger"><i class="typcn typcn-times"></i> Ditolak</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tombol Edit & Delete -->
                <div class="d-flex align-items-center gap-1">
                    @if($latestApproval && $latestApproval->status == 2 && auth()->user()->role == 'adm')
                        <button type="button" id="delete" data-id="{{ $anak->id }}" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; padding: 6px 10px;" title="Hapus Data">
                            <i class="typcn typcn-trash"></i>
                        </button>
                    @endif

                    <button type="button" id="edit" data-id="{{ $anak->id }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; padding: 6px 10px;" title="Edit Biodata">
                        <i class="typcn typcn-pencil"></i> Edit
                    </button>
                </div>
            </div>

            <!-- Hero Saldo Box -->
            <div class="balance-box">
                <div class="balance-label">Total Saldo Tabungan Aktif</div>
                <div class="balance-value">
                    Rp {{ number_format($saldo->final_balance ?? 0, 0, ',', '.') }}
                </div>

                <div class="balance-stats-grid">
                    <div>
                        <div class="stat-item-label">Saldo Awal</div>
                        <div class="stat-item-val">Rp {{ number_format($saldo->previous_balance ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="stat-item-label">Total Masuk (Credit)</div>
                        <div class="stat-item-val" style="color: #6ee7b7;">+ Rp {{ number_format($saldo->credit ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="stat-item-label">Saldo Berjalan</div>
                        <div class="stat-item-val">Rp {{ number_format($saldo->running_balance ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="stat-item-label">Total Keluar (Debit)</div>
                        <div class="stat-item-val" style="color: #fca5a5;">- Rp {{ number_format($saldo->debit ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Mobile-Friendly -->
            <div class="action-btn-row">
                @if ($saldo && $saldo->final_balance > 0)
                    <button type="button" id="reqApprove" data-id="{{ $anak->id }}" class="btn-fintech-primary flex-fill">
                        <i class="typcn typcn-arrow-up-outline" style="font-size: 18px;"></i> Ajukan Pencairan Dana
                    </button>
                @endif

                <button type="button" id="logsavings" data-id="{{ $anak->id }}" class="btn-fintech-secondary flex-fill">
                    <i class="typcn typcn-time" style="font-size: 16px;"></i> Riwayat Mutasi Tabungan
                </button>
            </div>
        </div>
    @endforeach
@endif

<!-- Modal Ajukan Approval -->
<div id="reqApproveModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header text-white" style="background: var(--yph-primary-gradient);">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-file-signature mr-2"></i> Ketentuan Pencairan Tabungan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-accept">
                @csrf
                <input type="hidden" name="id_anak" id="id_anak">
                <div class="modal-body p-4" style="max-height: 60vh; overflow-y: auto;">
                    <!-- Terms content loaded dynamically -->
                </div>
                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center p-3" style="background: #f8fafc; border-top: 1px solid var(--yph-border); gap: 12px;">
                    <div class="form-check m-0">
                        <input type="checkbox" id="confirmCheck" class="form-check-input" style="width: 18px; height: 18px; cursor: pointer;">
                        <label for="confirmCheck" class="form-check-label ml-1" style="font-size: 13px; font-weight: 600; color: #334155; cursor: pointer;">
                            Saya menyetujui seluruh syarat & ketentuan di atas.
                        </label>
                    </div>
                    <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                        <button type="button" data-dismiss="modal" class="btn btn-secondary mr-2" style="border-radius: 10px;">Batal</button>
                        <button type="button" id="saveChangesBtn" class="btn btn-fintech-primary" disabled>
                            <i class="fas fa-check-circle mr-1"></i> Ajukan Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Data Anak -->
<div id="editAnak" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header text-white" style="background: var(--yph-primary-gradient);">
                <h5 class="modal-title font-weight-bold" id="editModalLabel">
                    <i class="fas fa-user-edit mr-2"></i> Detail & Berkas Data Anak
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="form-edit">
                    @csrf
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge-fintech badge-fintech-primary" style="font-size: 13px;">
                            <i class="fas fa-id-card mr-1"></i> Biodata Anak
                        </span>
                    </div>
                    <div class="alert alert-light border py-2 px-3 mb-3" style="border-radius: 10px; font-size: 12px; color: #64748b;">
                        <i class="fas fa-info-circle text-primary mr-1"></i> Format berkas yang didukung: <strong>JPG, PNG, atau PDF (Maks. 3 MB)</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="namaanak" class="font-weight-bold" style="font-size: 13px;">Nama Lengkap Anak</label>
                            <input type="hidden" name="idanak" id="idanak">
                            <input type="text" name="namaanak" id="namaanak" class="form-control" placeholder="Nama Anak" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold" style="font-size: 13px;">Surat Keterangan Sekolah</label>
                            <div class="input-group">
                                <input type="file" class="form-control" name="surat_sekolah" id="surat_sekolah" style="border-radius: 10px 0 0 10px;">
                                <div class="input-group-append">
                                    <a href="#" id="download_surat_sekolah" class="btn btn-outline-primary" target="_blank" style="display: none; border-radius: 0 10px 10px 0;" title="Lihat Berkas">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="jenjang" class="font-weight-bold" style="font-size: 13px;">Jenjang Pendidikan</label>
                            @if (auth()->user()->role != 'krw')
                                <select name="id_program" id="id_program" class="form-control" style="border-radius: 10px; height: 42px;">
                                    <option value="">Pilih Jenjang Pendidikan</option>
                                    @foreach($program as $prog)
                                        <option value="{{ $prog->id }}">{{ $prog->level }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" name="text_program" id="text_program" class="form-control" placeholder="Jenjang Pendidikan" readonly style="border-radius: 10px; height: 42px; background: #f8fafc;">
                                <input type="hidden" name="id_program" id="id_program">
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold" style="font-size: 13px;">FC KTP Karyawan</label>
                            <div class="input-group">
                                <input type="file" class="form-control" name="fc_ktp" id="fc_ktp" style="border-radius: 10px 0 0 10px;">
                                <div class="input-group-append">
                                    <a href="#" id="download_fc_ktp" class="btn btn-outline-primary" target="_blank" style="display: none; border-radius: 0 10px 10px 0;" title="Lihat Berkas">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="namasekolah" class="font-weight-bold" style="font-size: 13px;">Nama Sekolah</label>
                            <input type="text" name="namasekolah" id="namasekolah" class="form-control" placeholder="Nama Sekolah" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold" style="font-size: 13px;">FC Raport</label>
                            <div class="input-group">
                                <input type="file" class="form-control" name="fc_raport" id="fc_raport" style="border-radius: 10px 0 0 10px;">
                                <div class="input-group-append">
                                    <a href="#" id="download_fc_raport" class="btn btn-outline-primary" target="_blank" style="display: none; border-radius: 0 10px 10px 0;" title="Lihat Berkas">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="tempatlahir" class="font-weight-bold" style="font-size: 13px;">Tempat Lahir</label>
                            <input type="text" name="tempatlahir" id="tempatlahir" class="form-control" placeholder="Kota Tempat Lahir" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold" style="font-size: 13px;">FC Rekening Sekolah / SPP</label>
                            <div class="input-group">
                                <input type="file" class="form-control" name="fc_rek_skolah" id="fc_rek_skolah" style="border-radius: 10px 0 0 10px;">
                                <div class="input-group-append">
                                    <a href="#" id="download_fc_rek_skolah" class="btn btn-outline-primary" target="_blank" style="display: none; border-radius: 0 10px 10px 0;" title="Lihat Berkas">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="tgllahir" class="font-weight-bold" style="font-size: 13px;">Tanggal Lahir</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light" style="border-radius: 10px 0 0 10px;">
                                        <i class="far fa-calendar-alt text-primary"></i>
                                    </span>
                                </div>
                                <input type="text" name="tgllahir" id="tgllahir" class="form-control fc-datepicker" placeholder="YYYY-MM-DD" style="border-radius: 0 10px 10px 0; height: 42px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mt-3 mb-2">
                        <span class="badge-fintech badge-fintech-warning" style="font-size: 13px;">
                            <i class="fas fa-history mr-1"></i> Riwayat Pencairan
                        </span>
                    </div>

                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto; border: 1px solid var(--yph-border); border-radius: 12px;">
                        <table id="tablelog" class="table table-hover table-sm mb-0">
                            <thead class="sticky-header" style="background: #f8fafc;">
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

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal" style="border-radius: 10px;">Tutup</button>
                        <button type="submit" class="btn btn-fintech-primary">
                            <i class="fas fa-save mr-1"></i> Update Data Anak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal log savings -->
<div id="ModalLogSavings" class="modal fade" tabindex="-1" aria-labelledby="modalSavingsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header text-white" style="background: var(--yph-primary-gradient);">
                <h5 class="modal-title font-weight-bold" id="modalSavingsLabel">
                    <i class="fas fa-wallet mr-2"></i> Riwayat Mutasi Tabungan: <span id="childname"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto; border: 1px solid var(--yph-border); border-radius: 12px;">
                    <table id="tablogsavings" class="table table-hover table-striped mb-0">
                        <thead class="sticky-header" style="background: #f1f5f9;">
                            <tr>
                                <th class="text-right">Saldo Awal</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Saldo Berjalan</th>
                                <th class="text-right">Debet</th>
                                <th class="text-right">Saldo Akhir</th>
                                <th>Catatan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="text-right mt-3">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px;">Tutup</button>
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

                    const UpdatedDate = new Date(transaction.updated_at).toLocaleString('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        dateStyle: 'long'
                    })

                        $('#tablogsavings tbody').append(`
                            <tr>
                                <td class="text-right">Rp. ${transaction.previous_balance.toLocaleString('en-US')}</td>
                                <td class="text-right">Rp. ${transaction.credit.toLocaleString('en-US')}</td>
                                <td class="text-right">Rp. ${transaction.running_balance.toLocaleString('en-US')}</td>
                                <td class="text-right">Rp. ${transaction.debit.toLocaleString('en-US')}</td>
                                <td class="text-right">Rp. ${transaction.final_balance.toLocaleString('en-US')}</td>
                                <td>${transaction.notes}</td>
                                <td>${UpdatedDate}</td>
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