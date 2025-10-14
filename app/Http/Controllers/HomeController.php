<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    
    public function index()
    {
        // ----------------------------
        // SALDO PER JENJANG
        // ----------------------------

        $transactions = Transaction::with('anak.program')
            ->join('data_anaks', 'transactions.id_anak', '=', 'data_anaks.id')
            ->join('employees', 'data_anaks.id_karyawan', '=', 'employees.id')
            ->join('programs', 'data_anaks.id_program', '=', 'programs.id')
            ->where('employees.isactive', 1)
            ->select(
                'programs.level as jenjang',
                'data_anaks.id as id_anak',
                'transactions.credit',
                'transactions.debit',
                'transactions.created_at'
            )
            ->get();

        // Group per jenjang-tahun-semester
        $semesterGrouped = $transactions->groupBy(function ($item) {
            $tahun = Carbon::parse($item->created_at)->year;
            $semester = Carbon::parse($item->created_at)->month <= 6 ? 'Genap' : 'Ganjil';
            return $item->jenjang . '-' . $tahun . '-' . $semester;
        });

        // Hitung mutasi per semester
        $semesterSaldo = collect();
        foreach ($semesterGrouped as $key => $items) {
            [$jenjang, $tahun, $semester] = explode('-', $key);

            $semesterSaldo->push((object)[
                'jenjang'       => $jenjang,
                'tahun'         => (int)$tahun,
                'semester'      => $semester,
                'jumlah_anak'   => $items->pluck('id_anak')->unique()->count(),
                'mutasi'        => $items->sum('credit') - $items->sum('debit'),
            ]);
        }

        // Gabung per jenjang dan tahun
        $saldoPerJenjang = collect();
        $semesterOrder = ['Genap' => 1, 'Ganjil' => 2];

        foreach ($semesterSaldo->groupBy('jenjang') as $jenjang => $records) {
            // urutkan berdasarkan tahun + semester
            $sorted = $records->sortBy(fn($i) => $i->tahun * 10 + $semesterOrder[$i->semester]);

            $running = 0;
            foreach ($sorted as $rec) {
                $running += $rec->mutasi; // running lintas tahun

                $rowKey = $jenjang . '-' . $rec->tahun;

                if (!isset($saldoPerJenjang[$rowKey])) {
                     $saldoPerJenjang[$rowKey] = (object)[
                        'jenjang'          => $jenjang,
                        'tahun'            => $rec->tahun,
                        'jumlah_anak'      => 0,
                        'semester_genap'   => 0,
                        'semester_ganjil'  => 0,
                        'total_tahun'      => 0,
                     ];
                }

                if ($rec->semester === 'Genap') {
                    $saldoPerJenjang[$rowKey]->semester_genap = $running;
                }else {
                    $saldoPerJenjang[$rowKey]->semester_ganjil = $running;
                }

                $saldoPerJenjang[$rowKey]->jumlah_anak = max($saldoPerJenjang[$rowKey]->jumlah_anak, $rec->jumlah_anak);

                 // saldo akhir tahun = saldo setelah semester ganjil
                $saldoPerJenjang[$rowKey]->total_tahun = $running;
            }
        }

        // Urutkan sesuai jenjang
        $order = ['SD','SMP','SMA','Perguruan Tinggi'];
        $saldoPerJenjang = $saldoPerJenjang->sortBy(function($item) use ($order) {
            return array_search($item->jenjang, $order).'-'.$item->tahun;
        })->values();

        // ----------------------------
        // SEMESTER DATA
        // ----------------------------
        $semesterTx = Transaction::with('anak.karyawan.company', 'anak.program')
            ->join('data_anaks', 'transactions.id_anak', '=', 'data_anaks.id')
            ->join('employees', 'data_anaks.id_karyawan', '=', 'employees.id')
            ->join('companies', 'employees.company_id', '=', 'companies.id')
            ->join('programs', 'data_anaks.id_program', '=', 'programs.id')
            ->where('employees.isactive', true)
            ->select(
                'transactions.id',
                'transactions.credit',
                'transactions.debit',
                'transactions.created_at',
                'companies.id as company_id',
                'companies.name as company_name',
                'programs.level as program_level'
            )
            ->get();

        $semesterGrouped = $semesterTx->groupBy(function ($item) {
            $tahun = Carbon::parse($item->created_at)->year;
            $semester = Carbon::parse($item->created_at)->month <= 6 ? 'Semester Genap' : 'Semester Ganjil';
            return $item->company_id . '-' . $item->program_level . '-' . $tahun . '-' . $semester;
        });

        $semesterData = collect();
        foreach ($semesterGrouped as $key => $items) {
            [$companyId, $programLevel, $tahun, $semester] = explode('-', $key);

            $semesterData->push((object) [
                'company_id'   => $companyId,
                'company_name' => $items->first()->company_name,
                'program_level'=> $programLevel,
                'tahun'        => (int) $tahun,
                'semester'     => $semester,
                'total_credit' => $items->sum('credit'),
                'total_debit'  => $items->sum('debit'),
            ]);
        }

        // running saldo per company + program
        $groupedSaldo = $semesterData->groupBy(fn($i) => $i->company_id . '-' . $i->program_level);
        foreach ($groupedSaldo as $key => $records) {
            $running = 0;
            foreach ($records->sortBy(function($i){
                $semesterNumber = $i->semester === 'Semester Genap' ? 1 : 2;
                return $i->tahun * 10 + $semesterNumber;
            }) as $rec) {
                $running += $rec->total_credit - $rec->total_debit;
                $rec->saldo_akhir = $running;
            }
        }
        $semesterData = $groupedSaldo->flatten();


        // ----------------------------
        // YEARLY DATA
        // ----------------------------
        $yearlyTx = Transaction::with('anak.orangtua.company', 'anak.program')
            ->join('data_anaks', 'transactions.id_anak', '=', 'data_anaks.id')
            ->join('employees', 'data_anaks.id_karyawan', '=', 'employees.id')
            ->join('companies', 'employees.company_id', '=', 'companies.id')
            ->join('programs', 'data_anaks.id_program', '=', 'programs.id')
            ->where('employees.isactive', true)
            ->select(
                'transactions.id',
                'transactions.credit',
                'transactions.debit',
                'transactions.created_at',
                'companies.id as company_id',
                'companies.name as company_name',
                'programs.level as program_level'
            )
            ->get();

        $yearlyGrouped = $yearlyTx->groupBy(function ($item) {
            return $item->company_id . '-' . $item->program_level . '-' . Carbon::parse($item->created_at)->year;
        });

        $yearlyData = collect();
        foreach ($yearlyGrouped as $key => $items) {
            [$companyId, $programLevel, $tahun] = explode('-', $key);

            $yearlyData->push((object) [
                'company_id'   => $companyId,
                'company_name' => $items->first()->company_name,
                'program_level'=> $programLevel,
                'tahun'        => (int) $tahun,
                'total_credit' => $items->sum('credit'),
                'total_debit'  => $items->sum('debit'),
            ]);
        }

        // running saldo per company + program
        $yearlySaldo = $yearlyData
            ->groupBy(fn($i) => $i->company_id . '-' . $i->program_level)
            ->map(function ($records) {
                $running = 0;
                return $records
                    ->sortBy('tahun')
                    ->map(function ($rec) use (&$running) {
                        $running += $rec->total_credit - $rec->total_debit;
                        $rec->saldo_akhir = $running;
                        return $rec;
                    });
            });

        $yearlyData = $yearlySaldo->flatten();


        // ----------------------------

        $programAll = Program::withCount([
            'anak as anak_dengan_transaksi_count' => function ($query) {
                $query->whereHas('transaction')
                ->whereHas('karyawan', function ($q) {
                    $q->where('isactive', 1); // Hanya yang orangtua aktif
                });
            }
        ])->get();

        // hitung total semua anak dengan transaksi
        $sumallChild = $programAll->sum('anak_dengan_transaksi_count');

        $companychild = Company::select('companies.*')
            ->selectSub(function ($query) {
                $query->from('data_anaks')
                    ->selectRaw('COUNT(data_anaks.id)')
                    ->join('employees', 'employees.id', '=', 'data_anaks.id_karyawan')
                    ->where('employees.isactive', 1)
                    ->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('transactions')
                            ->whereColumn('transactions.id_anak', 'data_anaks.id');
                    })
                    ->whereColumn('employees.company_id', 'companies.id');
            }, 'total_anak_perusahaan')
            ->get();

            $sumAllCompanyChild = $companychild->sum('total_anak_perusahaan');

        return view('home.home', compact('saldoPerJenjang', 'semesterData', 'yearlyData', 'programAll', 'sumallChild', 'companychild', 'sumAllCompanyChild'));
    }
}
