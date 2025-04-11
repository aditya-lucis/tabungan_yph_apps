@extends('layout.body')

@section('css')
<link href="{{ asset('assets/master/lib/datatables.net-dt/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    .table-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid black;
        width: 100%;
    }
    #example2 {
        width: 100% !important;
    }
    .no-border-top {
        border-top: none !important;
    }
</style>
@endsection

@section('content')
    <div class="table-card table-responsive">
    <h3>Saldo Per Jenjang Pendidikan</h3>
        <table class="table table-bordered">
        <thead>
            <tr>
                <th>Jenjang Pendidikan</th>
                <th>Tahun</th>
                <th>Jumlah Anak</th>
                <th>Semester 1</th>
                <th>Semester 2</th>
                <th>Total Per Tahun</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSemester1 = 0;
                $totalSemester2 = 0;
                $totalTahunan = 0;
            @endphp
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row['jenjang_pendidikan'] }}</td>
                    <td>{{ $row['tahun'] }}</td>
                    <td>{{ $row['jumlah_anak'] }}</td>
                    <td>Rp {{ number_format($row['semester_1'], 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($row['semester_2'], 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($row['total'], 2, ',', '.') }}</td>
                </tr>
                @php
                    $totalSemester1 += $row['semester_1'];
                    $totalSemester2 += $row['semester_2'];
                    $totalTahunan += $row['total'];
                @endphp
            @endforeach
            <tr>
                <td colspan="3"><strong>TOTAL KESELURUHAN</strong></td>
                <td><strong>Rp {{ number_format($totalSemester1, 2, ',', '.') }}</strong></td>
                <td><strong>Rp {{ number_format($totalSemester2, 2, ',', '.') }}</strong></td>
                <td><strong>Rp {{ number_format($totalTahunan, 2, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
    </div>
    <br>
    @php
        $groupedData = $semesterData->groupBy('company_name')->map(function ($programs) {
            return $programs->groupBy('program_level');
        });

        $totalCredit = 0;
        $totalDebit = 0;
        $totalLast = 0;
    @endphp

    <div class="table-card table-responsive">
        <h3>Saldo Per Semester</h3>
        <table class="table table-bordered" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th>Perusahaan</th>
                    <th>Program</th>
                    <th>Tahun</th>
                    <th>Semester</th>
                    <th>Total Credit</th>
                    <th>Total Debit</th>
                    <th>Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedData as $companyName => $programs)
                    @php $showCompany = true; @endphp
                    @foreach($programs as $programLevel => $records)
                        @php
                            $lastYear = null;
                            $showProgram = true;
                        @endphp

                        @foreach($records->sortBy(function ($item) {
                            return $item->tahun * 10 + ($item->semester === 'Semester 1' ? 1 : 2);
                        }) as $data)
                            <tr>
                                <td>{{ $showCompany ? $companyName : '' }}</td>

                                {{-- Tampilkan program level hanya saat tahun baru atau pertama --}}
                                <td>
                                    @if ($showProgram || $lastYear !== $data->tahun)
                                        {{ $programLevel }}
                                    @endif
                                </td>

                                <td>{{ $data->tahun }}</td>
                                <td>{{ $data->semester }}</td>
                                <td>Rp {{ number_format($data->total_credit, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($data->total_debit, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($data->saldo_akhir, 0, ',', '.') }}</td>
                            </tr>

                            @php
                                $showCompany = false;
                                $showProgram = false;
                                $lastYear = $data->tahun;

                                $totalCredit += $data->total_credit;
                                $totalDebit += $data->total_debit;
                                $totalLast += $data->saldo_akhir;
                            @endphp
                        @endforeach
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="3"></td>
                    <td><b>TOTAL</b></td>
                    <td><b>Rp {{ number_format($totalCredit, 2, ',', '.') }}</b></td>
                    <td><b>Rp {{ number_format($totalDebit, 2, ',', '.') }}</b></td>
                    <td><b>Rp {{ number_format($totalLast, 2, ',', '.') }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>

    <br>
    <div class="table-card table-responsive">
    <h3>Saldo Per Tahun</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Perusahaan</th>
                    <th>Program</th>
                    <th>Tahun</th>
                    <th>Total Credit</th>
                    <th>Total Debit</th>
                    <th>Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
            @php
                $Namecompany = '';
                $allCredit = 0;
                $allDebit = 0;
                $allLast = 0;
            @endphp
            @foreach($yearlyData as $data)
                @php
                    $isSameName = $data->name == $Namecompany;
                @endphp
                <tr>
                    <td class="{{ $isSameName ? 'no-border-top' : '' }}">
                        {{ !$isSameName ? $data->name : '' }}
                    </td>
                    <td>{{ $data->level }}</td>
                    <td>{{ $data->tahun }}</td>
                    <td>Rp {{ number_format($data->total_credit, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_debit, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->saldo_akhir, 0, ',', '.') }}</td>
                </tr>
                @php
                    $Namecompany = $data->name;
                    $allCredit += $data->total_credit;
                    $allDebit += $data->total_debit;
                    $allLast += $data->saldo_akhir;
                @endphp
            @endforeach
            <tr>
                <td colspan="2"></td>
                <td><b>TOTAL</b></td>
                <td><b>Rp {{ number_format($allCredit, 0, ',', '.') }} </b></td>
                <td><b>Rp {{ number_format($allDebit, 0, ',', '.') }} </b></td>
                <td><b>Rp {{ number_format($allLast, 0, ',', '.') }} </b></td>
            </tr>
        </tbody>
        </table>
    </div>
@endsection

@section('script')
<script src="{{ asset('assets/master/lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net-dt/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/select2/js/select2.min.js') }}"></script> 

<script>
$(document).ready(function() {
    $('#example2').DataTable({
        responsive: true,
        searching: false, // Menonaktifkan pencarian
        paging: false, // Aktifkan pagination
        lengthChange: false, // Menonaktifkan opsi jumlah data per halaman
        info: false, // Menyembunyikan informasi jumlah data
        ordering: false, // Menonaktifkan fitur sorting
        language: {
            emptyTable: "Tidak ada data tersedia",
            paginate: {
                previous: "Sebelumnya",
                next: "Selanjutnya"
            }
        }
    });
});
</script>
@endsection
