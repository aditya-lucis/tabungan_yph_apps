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
    /* Styling untuk legend HTML agar rapi dan bisa di-scroll */
    #companyLegend ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    #companyLegend li {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
        font-size: 13px;
        color: #333;
    }
    #companyLegend li span {
        display: inline-block;
        width: 14px;
        height: 14px;
        margin-right: 8px;
        border-radius: 3px;
    }

    .sticky-header th {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }

    .sticky-footer td {
        position: sticky;
        bottom: 0;
        background: #f8f9fa;
        font-weight: bold;
        z-index: 10;
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
            <div style="text-align:center; color:#002776;">
                Total Anak Keseluruhan: <b>{{ $sumallChild }}</b>
            </div>
        </div>
        
        <!-- Chart Card -->
        <div class="chart-card">
            <h3 style="color:#003399; font-weight:800; text-align:center; margin-bottom:20px;
                    background:#eaf1ff; padding:6px 12px; border-radius:6px;">
                <b>Total Anak Per Company</b>
            </h3>
            <div style="height:300px; width:300px; margin:auto;">
                <canvas id="chartCompany" height="300"></canvas>
            </div>

            <!-- Legend di luar canvas agar bisa discroll -->
            <div id="chartLegend" 
                style="max-height:150px; overflow-y:auto; margin-top:10px; border:1px solid #eee; border-radius:10px; padding:10px; font-size:14px;">
            </div>

            <h4 style="text-align:center; color:#002776;">
                Total Anak Keseluruhan: <b>{{ $companychild->sum('total_anak_perusahaan') }}</b>
            </h4>
        </div>
    </div>
    <br>

    <div class="table-card table-responsive">
        <h3>Saldo Per Jenjang Pendidikan</h3>
        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
        <table class="table table-bordered">
            <thead class="sticky-header">
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
                    $graduate = '';
                @endphp
                @foreach ($saldoPerJenjang as $row)
                    <tr>
                        <td>{{ $graduate != $row->jenjang ? $row->jenjang : '' }}</td>
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
                        $graduate = $row->jenjang;
                    @endphp
                @endforeach
            </tbody>
            <tfoot class="sticky-footer">
                <tr>
                    <td colspan="3"><strong>TOTAL KESELURUHAN</strong></td>
                    <td><strong>Rp {{ number_format($totalGenap, 0, ',', '.') }}</strong></td>
                    <td><strong>Rp {{ number_format($totalGanjil, 0, ',', '.') }}</strong></td>
                    <td><strong>Rp {{ number_format($totalAkhir, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
        </div>
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
        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
            <table class="table table-bordered" style="border-collapse: collapse; width: 100%;">
                <thead class="sticky-header">
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
                </tbody>
                <tfoot class="sticky-footer">
                    <tr>
                        <td colspan="3"></td>
                        <td><b>TOTAL</b></td>
                        <td><b>Rp {{ number_format($totalCredit, 2, ',', '.') }}</b></td>
                        <td><b>Rp {{ number_format($totalDebit, 2, ',', '.') }}</b></td>
                        <td><b>Rp {{ number_format($totalCredit - $totalDebit, 2, ',', '.') }}</b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <br>
    <div class="table-card table-responsive">
    <h3>Saldo Per Tahun</h3>
        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
            <table class="table table-bordered">
                <thead class="sticky-header">
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
                </tbody>
                <tfoot class="sticky-footer">
                    <tr>
                        <td colspan="2"></td>
                        <td><b>TOTAL</b></td>
                        <td><b>Rp {{ number_format($allCredit, 0, ',', '.') }} </b></td>
                        <td><b>Rp {{ number_format($allDebit, 0, ',', '.') }} </b></td>
                        <td><b>Rp {{ number_format($allCredit - $allDebit, 0, ',', '.') }} </b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
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

    // Ambil data dari Laravel
    const levels = {!! json_encode($programAll->pluck('level')) !!};
    const totalall = {!! json_encode($programAll->pluck('anak_dengan_transaksi_count')) !!};

    // Hitung total semua anak
    const totalAnak = totalall.reduce((a, b) => a + b, 0);

    // Buat label baru dengan persentase
    const labelsWithPercent = levels.map((level, i) => {
        const percent = totalAnak > 0 ? ((totalall[i] / totalAnak) * 100).toFixed(1) : 0;
        return `${level} = ${percent}%`;
    });

    // Buat chart
    new Chart(document.getElementById('programChart'), {
        type: 'pie',
        data: {
            labels: labelsWithPercent,
            datasets: [{
                data: totalall,
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = levels[context.dataIndex];
                            const value = totalall[context.dataIndex];
                            const percent = totalAnak > 0 ? ((value / totalAnak) * 100).toFixed(1) : 0;
                            return `${label}: ${value} anak (${percent}%)`;
                        }
                    }
                }
            }
        }
    });


    // === Chart 2: Company ===

    // Buat warna otomatis sesuai jumlah data
    const generateColors = (n) => {
        const colors = [];
        for (let i = 0; i < n; i++) {
            const hue = (i * 37) % 360; // variasi warna tetap konsisten
            colors.push(`hsl(${hue}, 70%, 60%)`);
        }
        return colors;
    };

    const ctx = document.getElementById('chartCompany').getContext('2d');
    let names = {!! json_encode($companychild->pluck('name')) !!};
    let totals = {!! json_encode($companychild->pluck('total_anak_perusahaan')) !!};

    // 🔹 Urutkan data berdasarkan total_anak_perusahaan (descending)
    let combined = names.map((name, i) => ({
        name,
        total: totals[i]
    }));
    combined.sort((a, b) => b.total - a.total);

    // Update kembali array setelah diurutkan
    names = combined.map(item => item.name);
    totals = combined.map(item => item.total);

    const colors = generateColors(names.length); // warna otomatis sebanyak jumlah perusahaan

    // === Chart.js ===
    const chart = new Chart(ctx, {
        type: 'pie',
        data: {
                labels: names,
                datasets: [{
                data: totals,
                backgroundColor: colors,
                borderWidth: 1
                }]
            },
            options: {
                plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                    label: function (context) {
                        return context.label + ' = ' + context.formattedValue;
                    }
                    }
                }
                }
            }
    });

    // === Custom Legend ===
    const legendContainer = document.getElementById('chartLegend');
    legendContainer.innerHTML = ''; // pastikan kosong dulu

    names.forEach((name, i) => {
        const color = colors[i];
        const total = totals[i];
        const item = document.createElement('div');
        item.style.display = 'flex';
        item.style.alignItems = 'center';
        item.style.marginBottom = '6px';
        item.innerHTML = `
            <div style="width:14px; height:14px; background:${color}; border-radius:3px; margin-right:8px;"></div>
            <span>${name} = <b>${total}</b></span>
        `;
        legendContainer.appendChild(item);
    });

</script>
@endsection
