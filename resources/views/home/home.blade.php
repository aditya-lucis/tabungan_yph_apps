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
    .chart-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px; /* jarak antar chart */
    }

    .chart-card {
        background: white;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 20px;
        flex: 1 1 45%; /* lebar sekitar 45% dan bisa menyesuaikan */
        box-sizing: border-box;
        min-width: 300px; /* agar tidak terlalu kecil di layar sempit */
    }

</style>
@endsection

@section('content')
<div class="chart-container">
    <!-- Chart 1 -->
    <div class="chart-card">
        <h3 style="color:#003399; font-weight:800; text-align:center; margin-bottom:20px;
                background:#eaf1ff; padding:6px 12px; border-radius:6px;">Total Anak Per Jenjang Pendidikan</h3>
        <div style="height:300px; width:300px; margin:auto;">
            <canvas id="programChart"></canvas>
        </div>
        <div style="margin-top:10px; text-align:center; font-weight:600;">
            Total Anak Keseluruhan: <b>{{ $sumallChild }}</b>
        </div>
    </div>

    <!-- Chart 2 -->
    <div class="chart-card" 
        style="background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.08); 
                padding:20px; max-width:420px; margin:auto;">

        @php
            // Gabungkan nama perusahaan dan total anak jadi satu label
            $companyLabels = $companychild->map(function ($item) {
                return $item->name . ' = ' . $item->total_anak_perusahaan;
            });
        @endphp

        <h3 style="color:#003399; font-weight:800; text-align:center; margin-bottom:20px;
                background:#eaf1ff; padding:6px 12px; border-radius:6px;">
            Total Anak Per Company
        </h3>

        <div style="height:300px; width:100%; display:flex; justify-content:center; align-items:center;">
            <canvas id="companyChart"></canvas>
        </div>

        <div style="margin-top:12px; text-align:center; font-weight:600; color:#001f5b;">
            Total Anak Keseluruhan: <b>{{ $sumAllCompanyChild }}</b>
        </div>
    </div>
</div>

<br>

<div class="table-card table-responsive">
    <h3>Saldo Per Jenjang Pendidikan</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Jenjang Pendidikan</th>
                <th>Tahun</th>
                <th>Jumlah Anak</th>
                <th>Semester Genap</th>
                <th>Semester Ganjil</th>
                <th>Total Per Tahun</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalGenap = 0;
                $totalGanjil = 0;
                $totalAkhir = 0;
            @endphp
            @foreach ($saldoPerJenjang as $row)
                <tr>
                    <td>{{ $row->jenjang }}</td>
                    <td>{{ $row->tahun }}</td>
                    <td>{{ $row->jumlah_anak }}</td>
                    <td>Rp {{ number_format($row->semester_genap, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($row->semester_ganjil, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($row->total_tahun, 0, ',', '.') }}</td>
                </tr>
                @php
                    $totalGenap += $row->semester_genap;
                    $totalGanjil += $row->semester_ganjil;
                    $totalAkhir += $row->total_tahun;
                @endphp
            @endforeach
            <tr>
                <td colspan="3"><strong>TOTAL KESELURUHAN</strong></td>
                <td><strong>Rp {{ number_format($totalGenap, 0, ',', '.') }}</strong></td>
                <td><strong>Rp {{ number_format($totalGanjil, 0, ',', '.') }}</strong></td>
                <td><strong>Rp {{ number_format($totalAkhir, 0, ',', '.') }}</strong></td>
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
                            return $item->tahun * 10 + ($item->semester === 'Semester Genap' ? 1 : 2);
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
                            @endphp
                        @endforeach
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="3"></td>
                    <td><b>TOTAL</b></td>
                    <td><b>Rp {{ number_format($totalCredit, 2, ',', '.') }}</b></td>
                    <td><b>Rp {{ number_format($totalDebit, 2, ',', '.') }}</b></td>
                    <td><b>Rp {{ number_format($totalCredit - $totalDebit, 2, ',', '.') }}</b></td>
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

                $groupedYearly = $yearlyData->groupBy('company_name')->map(function ($items) {
                    return $items->groupBy('program_level')->map(function ($subItems) {
                        return $subItems->sortBy('tahun');
                    });
                });

                $Namecompany = '';
                $allCredit = 0;
                $allDebit = 0;
            @endphp
            @foreach($groupedYearly as $companyName => $programs)
                <tr>
                    <td rowspan="{{ $programs->flatten()->count() }}">
                        {{ $companyName }}
                    </td>
                    @php $firstProgram = true; @endphp

                    @foreach($programs as $level => $records)
                        @foreach($records as $record)
                            @if(!$firstProgram)
                                <tr>
                            @endif

                            <td>{{ $level }}</td>
                            <td>{{ $record->tahun }}</td>
                            <td>Rp {{ number_format($record->total_credit, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($record->total_debit, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($record->saldo_akhir, 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $allCredit += $record->total_credit;
                                $allDebit += $record->total_debit;
                                $firstProgram = false;
                            @endphp
                        @endforeach
                    @endforeach
            @endforeach
            <tr>
                <td colspan="2"></td>
                <td><b>TOTAL</b></td>
                <td><b>Rp {{ number_format($allCredit, 0, ',', '.') }} </b></td>
                <td><b>Rp {{ number_format($allDebit, 0, ',', '.') }} </b></td>
                <td><b>Rp {{ number_format($allCredit - $allDebit, 0, ',', '.') }} </b></td>
            </tr>
        </tbody>
        </table>
    </div>
@endsection

@section('script')
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
<script>
    // === Chart 1: Program ===
    new Chart(document.getElementById('programChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($programAll->pluck('level')) !!},
            datasets: [{
                data: {!! json_encode($programAll->pluck('anak_dengan_transaksi_count')) !!},
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // === Chart 2: Company ===
    const ctxCompany = document.getElementById('companyChart').getContext('2d');
    new Chart(ctxCompany, {
        type: 'pie',
        data: {
            labels: {!! json_encode($companyLabels) !!},
            datasets: [{
                data: {!! json_encode($companychild->pluck('total_anak_perusahaan')) !!},
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                    '#FF9F40', '#C9CBCF', '#6EE7B7', '#F472B6'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 15,
                        padding: 12,
                        font: {
                            size: 12,
                            family: 'Arial, sans-serif'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return `${label}: ${value}`;
                        }
                    }
                }
            },
            layout: { padding: { bottom: 10 } }
        }
    });
</script>
@endsection
