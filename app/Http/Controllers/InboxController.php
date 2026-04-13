<?php

namespace App\Http\Controllers;

use App\Exports\ReqApprovalExport;
use App\Mail\CustomEmail;
use App\Models\DataAnak;
use App\Models\LogApprovalHistory;
use App\Models\ReqApproval;
use App\Models\ReqApprovalDetail;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\NotifReqApprovalCreated;
use App\Notifications\NotifReqApprovalUpdated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class InboxController extends Controller
{
    
    public function index(Request $request) {
        if ($request->ajax()) {
            // Ambil tanggal awal & akhir dari request, jika tidak ada gunakan awal & akhir bulan ini
            $start_date = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
            $end_date = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

            $user = Auth::user();
    
            $query = ReqApproval::with(['anak.karyawan.company','anak', 'anak.karyawan', 'user'])
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
    
        return view('transaksi.approval.index', [ 'user_role' => Auth::user()->role, 'id_employee' => Auth::user()->id_employee, ]);
    }

    public function edit($id) {
        $query = ReqApproval::with(['anak.karyawan.company','anak', 'anak.karyawan', 'anak.program', 'anak.transaction', 'anak.latestTransaction', 'user', 'reqpprovaldetail','latestreqpprovallog'])->find($id);

        if (!$query) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json($query);
    }

    public function update(Request $request, $id)
    {
        $query = ReqApproval::find($id);
        $anakData = DataAnak::find($request->id_anak);
        $user = Auth::user();

        $finalBalance = $anakData->latestTransaction->final_balance;

        if (!$query) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        if ($request->status == 1) {

            if ($request->nominal_input > $finalBalance) {
                return response()->json(['success' => false, 'message' => 'Nominal tidak boleh melebihi sisa tabungan!'], 404);
            }else{
                Transaction::createTransaction($request->id_anak, 0, $request->nominal_input, $request->note_input);
            }
        }

        // Update data
        $query->update([
            'status' => $request->status,
            'notes' => $request->note_input,
            'nominalapprove' => $request->nominal_input,
            'approve_by_id' => $user->id,
        ]);

        if ($request->status == 1) {
            LogApprovalHistory::create([
                'id_req_approval' => $query->id,
                'descript' => 'Dokumen Telah Disetujui',
                'status' => $request->status
            ]);
        }elseif ($request->status == 2) {
            LogApprovalHistory::create([
                'id_req_approval' => $query->id,
                'descript' => 'Dokumen Ditolak',
                'status' => $request->status
            ]);
        }elseif ($request->status == 3) {
            LogApprovalHistory::create([
                'id_req_approval' => $query->id,
                'descript' => $request->loghistorysel,
                'status' => $request->status
            ]);
        }

        $pengaju = $anakData->karyawan->user;

        $query->refresh();

        if ($pengaju) {
            $pengaju->notify(new NotifReqApprovalUpdated($query));
        }

        $nominalreq = number_format($query->nominal, 2);
        $nominalapprove = number_format($request->nominal_input, 2);
        $fibalbalance = $anakData->latestTransaction->final_balance - $request->nominal_input;
        $fibalbalancefix = number_format($fibalbalance, 2);

        $title = $request->status == 1 
            ? "Persetujuan Pencairan Saldo Tabungan" 
            : "Penolakan Pencairan Saldo Tabungan";

        $body = $request->status == 1
            ? "Pencairan saldo tabungan atas nama $anakData->nama telah disetujui dengan nominal yang disetujui Rp. $nominalapprove. Sekarang sisa saldo tabungan $anakData->nama adalah Rp. $fibalbalancefix. \nPesan dari kami: $request->note_input."
            : "Mohon maaf, pengajuan pencairan saldo tabungan kamu atas nama $anakData->nama sebesar Rp. $nominalreq tidak bisa disetujui. Untuk info lebih lanjut anda bisa menghubungi bagian Divisi Pendidikan Yayasan Persada Hati.";


        // $emailData = [
        //     'title' => $title,
        //     'body' => $body,
        //     'subject' => $title,
        //     'alert' => false
        // ];

        // $toEmail = $pengaju->email;

        // if ($toEmail) {
        //     Mail::to($toEmail)->queue(new CustomEmail($emailData));
        // }

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui']);
    }

    public function export(Request $request)
    {
        $start = $request->input('start_date') ?? now()->startOfMonth()->format('Y-m-d');
        $end = $request->input('end_date') ?? now()->endOfMonth()->format('Y-m-d');

        return Excel::download(new ReqApprovalExport($start, $end), 'approval-export.xlsx');
    }

    public function loghistory($id)
    {
        $data = LogApprovalHistory::where('id_req_approval', $id)->orderBy('created_at', 'desc')->get();

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json($data);
    }

    public function updateRevised(Request $request, $id)
    {
        $request->validate([
            'id_anak'                   => 'required|exists:data_anaks,id',
            'norek'                     => 'required|string',
            'bankname'                  => 'required|string',
            'accountbankname'           => 'required|string',
            'notes'                     => 'required|string',
            'rincian_details'           => 'required|array|min:1',
            'rincian_details.*.rincian' => 'required|string',
            'rincian_details.*.nominal' => 'required|numeric|min:1',
            'new_file_pencairan'        => 'nullable|file|mimes:jpg,jpeg,png,pdf,xlsx,xls|max:5120',
            'new_file_raport'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $query = ReqApproval::findOrFail($id);
        $anakData = DataAnak::findOrFail($request->id_anak);

        $totalRincian = collect($request->rincian_details)->sum('nominal');
        if ($totalRincian <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total rincian tidak boleh nol!'
            ], 422);
        }

        $filePencairanName = $query->file;
        $fileFcRaportName  = $anakData->fc_raport;

        if ($request->hasFile('new_file_pencairan')) {
            // Hapus file lama kalau ada
            if ($query->file && File::exists(public_path('upload/' . $query->file))) {
                File::delete(public_path('upload/' . $query->file));
            }

            $file = $request->file('new_file_pencairan');
            $filePencairanName = 'File_Pencairan_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filePencairanName);
        }

        if ($request->hasFile('new_file_raport')) {
            if ($anakData->fc_raport && File::exists(public_path('upload/' . $anakData->fc_raport))) {
                File::delete(public_path('upload/' . $anakData->fc_raport));
            }

            $file = $request->file('new_file_raport');
            $fileFcRaportName = 'fc_raport_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $fileFcRaportName);
        }

        $query->update([
            'reason'            => $request->notes,
            'norek'             => $request->norek,
            'bankname'          => $request->bankname,
            'accountbankname'   => $request->accountbankname,
            'file'              => $filePencairanName,
            'status'            => 0,
        ]);

        if ($request->hasFile('new_file_raport')) {
            $anakData->update(['fc_raport' => $fileFcRaportName]);
        }

        ReqApprovalDetail::where('id_req_approval', $query->id)->delete();

        foreach ($request->rincian_details as $item) {
            ReqApprovalDetail::create([
                'id_req_approval' => $query->id,
                'rincian'         => $item['rincian'],
                'nominal'         => $item['nominal'],
            ]);
        }

        $admins = User::where('role', 'adm')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NotifReqApprovalCreated($query));   // reuse notification yang sama
        }

        if (Auth::user()->role === 'krw') {
            $emailData = [
                'title'   => 'Revisi Pengajuan Pencairan',
                'body'    => "Revisi pengajuan tabungan untuk {$anakData->nama} telah dikirim kembali. Mohon menunggu persetujuan admin.",
                'subject' => 'Revisi Pengajuan Tabungan',
                'alert'   => true
            ];

            $toEmail = $anakData->karyawan->user->email ?? null;
            // Mail::to($toEmail)->queue(new CustomEmail($emailData));
        }

        return response()->json([
            'success' => true,
            'message' => "Revisi berhasil disimpan. Pengajuan kembali ke status New."
        ]);

    }

}
