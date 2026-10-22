@extends('layout.body')
@section('css')
<link href="{{ asset('assets/master/lib/amazeui-datetimepicker/css/amazeui.datetimepicker.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/jquery-simple-datetimepicker/jquery.simple-dtpicker.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/pickerjs/picker.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 style="font-weight: 800; color: var(--yph-text-dark); margin-bottom: 2px;">Pendaftaran Peserta Tabungan</h4>
                <p class="text-muted" style="font-size: 13px; margin: 0;">Lengkapi biodata dan unggah berkas persyaratan pendidikan anak.</p>
            </div>
            <button type="button" id="back" class="btn btn-sm btn-outline-secondary" data-id="{{$employee->id}}" style="border-radius: 10px; font-weight: 600;">
                <i class="typcn typcn-arrow-left"></i> Kembali
            </button>
        </div>

        <form action="{{route('pengajuan.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row" id="childrenContainer">
                <!-- FORM ANAK PERTAMA -->
                <div class="col-12 col-lg-6 child-form mb-3">
                    <div class="fintech-card p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h5 style="font-weight: 700; margin: 0; color: var(--yph-primary);">
                                <i class="typcn typcn-user"></i> Data Anak @if (count($anakData) < 1) <span class="child-count">Pertama</span> @endif
                            </h5>
                            <span class="badge-fintech badge-fintech-primary">Form Utama</span>
                        </div>

                        <div class="form-group mb-3">
                            <label style="font-weight: 600; font-size: 13px;">Tingkat Sekolah <span class="text-danger">*</span></label>
                            <select class="form-control" id="anak[0][id_program]" name="anak[0][id_program]" required style="border-radius: 10px; height: 42px;">
                                <option label="Pilih Tingkat Sekolah"></option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">
                                        {{ $program->level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label style="font-weight: 600; font-size: 13px;">Nama Lengkap Anak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="anak[0][nama]" placeholder="Nama Lengkap Anak" required style="border-radius: 10px; height: 42px;">
                            <input type="hidden" name="employee_id" value="{{$employee->id}}">
                        </div>

                        <div class="form-group mb-3">
                            <label style="font-weight: 600; font-size: 13px;">Nama Sekolah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="anak[0][nama_sekolah]" placeholder="Nama Sekolah" required style="border-radius: 10px; height: 42px;">
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-6 mb-3">
                                <label style="font-weight: 600; font-size: 13px;">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="anak[0][tempat_lahir]" placeholder="Tempat Lahir" required style="border-radius: 10px; height: 42px;">
                            </div>
                            <div class="col-12 col-sm-6 mb-3">
                                <label style="font-weight: 600; font-size: 13px;">Tanggal Lahir <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-right-0">
                                        <i class="typcn typcn-calendar-outline text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control fc-datepicker" name="anak[0][tgl_lahir]" placeholder="MM/DD/YYYY" required style="height: 42px; border-radius: 0 10px 10px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label style="font-weight: 600; font-size: 13px;">FC KTP Orangtua <span class="text-muted">(JPG/PNG/PDF, Maks 3MB)</span></label>
                            <input type="file" class="form-control" name="fc_ktp" id="fc_ktp" style="border-radius: 10px;">
                        </div>

                        <div class="mb-3">
                            <label style="font-weight: 600; font-size: 13px;">Surat Keterangan Sekolah</label>
                            <input type="file" class="form-control" name="anak[0][surat_sekolah]" style="border-radius: 10px;">
                        </div>

                        <div class="mb-3">
                            <label style="font-weight: 600; font-size: 13px;">FC Raport</label>
                            <input type="file" class="form-control" name="anak[0][fc_raport]" style="border-radius: 10px;">
                        </div>

                        <div class="mb-3">
                            <label style="font-weight: 600; font-size: 13px;">FC Rekening Sekolah</label>
                            <input type="file" class="form-control" name="anak[0][fc_rek_sekolah]" style="border-radius: 10px;">
                        </div>
                    </div>
                </div>

                <!-- FORM ANAK KEDUA (DISEMBUNYIKAN DULU) -->
                <div class="col-12 col-lg-6 child-form mb-3" id="child2" style="display: none;">
                    <div class="fintech-card p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h5 style="font-weight: 700; margin: 0; color: var(--yph-primary);">
                                <i class="typcn typcn-user"></i> Data Anak <span class="child-count">Kedua</span>
                            </h5>
                            <span class="badge-fintech badge-fintech-warning">Opsional</span>
                        </div>

                        <div class="form-group mb-3">
                            <label style="font-weight: 600; font-size: 13px;">Tingkat Sekolah <span class="text-danger">*</span></label>
                            <select class="form-control" id="anak[1][id_program]" name="anak[1][id_program]" style="border-radius: 10px; height: 42px;">
                                <option label="Pilih Tingkat Sekolah"></option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">
                                        {{ $program->level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label style="font-weight: 600; font-size: 13px;">Nama Lengkap Anak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="anak[1][nama]" placeholder="Nama Lengkap Anak" style="border-radius: 10px; height: 42px;">
                        </div>

                        <div class="form-group mb-3">
                            <label style="font-weight: 600; font-size: 13px;">Nama Sekolah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="anak[1][nama_sekolah]" placeholder="Nama Sekolah" style="border-radius: 10px; height: 42px;">
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-6 mb-3">
                                <label style="font-weight: 600; font-size: 13px;">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="anak[1][tempat_lahir]" placeholder="Tempat Lahir" style="border-radius: 10px; height: 42px;">
                            </div>
                            <div class="col-12 col-sm-6 mb-3">
                                <label style="font-weight: 600; font-size: 13px;">Tanggal Lahir <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-right-0">
                                        <i class="typcn typcn-calendar-outline text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control fc-datepicker" name="anak[1][tgl_lahir]" placeholder="MM/DD/YYYY" style="height: 42px; border-radius: 0 10px 10px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label style="font-weight: 600; font-size: 13px;">Surat Keterangan Sekolah</label>
                            <input type="file" class="form-control" name="anak[1][surat_sekolah]" style="border-radius: 10px;">
                        </div>

                        <div class="mb-3">
                            <label style="font-weight: 600; font-size: 13px;">FC Raport</label>
                            <input type="file" class="form-control" name="anak[1][fc_raport]" style="border-radius: 10px;">
                        </div>

                        <div class="mb-3">
                            <label style="font-weight: 600; font-size: 13px;">FC Rekening Sekolah</label>
                            <input type="file" class="form-control" name="anak[1][fc_rek_sekolah]" style="border-radius: 10px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex flex-wrap gap-2 mt-2 mb-4">
                @if(count($anakData) < 1)
                    <button type="button" id="addchild" class="btn btn-outline-primary" style="height: 46px; border-radius: 12px; font-weight: 600;">
                        <i class="typcn typcn-plus"></i> Tambah Anak Kedua
                    </button>
                @endif
                <button type="submit" class="btn btn-fintech-primary flex-fill" style="min-width: 200px;">
                    <i class="typcn typcn-upload"></i> Kirim Formulir Pendaftaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/master/lib/jquery-ui/ui/widgets/datepicker.js') }}"></script>
<script src="{{ asset('assets/master/lib/select2/js/select2.min.js') }}"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('.select2').select2({ placeholder: 'Choose one' });
</script>
<script>
$(document).ready(function() {
    $('.fc-datepicker').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true
    });

    let isChildAdded = false;

    $('#addchild').click(function() {
        if (!isChildAdded) {
            $('#child2').show();
            $('#child2 input, #child2 select').attr('required', true).prop('disabled', false); // Tambahkan required & aktifkan input
            $(this).text("Batal Tambah Anak").removeClass("btn-dark").addClass("btn-danger");
            isChildAdded = true;
        } else {
            $('#child2').hide();
            $('#child2 input, #child2 select').removeAttr('required').prop('disabled', true); // Hapus required & nonaktifkan input
            $(this).text("Tambah Anak").removeClass("btn-danger").addClass("btn-dark");
            isChildAdded = false;
        }
    });

    $('#back').click(function(){
        var id = $(this).data('id')
        window.location.href = "/employee/" + id;
    })
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