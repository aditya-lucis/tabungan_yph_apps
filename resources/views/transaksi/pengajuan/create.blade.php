@extends('layout.body')
@section('css')
<link href="{{ asset('assets/master/lib/amazeui-datetimepicker/css/amazeui.datetimepicker.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/jquery-simple-datetimepicker/jquery.simple-dtpicker.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/pickerjs/picker.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{route('pengajuan.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row" id="childrenContainer">
                <!-- FORM ANAK PERTAMA -->
                <div class="col-md-6 child-form">
                    <div class="card card-body pd-40">
                        <h5 class="card-title mg-b-20">Data Anak @if (count($anakData) < 1) <span class="child-count">Pertama</span> @endif</h5>
                        <div class="form-group mb-2">
                            <label>Tingkat Sekolah</label>
                            <select class="form-control" id="anak[0][id_program]" name="anak[0][id_program]" required>
                                <option label="Choose one"></option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">
                                        {{ $program->level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row row-xs">
                            <div class="col-md-6">
                                <label>Nama</label>
                                <input type="text" class="form-control" name="anak[0][nama]" placeholder="Nama Anak" required>
                                <input type="hidden" name="employee_id" value="{{$employee->id}}">
                            </div>
                            <div class="col-md-6">
                                <label>Surat Keterangan Sekolah</label>
                                <input type="file" class="form-control" name="anak[0][surat_sekolah]">
                            </div>
                        </div>
                        <br>
                        <div class="row row-xs">
                            <div class="col-md-6">
                                <label>Nama Sekolah</label>
                                <input type="text" class="form-control" name="anak[0][nama_sekolah]" placeholder="Nama Sekolah" required>
                            </div>
                            <div class="col-md-6">
                                <label>FC KTP Orangtua</label>
                                <input type="file" class="form-control" name="fc_ktp" id="fc_ktp">
                            </div>
                        </div>
                        <br>
                        <div class="row row-xs">
                            <div class="col-md-6">
                                <label>Tempat Lahir</label>
                                <input type="text" class="form-control" name="anak[0][tempat_lahir]" placeholder="Tempat Lahir" required>
                            </div>
                            <div class="col-md-6">
                                <label>FC Raport</label>
                                <input type="file" class="form-control" name="anak[0][fc_raport]">
                            </div>
                        </div>
                        <br>
                        <div class="row row-xs">
                            <div class="col-md-6">
                                <label>Tanggal Lahir</label>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                    </div>
                                    <input type="text" class="form-control fc-datepicker" name="anak[0][tgl_lahir]" placeholder="MM/DD/YYYY" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>FC Rek Sekolah</label>
                                <input type="file" class="form-control" name="anak[0][fc_rek_sekolah]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORM ANAK KEDUA (DISEMBUNYIKAN DULU) -->
                <div class="col-md-6 child-form" id="child2" style="display: none;">
                    <div class="card card-body pd-40">
                        <h5 class="card-title mg-b-20">Data Anak <span class="child-count">Kedua</span></h5>
                        <div class="form-group mb-2">
                            <label>Tingkat Sekolah</label>
                            <select class="form-control" id="anak[1][id_program]" name="anak[1][id_program]">
                                <option label="Choose one"></option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">
                                        {{ $program->level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row row-xs">
                            <div class="col-md-6">
                                <label>Nama</label>
                                <input type="text" class="form-control" name="anak[1][nama]" placeholder="Nama Anak">
                            </div>
                            <div class="col-md-6">
                                <label>Surat Keterangan Sekolah</label>
                                <input type="file" class="form-control" name="anak[1][surat_sekolah]">
                            </div>
                        </div>
                        <br>
                        <div class="row row-xs">
                            <div class="col-md-6">
                                <label>Nama Sekolah</label>
                                <input type="text" class="form-control" name="anak[1][nama_sekolah]" placeholder="Nama Sekolah">
                            </div>
                            <!-- FC KTP DIHAPUS DARI ANAK KEDUA -->
                        </div>
                        <br>
                        <div class="row row-xs">
                            <div class="col-md-6">
                                <label>Tempat Lahir</label>
                                <input type="text" class="form-control" name="anak[1][tempat_lahir]" placeholder="Tempat Lahir">
                            </div>
                            <div class="col-md-6">
                                <label>FC Raport</label>
                                <input type="file" class="form-control" name="anak[1][fc_raport]">
                            </div>
                        </div>
                        <br>
                        <div class="row row-xs">
                            <div class="col-md-6">
                                <label>Tanggal Lahir</label>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                    </div>
                                    <input type="text" class="form-control fc-datepicker" name="anak[1][tgl_lahir]" placeholder="MM/DD/YYYY">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>FC Rek Sekolah</label>
                                <input type="file" class="form-control" name="anak[1][fc_rek_sekolah]">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
        @if(count($anakData) < 1)
            <button type="button" id="addchild" class="btn btn-dark">Tambah Anak</button>
        @endif
            <button type="submit" class="btn btn-primary">Kirim</button>
            <button id="back" class="btn btn-danger" data-id="{{$employee->id}}">Back</button>
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