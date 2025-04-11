<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class FormatEmplyeeExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array() : array {
        $companies = Company::all();

        $data = $companies->map(function ($company){
            return [
                $company->aliase ?? '',
                '',
                '',
                '',
            ];
        })->toArray();

        return $data;
    }

    public function headings(): array {
        return [
            'Affco', 'Nama', 'Email', 'Phone'
        ];
    }

    public function styles(Worksheet $sheet){
        return [
            // Atur header (baris pertama) menjadi bold dan berwarna oranye
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFF00']
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
            'B' => 25, // Nama
            'C' => 20, // Email
            'D' => 20, // Phone
        ];
    }
}
