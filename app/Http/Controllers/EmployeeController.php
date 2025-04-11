<?php

namespace App\Http\Controllers;

use App\Exports\FormatEmplyeeExport;
use App\Imports\EmployeeImports;
use App\Models\Company;
use App\Models\DataAnak;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index(Request $request) {

        if ($request->ajax()) {
            $query = Employee::with(['company'])
            ->orderBy('name', 'ASC'); // Query Builder

            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan nomor otomatis
                ->addColumn('action', function ($item) {
                    return '
                    <div class="d-flex gap-2 btn-icon-list">
                        <a id="edit" class="btn btn-warning btn-icon btn-sm" href="' . route('employee.show', $item->id) . '">
                            <i class="typcn typcn-eye"></i>
                        </a>
                        <a id="adminadd" class="btn btn-primary btn-icon btn-sm" data-id="'.$item->id.'">
                            <i class="typcn typcn-user-add"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true); // Kembalikan JSON
        }

        return view('master.employee.index');
    }

    public function create() {
        $companies = Company::all();
        return view('master.employee.create', compact('companies'));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Request diterima:', $request->all());

            // Validasi input
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'company_id' => 'required',
            ]);

            \Log::info('Data valid!');

            // Simpan data karyawan
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company_id' => $request->company_id,
                'isactive' => true // Nilai tetap true
            ]);

            \Log::info('Data berhasil disimpan:', $employee->toArray());

            // Redirect ke index dengan pesan sukses
            return redirect()->route('employee.index')->with('success', 'Data berhasil ditambahkan!');

        } catch (\Exception $e) {
            \Log::error('Terjadi kesalahan:', ['error' => $e->getMessage()]);

            // Redirect kembali ke form dengan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data!');
        }
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $companies = Company::all(); // Ambil daftar perusahaan
        return view('master.employee.create', compact('employee', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'company_id' => 'required',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_id' => $request->company_id,
        ]);

        return redirect()->route('employee.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        $dataanak = DataAnak::where('id_karyawan', $id)->get();
        return view('master.employee.detail', compact('employee', 'dataanak'));
    }

    public function toggleStatus($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->isactive = !$employee->isactive; // Toggle status
        $employee->save();
 
        // Cari user yang terkait dengan employee
        $user = User::where('id_employee', $id)->first();
        if ($user) {
            $user->status = $employee->isactive; // Samakan status user dengan employee
            $user->save();
        }
    
        return response()->json([
            'message' => 'Status berhasil diperbarui!',
            'new_status' => $employee->isactive
        ]);
    }
    
    public function getidkaryawn($id)
    {
        $employee = Employee::findOrFail($id);

        if (!$employee->isactive) { // Jika isactive == false
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan sudah tidak aktif'
            ], 400); // 400 Bad Request
        }
        
        return response()->json($employee);
    }
    
    public function createauth(Request $request)
    {
        // Cari karyawan berdasarkan ID
        $employee = Employee::findOrFail($request->idkaryawan);

        // Validasi password harus sama
        if ($request->new_password !== $request->confirm_password) {
            return response()->json([
                'success' => false, // Perbaikan: gunakan 'success' agar AJAX bisa membaca
                'message' => 'Password tidak cocok...'
            ], 400); // 400 Bad Request
        }

        // Cek apakah user dengan id_employee sudah ada
        $user = User::where('id_employee', $request->idkaryawan)->first();

        if ($user) {
            // Jika user sudah ada, update password
            $user->update([
                'password' => Hash::make($request->confirm_password)
            ]);

            return response()->json([
                'success' => true, // Perbaikan: gunakan 'success' agar AJAX bisa membaca
                'message' => "Password untuk karyawan $employee->name telah diperbarui!"
            ]);
        } else {
            // Jika user belum ada, buat user baru
            User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'role' => 'krw',
                'status' => true,
                'password' => Hash::make($request->confirm_password),
                'id_employee' => $request->idkaryawan,
            ]);

            return response()->json([
                'success' => true, // Perbaikan: gunakan 'success' agar AJAX bisa membaca
                'message' => "Authentikasi untuk karyawan $employee->name berhasil dibuat!"
            ]);
        }
    }

    public function formatDataEmployee() {
        return Excel::download(new FormatEmplyeeExport, 'formatdatakaryawan.xlsx');
    }

    public function importexcel(Request $request) {
        // Validasi file
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048' // Maksimal 2MB
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }
    
        try {
            // Ambil file
            $file = $request->file('file');
    
            // Buat nama file unik
            $nama_file = time().'_'.$file->getClientOriginalName();
        
            // Pindahkan file ke folder public/upload
            $file->move(public_path('upload'), $nama_file);
    
            // Import data dari file
            Excel::import(new EmployeeImports, public_path('upload/' . $nama_file));
    
            return response()->json(['success' => true, 'message' => 'Data berhasil diupdate!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengimport data.'], 500);
        }
    }
}
