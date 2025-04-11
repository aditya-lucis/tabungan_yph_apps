<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserEmployeeImports implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $lastCompany = null;

        foreach ($collection as $row) {

            $companyname = trim($row['perusahaan'] ?? '') ?: $lastCompany;
            // Skip kalau masih kosong
            if (!$companyname) continue;

            // Simpan nilai ini untuk baris berikutnya
            $lastCompany = $companyname;

            $affco = Company::where('name', $companyname)->first();
            if (!$affco) continue;

            $employee = Employee::where([
                ['name', $row['nama']],
                ['email', $row['email']],
                ['phone', $row['phone']],
                ['company_id', $affco->id],
                ['isactive', true]
            ])->first();

            if ($employee != null) {
                User::create([
                    'name' => $row['nama'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'password' => Hash::make($row['password']),
                    'id_employee' => $employee->id,
                    'status' => true,
                    'role' => 'krw',
                ]);
            }
        }
    }
}
