@extends('layout.body')

@section('css')
<link href="{{ asset('assets/master/lib/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <div class="az-content-breadcrumb mb-1">
            <span>Master</span>
            <span>Employee</span>
            <span>{{ isset($employee) ? 'Edit Karyawan' : 'Tambah Karyawan' }}</span>
        </div>
        <h4 class="mb-0" style="font-weight: 800; color: #0f172a;">{{ isset($employee) ? 'Edit Data Karyawan' : 'Tambah Karyawan Baru' }}</h4>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-8 col-lg-7">
        <div class="fintech-card">
            <!-- Cek apakah halaman ini untuk Edit atau Tambah -->
            <form action="{{ isset($employee) ? route('employee.update', $employee->id) : route('employee.store') }}" method="post">
                @csrf
                @isset($employee)
                    @method('PUT') <!-- Jika edit, pakai PUT -->
                @endisset

                <div class="form-group mb-3">
                    <label for="name" class="font-weight-bold" style="font-size: 13px; color: #475569;">Nama Karyawan</label>
                    <input type="text" class="form-control" id="name" name="name" autocomplete="off" 
                           placeholder="Masukkan nama lengkap"
                           value="{{ old('name', $employee->name ?? '') }}" required style="border-radius: 10px; height: 44px;">
                </div>

                <div class="form-group mb-3">
                    <label for="email" class="font-weight-bold" style="font-size: 13px; color: #475569;">Email</label>
                    <input type="email" class="form-control" id="email" name="email" autocomplete="off" 
                           placeholder="nama@perusahaan.com"
                           value="{{ old('email', $employee->email ?? '') }}" required style="border-radius: 10px; height: 44px;">
                </div>

                <div class="form-group mb-3">
                    <label for="phone" class="font-weight-bold" style="font-size: 13px; color: #475569;">No. Handphone (WhatsApp)</label>
                    <input type="text" class="form-control" id="phone" name="phone" autocomplete="off" 
                           placeholder="Contoh: 081234567890"
                           value="{{ old('phone', $employee->phone ?? '') }}" required style="border-radius: 10px; height: 44px;">
                </div>

                <div class="form-group mb-4">
                    <label for="company_id" class="font-weight-bold" style="font-size: 13px; color: #475569;">Affco / Perusahaan</label>
                    <select class="form-control select2" id="company_id" name="company_id" required>
                        <option label="Pilih Perusahaan"></option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" 
                                {{ old('company_id', $employee->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2 pt-2" style="border-top: 1px solid var(--yph-border);">
                    <button type="submit" class="btn btn-fintech-primary mr-2">
                        <i class="fas fa-save mr-1"></i> {{ isset($employee) ? 'Simpan Perubahan' : 'Simpan Karyawan' }}
                    </button>
                    <button type="button" id="batal" class="btn btn-fintech-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/master/lib/select2/js/select2.min.js') }}"></script> 
<script>
    $('.select2').select2({ placeholder: 'Choose one' });

    $('#batal').click(function () {
        window.location.href = "{{ route('employee.index') }}";
    });
</script>
@endsection