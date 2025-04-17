<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UserEmployeeMigrate implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    /**
     * Mengambil data Employee yang belum ada di User, termasuk nama perusahaan.
     */
    public function collection()
    {
        return Employee::where('isactive', true)
            ->whereDoesntHave('user', function ($query) {
                $query->whereColumn('users.id_employee', 'employees.id'); // Ini yang disarankan
            })
            ->with('company')
            ->orderBy('company_id')
            ->orderBy('name', 'ASC')
            ->get();
    }


    /**
     * Menentukan header kolom.
     */
    public function headings(): array
    {
        return ['Perusahaan', 'Nama', 'Email', 'Phone', 'Password'];
    }

    /**
     * Mapping data Employee ke format Excel.
     */
    public function map($employee): array
    {
        return [
            $employee->company ? $employee->company->name : 'N/A', // Perusahaan di kolom pertama
            $employee->name,
            $employee->email,
            $employee->phone,
            '' // Password dikosongkan
        ];
    }

    /**
     * Mengatur gaya tampilan untuk header.
     */
    public function styles(Worksheet $sheet)
    {
        // Header di baris pertama (A1:E1)
        $sheet->getStyle('A1:E1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '90EE90'], // Warna hijau cerah
            ],
            'font' => [
                'bold' => true,
            ]
        ]);

        return [];
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data = $this->collection();

                $startRow = 2; // Karena header di baris 1
                $currentCompany = null;
                $mergeStartRow = $startRow;

                foreach ($data as $index => $employee) {
                    $rowNum = $startRow + $index;
                    $companyName = $employee->company ? $employee->company->name : 'N/A';

                    if ($currentCompany === null) {
                        $currentCompany = $companyName;
                    }

                     // Jika ganti perusahaan atau data terakhir
                     $isLast = $index === count($data) - 1;
                     if ($companyName !== $currentCompany || $isLast) {
                        $mergeEndRow = $isLast && $companyName === $currentCompany ? $rowNum : $rowNum - 1;

                        if ($mergeEndRow > $mergeStartRow) {
                            $cellRange = "A{$mergeStartRow}:A{$mergeEndRow}";
                            $sheet->mergeCells($cellRange);
                            $sheet->getStyle($cellRange)->getAlignment()->setVertical('center')->setHorizontal('center');
                        }
                        
                         // Set untuk perusahaan selanjutnya
                         $currentCompany = $companyName;
                         $mergeStartRow = $rowNum;
                     }

                }
            }
        ];
    }
}