<?php

namespace App\Exports;

use App\Models\ReqApproval;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReqApprovalExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles,
    WithColumnWidths,
    WithColumnFormatting
{
    protected $start_date, $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        return ReqApproval::with(['anak.karyawan.company', 'anak', 'anak.karyawan', 'user'])
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'Companie',
            'Nama Anak',
            'Tujuan Pencairan',
            'Nominal Yang Diajukan',
            'Tanggal Pengajuan',
            'Tipe Pencairan',
            'Status'
        ];
    }

    public function map($item): array
    {
        static $i = 1;

        return [
            $i++,
            optional($item->anak->karyawan)->name,
            optional(optional($item->anak->karyawan)->company)->name,
            optional($item->anak)->nama,
            $item->reason,
            $item->nominal,
            Carbon::parse($item->created_at)->translatedFormat('d F Y'),
            $item->isreimburst == 1 ? 'Reimburse' : 'Cash Advance',
            match ($item->status) {
                0 => 'New',
                1 => 'Approved',
                2 => 'Rejected',
                default => '',
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4FC3F7'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // ribuan, 2 desimal
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 30,
            'F' => 20,
            'G' => 20,
            'H' => 18,
            'I' => 15,
        ];
    }
}
