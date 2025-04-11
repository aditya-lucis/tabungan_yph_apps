<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\DataAnak;
use App\Models\Employee;
use App\Models\Program;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataSaldoAnakImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        $tempBalances = [];

        // Simpan nilai terakhir yang terbaca
        $lastOrtu = null;
        $lastCompany = null;
        $lastAnak = null;
        $lastTingkat = null;
        $createdAt = null;

        foreach ($collection as $row) {
            $namaOrtu = trim($row['nama_orangtua'] ?? '') ?: $lastOrtu;
            $companyName = trim($row['company'] ?? '') ?: $lastCompany;
            $namaAnak = trim($row['nama_anak'] ?? '') ?: $lastAnak;
            $tingkat = trim($row['tingkat_sekolah'] ?? '') ?: $lastTingkat;
            $tglTransaksi = $row['transaction_date'] ?? null;

            // Skip kalau masih kosong
            if (!$namaOrtu || !$companyName || !$namaAnak || !$tingkat) continue;

            // Simpan nilai ini untuk baris berikutnya
            $lastOrtu = $namaOrtu;
            $lastCompany = $companyName;
            $lastAnak = $namaAnak;
            $lastTingkat = $tingkat;

            $company = Company::where('name', $companyName)->first();
            if (!$company) continue;

            $employee = Employee::where('name', $namaOrtu)->where('company_id', $company->id)->first();
            if (!$employee) continue;

            $program = Program::where('level', $tingkat)->first();
            if (!$program) continue;

            $anak = DataAnak::where([
                ['nama', $namaAnak],
                ['id_karyawan', $employee->id],
                ['id_program', $program->id]
            ])->first();

            if (!$anak) continue;

            $idAnak = $anak->id;

            if ($tglTransaksi) {
                if (is_numeric($tglTransaksi)) {
                    // Format Excel datetime ke Carbon
                    $createdAt = Date::excelToDateTimeObject($tglTransaksi)->format('Y-m-d 00:00:00');
                }else {
                     // Format string biasa
                    $createdAt = Carbon::parse($tglTransaksi)->format('Y-m-d 00:00:00');
                }
            }

            // Ambil saldo sebelumnya
            $previousBalance = $tempBalances[$idAnak] ?? Transaction::where('id_anak', $idAnak)->latest()->first()->final_balance ?? 0;

            $credit = floatval($row['credit'] ?? 0);
            $debit = floatval($row['debit'] ?? 0);
            $runningBalance = $previousBalance + $credit;
            $finalBalance = $runningBalance - $debit;

            Transaction::unsetEventDispatcher();
            Transaction::unguard(); // <- ini kunci!

            Transaction::create([
                'id_anak' => $idAnak,
                'previous_balance' => $previousBalance,
                'credit' => $credit,
                'running_balance' => $runningBalance,
                'debit' => $debit,
                'final_balance' => $finalBalance,
                'notes' => $row['note'] ?? null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            Transaction::reguard(); // <- aktifkan kembali kalau sudah
            Transaction::setEventDispatcher(app('events'));

            $tempBalances[$idAnak] = $finalBalance;
        }
    }
}
