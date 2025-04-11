<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::insert([
            ['name' => 'PT Persada Inti Utama', 'aliase' => 'PIU'],
            ['name' => 'PT Sokka Tama Fiber', 'aliase' => 'STF'],
            ['name' => 'PT Persada Puri Tama', 'aliase' => 'PPT'],
            ['name' => 'PT Persada Medika Dharma', 'aliase' => 'PMD'],
            ['name' => 'PT Persada Konstruksi', 'aliase' => 'PKU'],
            ['name' => 'PT Persada Puri Tama Servis', 'aliase' => 'PTS'],
            ['name' => 'PT Aplikasi Klinik Indonesia', 'aliase' => 'AKI'],
            ['name' => 'PT Sokka Kreatif Teknologi', 'aliase' => 'SKT'],
            ['name' => 'PT Popnet Indonesia', 'aliase' => 'Popnet'],
            ['name' => 'PT Persada Karyatama', 'aliase' => 'PKT'],
            ['name' => 'Rumah Sakit Helsa Jatirahayu', 'aliase' => 'HelsaJTR'],
            ['name' => 'Rumah Sakit Helsa Cikampek', 'aliase' => 'HelsaCKM'],
            ['name' => 'Rumah Sakit Helsa Citeureup', 'aliase' => 'HelsaCTR'],
            ['name' => 'Rumah Sakit Helsa Ciputat', 'aliase' => 'HelsaCPT'],
            ['name' => 'Klinik Utama Bunda Nanda', 'aliase' => 'BundaNanda'],
        ]);
    }
}

