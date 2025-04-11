<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\DataAnak;
use App\Models\Employee;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataAnakImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $lastCompany = null;
        $lastEmployee = null;

        foreach ($rows as $row) {
            // Simpan company jika tersedia
            if (!empty($row['company'])) {
                $lastCompany = Company::where('name', trim($row['company']))->first();
            }

            if (!$lastCompany) continue;

            // Simpan employee jika tersedia
            if (!empty($row['nama_orangtua'])) {
                $lastEmployee = Employee::where('name', trim($row['nama_orangtua']))
                    ->where('company_id', $lastCompany->id)
                    ->first();
            }

            if (!$lastEmployee) continue;

            // Skip jika employee sudah punya 2 anak
            $anakCount = DataAnak::where('id_karyawan', $lastEmployee->id)->count();
            if ($anakCount >= 2) continue;

            // Ambil data anak dan info lainnya
            $namaAnak = explode(',', $row['nama_anak']);
            $tingkat = explode(',', $row['tingkat_sekolah']);
            $namaSekolah = explode(',', $row['nama_sekolah']);
            $tempatLahir = explode(',', $row['tempat_lahir']);
            $tglLahir = explode(',', $row['tanggal_lahir']);

            foreach ($namaAnak as $i => $nama) {
                $level = trim($tingkat[$i] ?? '');
                $program = Program::where('level', $level)->first();
                if (!$program) continue;

                $tglExcel = trim($tglLahir[$i] ?? '');
                $tglLahirFix = null;

                if (is_numeric($tglExcel)) {
                    $tglLahirFix = Date::excelToDateTimeObject($tglExcel)->format('Y-m-d');
                } elseif (!empty($tglExcel)) {
                    $tglLahirFix = Carbon::parse($tglExcel)->format('Y-m-d');
                }

                // Skip jika anak sudah pernah diinput
                $isExist = DataAnak::where('nama', trim($nama))
                    ->where('id_karyawan', $lastEmployee->id)
                    ->where('tgl_lahir', $tglLahirFix)
                    ->exists();

                if ($isExist) continue;

                // Tambah anak baru
                DataAnak::create([
                    'nama' => trim($nama),
                    'id_karyawan' => $lastEmployee->id,
                    'id_program' => $program->id,
                    'nama_sekolah' => trim($namaSekolah[$i] ?? ''),
                    'tempat_lahir' => trim($tempatLahir[$i] ?? ''),
                    'tgl_lahir' => $tglLahirFix,
                    'fc_ktp' => '404.jpg',
                    'surat_sekolah' => '404.jpg',
                    'fc_raport' => '404.jpg',
                    'fc_rek_sekolah' => '404.jpg',
                ]);

                // Cek ulang count dan break jika sudah 2 anak
                $anakCount++;
                if ($anakCount >= 2) break;
            }
        }
    }
}