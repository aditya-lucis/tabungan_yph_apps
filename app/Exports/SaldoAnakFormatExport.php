<?php

namespace App\Exports;

use App\Models\DataAnak;
use Maatwebsite\Excel\Concerns\FromCollection;use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class SaldoAnakFormatExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array() : array {
        $childs = DataAnak::with(['karyawan', 'program'])->get();

        // Format data Nama Orangtua, Company, dan Nama Anak
        $data = $childs->map(function ($child) {
            return [
                $child->karyawan->company->name ?? '',  // Kolom "Company"
                $child->karyawan->name ?? '',  // Kolom "Nama Orangtua"
                $child->nama, // Kolom "Nama Anak"
                $child->program->level, // Kolom "Tingkat Sekolah Anak"
                0, // Kolom "Credit"
                0, // Kolom "Debit"
                '', // Kolom "Note"
                '', // Kolom "Transaction_Date"
            ];
        })->toArray();

        return $data;
    }

    public function headings(): array {
        return [
            'Company', 'Nama_Orangtua', 'Nama_Anak', 'Tingkat_Sekolah', 
            'Debit', 'Credit', 'Note', 'Transaction_Date'
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
                    'startColor' => ['rgb' => '4FC3F7']
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
            'E' => 30, // Debit
            'F' => 20, // Credit
            'G' => 15, // Note
            'H' => 15, // Transaction Date
        ];
    }
}
