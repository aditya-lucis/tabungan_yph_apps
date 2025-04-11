<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $order = ['SD', 'SMP', 'SMA', 'Perguruan Tinggi'];

        $data = Program::with(['anak.transaction'])->get()
            ->map(function ($program) {
                $perJenjang = [];
        
                foreach ($program->anak as $anak) {
                    foreach ($anak->transaction as $trx) {
                        $tahun = \Carbon\Carbon::parse($trx->created_at)->year;
                        $bulan = \Carbon\Carbon::parse($trx->created_at)->month;
                        $semester = $bulan <= 6 ? 'Semester 1' : 'Semester 2';
        
                        $key = $program->level . '-' . $tahun;
        
                        if (!isset($perJenjang[$key])) {
                            $perJenjang[$key] = [
                                'jenjang_pendidikan' => $program->level,
                                'tahun' => $tahun,
                                'jumlah_anak' => 0,
                                'semester_1' => 0,
                                'semester_2' => 0,
                                'total' => 0,
                            ];
                        }
        
                        $saldo = $trx->credit - $trx->debit;
        
                        if ($semester === 'Semester 1') {
                            $perJenjang[$key]['semester_1'] += $saldo;
                        } else {
                            $perJenjang[$key]['semester_2'] += $saldo;
                        }
        
                        $perJenjang[$key]['total'] += $saldo;
                    }
                }
        
                // Tambahkan jumlah anak hanya satu kali per program
                foreach ($perJenjang as &$val) {
                    $val['jumlah_anak'] = $program->anak->count();
                }
        
                return array_values($perJenjang);
            })
            ->flatten(1)
            ->sortBy(function ($item) use ($order) {
                return array_search($item['jenjang_pendidikan'], $order) . '-' . $item['tahun'];
            })->values();

                $semesterData = Transaction::with('anak.karyawan.company', 'anak.program')
                    ->selectRaw("
                        companies.id AS company_id,
                        companies.name AS company_name,
                        programs.level AS program_level,
                        EXTRACT(YEAR FROM transactions.created_at) AS tahun,
                        CASE 
                            WHEN EXTRACT(MONTH FROM transactions.created_at) BETWEEN 1 AND 6 THEN 'Semester 1'
                            ELSE 'Semester 2'
                        END AS semester,
                        SUM(transactions.credit) AS total_credit,
                        SUM(transactions.debit) AS total_debit,
                        (SUM(transactions.credit) - SUM(transactions.debit)) AS saldo_akhir
                    ")
                    ->join('data_anaks', 'transactions.id_anak', '=', 'data_anaks.id')
                    ->join('employees', 'data_anaks.id_karyawan', '=', 'employees.id')
                    ->join('companies', 'employees.company_id', '=', 'companies.id')
                    ->join('programs', 'data_anaks.id_program', '=', 'programs.id')
                    ->groupBy(
                        'companies.id',
                        'companies.name',
                        'programs.level',
                        DB::raw('EXTRACT(YEAR FROM transactions.created_at)'),
                        DB::raw("CASE WHEN EXTRACT(MONTH FROM transactions.created_at) BETWEEN 1 AND 6 THEN 'Semester 1' ELSE 'Semester 2' END")
                    )
                    ->orderBy('companies.name', 'ASC')
                    ->get();
   

                $yearlyData = Transaction::with('anak.orangtua.company', 'anak.program')
                ->selectRaw("
                    companies.id,
                    companies.name,
                    programs.level,
                    EXTRACT(YEAR FROM transactions.created_at) as tahun,
                    SUM(transactions.credit) as total_credit,
                    SUM(transactions.debit) as total_debit,
                    (SUM(transactions.credit) - SUM(transactions.debit)) as saldo_akhir
                ")
                ->join('data_anaks', 'transactions.id_anak', '=', 'data_anaks.id')
                ->join('employees', 'data_anaks.id_karyawan', '=', 'employees.id')
                ->join('companies', 'employees.company_id', '=', 'companies.id')
                ->join('programs', 'data_anaks.id_program', '=', 'programs.id')
                ->groupBy('companies.id', 'companies.name', 'programs.level', 'tahun')
                ->orderBy('companies.name', 'ASC')
                ->get();

        return view('home.home', compact('data', 'semesterData', 'yearlyData'));
    }

}
