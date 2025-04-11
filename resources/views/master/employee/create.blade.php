@extends('layout.body')

@section('css')
<link href="{{ asset('assets/master/lib/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="az-content-breadcrumb">
    <span>Master</span>
    <span>Employee</span>
    <span>{{ isset($employee) ? 'Edit Employee' : 'Add Employee' }}</span>
</div>

<div class="row">
    <div class="col-md-10 col-lg-8 col-xl-">
        <div class="card card-body pd-40">
            <!-- Cek apakah halaman ini untuk Edit atau Tambah -->
            <form action="{{ isset($employee) ? route('employee.update', $employee->id) : route('employee.store') }}" method="post">
                @csrf
                @isset($employee)
                    @method('PUT') <!-- Jika edit, pakai PUT -->
                @endisset

                <div class="form-group mb-2">
                    <label for="name" class="az-content-label tx-11 tx-medium tx-gray-600">Nama Karyawan</label>
                    <input type="text" class="form-control" id="name" name="name" autocomplete="off" 
                           value="{{ old('name', $employee->name ?? '') }}" required>
                </div>

                <div class="form-group mb-2">
                    <label for="email" class="az-content-label tx-11 tx-medium tx-gray-600">Email</label>
                    <input type="text" class="form-control" id="email" name="email" autocomplete="off" 
                           value="{{ old('email', $employee->email ?? '') }}" required>
                </div>

                <div class="form-group mb-2">
                    <label for="phone" class="az-content-label tx-11 tx-medium tx-gray-600">No. HP</label>
                    <input type="text" class="form-control" id="phone" name="phone" autocomplete="off" 
                           value="{{ old('phone', $employee->phone ?? '') }}" required>
                </div>

                <div class="form-group mb-2">
                    <label for="company_id" class="az-content-label tx-11 tx-medium tx-gray-600">Affco</label>
                    <select class="form-control select2" id="company_id" name="company_id" required>
                        <option label="Choose one"></option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" 
                                {{ old('company_id', $employee->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <br>
                <button type="submit" class="btn btn-az-primary pd-x-30 mg-r-5">
                    {{ isset($employee) ? 'Update' : 'Simpan' }}
                </button>
                <button type="button" id="batal" class="btn btn-dark pd-x-30">Batal</button>
            </form>
        </div>
    </div>
</div>
<br>
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