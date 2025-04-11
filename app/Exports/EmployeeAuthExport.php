<?php

namespace App\Exports;
namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeAuthExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * Ambil data untuk diexport
     */
    public function collection()
    {
        return Employee::where('company_id', $this->company_id)
            ->where('isactive', true)
            ->with('user') // Eager loading relasi user
            ->orderBy('name', 'ASC')
            ->get()
            ->map(function ($employee) {
                return [
                    'Nama'      => $employee->name,
                    'Email'     => $employee->email,
                    'Phone'     => $employee->phone,
                    'Password'  => $employee->user ? Crypt::decrypt($employee->user->password) : 'N/A'
                ];
            });
    }

    /**
     * Buat header tabel
     */
    public function headings(): array
    {
        return [
            ["Daftar Data Autentikasi Karyawan"], // Baris judul
            ["Nama", "Email", "Phone", "Password"] // Header kolom
        ];
    }

    /**
     * Atur gaya tampilan
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]], // Judul utama
            2    => ['font' => ['bold' => true, 'size' => 12]], // Header kolom
        ];
    }

    /**
     * Nama sheet dalam file Excel
     */
    public function title(): string
    {
        return "Data Karyawan";
    }
}