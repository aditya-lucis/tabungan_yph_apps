<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Company::orderBy('name', 'ASC'); // Query Builder

            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan nomor otomatis
                ->editColumn('aliase', function ($item) {
                    return $item->aliase ? '(' . $item->aliase . ')' : ''; // Tambahkan tanda kurung jika aliase ada
                })
                ->addColumn('action', function ($item) {
                    return '
                    <div class="d-flex gap-2 btn-icon-list">
                        <a id="edit" class="btn btn-success btn-icon btn-sm" data-id="' . $item->id . '">
                            <i class="typcn typcn-pencil"></i>
                        </a>
                        <a id="show" class="btn btn-warning btn-icon btn-sm" href="' . route('company.show', $item->id) . '">
                            <i class="typcn typcn-eye"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true); // Kembalikan JSON
        }

        $company = Company::all();

        return view('master.company.index', ['companies' => $company]);
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
            'aliase' => 'required'
        ]);
        Company::create([
            'name' => $request->name,
            'aliase' => $request->aliase
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diupdate!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $company = Company::findOrFail($id);

        $employee = Employee::where('company_id', $id)->get();

        if ($request->ajax()) {
            $employee = Employee::where('company_id', $id)
            ->orderBy('name', 'ASC')
            ->get();

            return DataTables::of($employee)
            ->addIndexColumn() // Menambahkan nomor otomatis
            ->addColumn('action', function ($item) {
                return '
                <div class="d-flex gap-2 btn-icon-list">
                    <a id="edit" class="btn btn-warning btn-icon btn-sm" href="' . route('employee.show', $item->id) . '">
                        <i class="typcn typcn-eye"></i>
                    </a>
                </div>';
            })
            ->rawColumns(['action'])
            ->make(true); // Kembalikan JSON
        }

        return view('master.company.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Company::findOrFail($id);
        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $company = Company::findOrFail($id);
        $company->update([
            'name' => $request->name,
            'aliase' => $request->aliase
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
