<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\UserEmployeeMigrate;
use App\Imports\UserEmployeeImports;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil parameter filter dari request (default "adm" jika tidak ada)
            $roleFilter = $request->get('role', 'adm');

            // Query user berdasarkan role yang dipilih
            $user = User::where('role', $roleFilter)->orderBy('name', 'ASC');

            return DataTables::of($user)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    return '
                        <div class="d-flex gap-2 btn-icon-list">
                            <a id="edit" class="btn btn-success btn-icon btn-sm" data-id="' . $item->id . '">
                                <i class="typcn typcn-pencil"></i>
                            </a>
                            <a id="show" class="btn btn-warning btn-icon btn-sm" data-id="' . $item->id . '">
                                <i class="typcn typcn-pencil"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master.users.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'required|confirmed',  // Pastikan password ada dan terkonfirmasi
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => true,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $User = User::findOrFail($id);

        return view('master.users.edit', compact('User'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'role' => 'required',
            'password' => $request->filled('password') ? 'min:6' : '', // Password opsional
        ]);        

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password, // Update password hanya jika diisi
        ]);

        return response()->json(['success' => true, 'message' => 'User berhasil diperbarui!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function toggleStatus(string $id)
    {
        $User = User::findOrFail($id);
        $User->status = !$User->status; // Toggle status
        $User->save();

        $employee = Employee::where('id', $User->id_employee)->first();
        if ($employee) {
            $employee->isactive = $User->status; // Toggle status
            $employee->save();
        }
    
        return response()->json([
            'message' => 'Status berhasil diperbarui!',
            'new_status' => $User->status
        ]);
    }

    public function updateprofile(Request $request, string $id){

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui!']);
    }
    
    public function updatepassword(Request $request, string $id){
        $user = User::findOrFail($id);

         // Validasi password harus sama
         if ($request->new_password !== $request->confirm_password) {
            return response()->json([
                'success' => false, // Perbaikan: gunakan 'success' agar AJAX bisa membaca
                'message' => 'Password tidak cocok...'
            ], 400); // 400 Bad Request
        }

        $user->update([
            'password' => Hash::make($request->confirm_password)
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui!']);
    }

    public function exportUserEmployee() {
        return Excel::download(new UserEmployeeMigrate, 'user_employee_migrate.xlsx');
    }

    public function importUserEmployee(Request $request) {
        // Validasi file
        $validator = Validator::make($request->all(), [
            'addFileMigrate' => 'required|mimes:csv,xls,xlsx|max:2048' // Maksimal 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        try {
            // Ambil file
            $file = $request->file('addFileMigrate');

            // Buat nama file unik
            $nama_file = time().'_'.$file->getClientOriginalName();

            // Pindahkan file ke folder public/upload
            $file->move(public_path('upload'), $nama_file);

            // Import data dari file
            Excel::import(new UserEmployeeImports, public_path('upload/' . $nama_file));

            return response()->json(['success' => true, 'message' => 'Data berhasil diupdate!']);

        } catch (\Exception $e) {
            \Log::error('Import Gagal: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
