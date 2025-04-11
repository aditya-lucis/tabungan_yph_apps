<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\DataAnak;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class AnakFormatExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Ambil Employee yang belum ada atau hanya muncul sekali di DataAnak
        $employees = Employee::with('company')
            ->where('isactive', true)
            ->get()
            ->filter(function ($employee) {
                $count = DataAnak::where('id_karyawan', $employee->id)->count();
                return $count == 0 || $count == 1; // Jika belum ada atau baru sekali
            });

        // Format data agar hanya Nama Orangtua & Company terisi, kolom lain kosong
        $data = $employees->map(function ($employee) {
            return [
                $employee->company->name ?? '',  // Kolom "Company"
                $employee->name,                 // Kolom "Nama Orangtua"
                '', '', '', '', ''               // Kolom lain kosong
            ];
        })->toArray();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Company', 'Nama_Orangtua', 'Nama_Anak', 'Tingkat_Sekolah', 
            'Nama_Sekolah', 'Tempat_Lahir', 'Tanggal_Lahir'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Atur header (baris pertama) menjadi bold dan berwarna oranye
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFA500'] // Oranye
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Company
            'B' => 25, // Nama Orangtua
            'C' => 20, // Nama Anak
            'D' => 20, // Tingkat Sekolah
            'E' => 30, // Nama Sekolah
            'F' => 20, // Tempat Lahir
            'G' => 15, // Tanggal Lahir
        ];
    }
}
