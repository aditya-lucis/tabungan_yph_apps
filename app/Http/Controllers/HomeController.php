<?php

namespace App\Http\Controllers;

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
            ->join('programs', 'data_anaks.id_program', '=', 'programs.id')
            ->select(
                'transactions.id',
                'transactions.credit',
                'transactions.debit',
                'transactions.created_at',
                'programs.level as jenjang',
                'data_anaks.id as anak_id'
            )
            ->get();

        $grouped = $transactions->groupBy(function ($item) {
            $tahun = Carbon::parse($item->created_at)->year;
            $semester = Carbon::parse($item->created_at)->month <= 6 ? 'Semester Genap' : 'Semester Ganjil';
            return $item->jenjang . '-' . $tahun . '-' . $semester;
        });

        $saldoPerJenjang = collect();
        foreach ($grouped as $key => $items) {
            [$jenjang, $tahun, $semester] = explode('-', $key);

            $saldoPerJenjang->push((object) [
                'jenjang'       => $jenjang,
                'tahun'         => (int) $tahun,
                'semester'      => $semester,
                'jumlah_anak'   => $items->pluck('anak_id')->unique()->count(),
                'total_credit'  => $items->sum('credit'),
                'total_debit'   => $items->sum('debit'),
            ]);
        }

        // =============================================
        // Logika Baru: Menggabungkan saldo per tahun
        // =============================================
        $saldoTahunan = collect();
        $saldoPerJenjang->groupBy(['jenjang', 'tahun'])->each(function ($items, $key) use ($saldoTahunan) {
            $jenjang = $key[0];
            $tahun = $key[1];

            $genap = $items->firstWhere('semester', 'Semester Genap');
            $ganjil = $items->firstWhere('semester', 'Semester Ganjil');

            $totalCreditGenap = $genap ? $genap->total_credit : 0;
            $totalDebitGenap = $genap ? $genap->total_debit : 0;

            $totalCreditGanjil = $ganjil ? $ganjil->total_credit : 0;
            $totalDebitGanjil = $ganjil ? $ganjil->total_debit : 0;

            // Jumlah anak unik (unik per tahun)
            $jumlahAnak = $items->sum('jumlah_anak');

            $saldoTahunan->push((object) [
                'jenjang'       => $jenjang,
                'tahun'         => $tahun,
                'jumlah_anak'   => $jumlahAnak,
                'total_credit'  => $totalCreditGenap + $totalCreditGanjil,
                'total_debit'   => $totalDebitGenap + $totalDebitGanjil,
            ]);
        });

        // urutkan kembali hasilnya
        $order = ['SD', 'SMP', 'SMA', 'Perguruan Tinggi'];
        $saldoPerJenjang = $saldoTahunan->sortBy(function ($item) use ($order) {
            return array_search($item->jenjang, $order) . '-' . $item->tahun;
        })->values();


        // ----------------------------
        // SEMESTER DATA
        // ----------------------------
        $semesterTx = Transaction::with('anak.karyawan.company', 'anak.program')
            ->join('data_anaks', 'transactions.id_anak', '=', 'data_anaks.id')
            ->join('employees', 'data_anaks.id_karyawan', '=', 'employees.id')
            ->join('companies', 'employees.company_id', '=', 'companies.id')
            ->join('programs', 'data_anaks.id_program', '=', 'programs.id')
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
            $tahun = \Carbon\Carbon::parse($item->created_at)->year;
            $semester = \Carbon\Carbon::parse($item->created_at)->month <= 6 ? 'Semester Genap' : 'Semester Ganjil';
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
            foreach ($records->sortBy(fn($i) => $i->tahun . $i->semester) as $rec) {
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
            return $item->company_id . '-' . $item->program_level . '-' . \Carbon\Carbon::parse($item->created_at)->year;
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
        $yearlySaldo = $yearlyData->groupBy(fn($i) => $i->company_id . '-' . $i->program_level);
        foreach ($yearlySaldo as $key => $records) {
            $running = 0;
            foreach ($records->sortBy('tahun') as $rec) {
                $running += $rec->total_credit - $rec->total_debit;
                $rec->saldo_akhir = $running;
            }
        }
        $yearlyData = $yearlySaldo->flatten();


        // ----------------------------
        return view('home.home', compact('saldoPerJenjang', 'semesterData', 'yearlyData'));
    }


}
