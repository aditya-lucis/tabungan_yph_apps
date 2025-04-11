<?php

namespace App\Http\Controllers;

use App\Models\ApprovalFirst;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\UpdateValidated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ValidatedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil tanggal awal & akhir dari request, jika tidak ada gunakan awal & akhir bulan ini
            $start_date = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
            $end_date = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

            $user = Auth::user();
    
            $query = ApprovalFirst::with(['anak.karyawan.company','anak', 'anak.karyawan', 'user'])
                ->whereBetween('created_at', [$start_date, $end_date]) // Filter berdasarkan tanggal
                ->orderBy('created_at', 'DESC');

             // 👇 Filter berdasarkan role dan id_employee
            if ($user->role === 'krw' && $user->id_employee) {
                $query->whereHas('anak.karyawan', function ($q) use ($user) {
                    $q->where('id', $user->id_employee);
                });
            }
    
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    return '
                        <div class="d-flex gap-2 btn-icon-list">
                            <a id="edit" class="btn btn-warning btn-icon btn-sm" 
                                data-id="' . $item->id . '">
                                <i class="typcn typcn-pencil"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->translatedFormat('d F Y'); 
                })
                ->make(true);
        }

        return view('transaksi.validated.index', [ 'user_role' => Auth::user()->role, 'id_employee' => Auth::user()->id_employee, ]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $query = ApprovalFirst::with(['anak.karyawan.company','anak', 'anak.karyawan', 'anak.program', 'user'])->find($id);

        if (!$query) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json($query);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $query = ApprovalFirst::find($id);

        $user = User::where('id_employee', $request->id_karyawan)->first();

        $id_anak = $request->id_anak;

        $program = Program::where('id', $request->id_program)->first();

        $saldokredit = $program->total;

        $validate = $request->validate([
            'status' => 'required',
            'notes' => 'required',
        ]);

        $query->update($validate);

        if ($user) {
            $user->notify(new UpdateValidated($query));
        }

        if ($request->status == 1) {
            Transaction::createTransaction($id_anak, $saldokredit, 0, $request->notes);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
