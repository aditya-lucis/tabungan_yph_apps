<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Program::orderBy('level', 'ASC'); // Query Builder

            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan nomor otomatis
                ->addColumn('action', function ($item) {
                    return '
                    <div class="d-flex gap-2 btn-icon-list">
                        <a id="edit" class="btn btn-success btn-icon btn-sm" data-id="' . $item->id . '">
                            <i class="typcn typcn-pencil"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true); // Kembalikan JSON
        }

        $program = Program::all();

        return view('master.program.index', ['programs' => $program]);
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
            'level' => 'required',
            'total' => 'required|numeric',
        ]);
        Program::create([
            'level' => $request->level,
            'total' => $request->total
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diupdate!']);
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
    public function edit($id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json($program);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        $program->update([
            'level' => $request->level,
            'total' => (int) $request->total // Pastikan total adalah angka
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diupdate!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
