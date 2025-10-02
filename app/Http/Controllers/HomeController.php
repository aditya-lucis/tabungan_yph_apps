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
        $saldoPerJenjang = Transaction::with('anak.program')
            ->selectRaw("
                programs.level as jenjang,
                EXTRACT(YEAR FROM transactions.created_at) as tahun,
                COUNT(DISTINCT data_anaks.id) as jumlah_anak,
                SUM(CASE WHEN EXTRACT(MONTH FROM transactions.created_at) BETWEEN 1 AND 6 
                     THEN transactions.credit - transactions.debit ELSE 0 END) as semester_genap,
                SUM(CASE WHEN EXTRACT(MONTH FROM transactions.created_at) BETWEEN 7 AND 12 
                     THEN transactions.credit - transactions.debit ELSE 0 END) as semester_ganjil,
                SUM(transactions.credit - transactions.debit) as total_per_tahun
            ")
            ->join('data_anaks', 'transactions.id_anak', '=', 'data_anaks.id')
            ->join('programs', 'data_anaks.id_program', '=', 'programs.id')
            ->groupBy('programs.level', 'tahun')
            ->get();

        $order = ['SD', 'SMP', 'SMA', 'Perguruan Tinggi'];
        $saldoPerJenjang = $saldoPerJenjang
            ->sortBy(function ($item) use ($order) {
                return array_search($item->jenjang, $order) . '-' . $item->tahun;
            })
            ->values();

        $semesterData = Transaction::with('anak.karyawan.company', 'anak.program')
            ->selectRaw("
                companies.id AS company_id,
                companies.name AS company_name,
                programs.level AS program_level,
                EXTRACT(YEAR FROM transactions.created_at) AS tahun,
                CASE 
                    WHEN EXTRACT(MONTH FROM transactions.created_at) BETWEEN 1 AND 6 THEN 'Semester Genap'
                    ELSE 'Semester Ganjil'
                END AS semester,
                SUM(transactions.credit) AS total_credit,
                SUM(transactions.debit) AS total_debit,
                SUM(SUM(transactions.credit) - SUM(transactions.debit)) 
                    OVER (
                        PARTITION BY companies.id, programs.level
                        ORDER BY EXTRACT(YEAR FROM transactions.created_at),
                                CASE WHEN EXTRACT(MONTH FROM transactions.created_at) BETWEEN 1 AND 6 THEN 1 ELSE 2 END
                        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
                    ) AS saldo_akhir
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
                DB::raw("CASE WHEN EXTRACT(MONTH FROM transactions.created_at) BETWEEN 1 AND 6 THEN 'Semester Genap' ELSE 'Semester Ganjil' END")
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
                    SUM(SUM(transactions.credit) - SUM(transactions.debit)) 
                    OVER (
                        PARTITION BY companies.id, programs.level
                        ORDER BY EXTRACT(YEAR FROM transactions.created_at)
                        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
                    ) as saldo_akhir
            ")
            ->join('data_anaks', 'transactions.id_anak', '=', 'data_anaks.id')
            ->join('employees', 'data_anaks.id_karyawan', '=', 'employees.id')
            ->join('companies', 'employees.company_id', '=', 'companies.id')
            ->join('programs', 'data_anaks.id_program', '=', 'programs.id')
            ->groupBy('companies.id', 'companies.name', 'programs.level', 'tahun')
            ->orderBy('companies.name', 'ASC')
            ->get();

        return view('home.home', compact('saldoPerJenjang', 'semesterData', 'yearlyData'));
    }

}
