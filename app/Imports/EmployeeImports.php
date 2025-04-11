<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImports implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $collection)
    {
         // Simpan nilai terakhir yang terbaca
         $lastCompany = null;

        foreach ($collection as $row) {

            $companyname = trim($row['affco'] ?? '') ?: $lastCompany;

            // Skip kalau masih kosong
            if (!$companyname) continue;

            // Simpan nilai ini untuk baris berikutnya
            $lastCompany = $companyname;

            $affco = Company::where('aliase', $companyname)->first();
            if (!$affco) continue;

            if ($affco != null) {
                Employee::create([
                    'name' => $row['nama'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'company_id' => $affco->id,
                    'isactive' => true
                ]);
            }
        }
    }
}
