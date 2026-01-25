<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Program;
use App\Models\DataAnak;
use App\Models\Employee;
use App\Mail\CustomEmail;
use App\Models\ReqApproval;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ApprovalFirst;
use App\Imports\DataAnakImport;
use App\Models\TermAndCondition;
use App\Exports\AnakFormatExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Imports\DataSaldoAnakImport;
use App\Notifications\FirstApproval;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SaldoAnakFormatExport;
use App\Models\LogHistorySemesterCredit;
use App\Models\ReqApprovalDetail;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\NotifAddCreditScore;
use App\Notifications\NotifReqApprovalCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PengajuanController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $query = DataAnak::with(['karyawan', 'program', 'karyawan.company', 'reqpproval'])
            ->whereHas('karyawan', function ($q) {
                $q->where('isactive', true);
            })
            ->whereHas('approval', function ($q) {
                $q->where('status', 1);
            })
            ->orderBy('nama', 'ASC');

            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan nomor otomatis
                ->addColumn('running_balance', function ($row) {
                    return $row->latestTransaction->final_balance ?? 0;
                })
                ->make(true); // Kembalikan JSON
        }

        return view('transaksi.pengajuan.index');
    }

    public function add($idEmployee) {

        $realId = (Auth::user()->role !== 'krw') 
                            ? Crypt::decryptString($idEmployee) 
                            : $idEmployee;

        $employee = Employee::where('id', $realId)->first(); // Ambil satu record
        $anakData = DataAnak::where('id_karyawan', $realId)->get();
        $programs = Program::all();
    
        return view('transaksi.pengajuan.create', compact('employee', 'programs', 'anakData'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction(); // Mulai transaksi database

            $fcKtpName = null;
            if ($request->hasFile('fc_ktp')) {
                $fcKtp = $request->file('fc_ktp');
                $fcKtpName = 'ktp_' . uniqid() . '.' . $fcKtp->getClientOriginalExtension();
                $fcKtp->move(public_path('upload'), $fcKtpName);
            }

            // Ambil employee_id dari anak pertama
            $employeeId = $request->employee_id ?? null;

            foreach (array_values($request->anak) as $index => $anak) {
                if (!isset($anak['nama']) || empty($anak['nama'])) {
                    continue;
                }

                $cekDuplikat = DataAnak::where('id_karyawan', $employeeId)
                ->where('nama', $anak['nama'])
                ->first();

                if ($cekDuplikat) {
                    throw new \Exception("Anak dengan nama {$anak['nama']} sudah anda daftarkan.");
                }

                 // Convert tgl_lahir ke format MySQL (Y-m-d)
                $tglLahir = null;
                if (!empty($anak['tgl_lahir'])) {
                    $tglLahir = \Carbon\Carbon::createFromFormat('m/d/Y', $anak['tgl_lahir'])->format('Y-m-d');
                }

                $suratSekolahName = null;
                $fcRaportName = null;
                $fcRekSekolahName = null;

                if ($request->hasFile("anak.$index.surat_sekolah")) {
                    $file = $request->file("anak.$index.surat_sekolah");
                    $suratSekolahName = 'surat_sekolah_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('upload'), $suratSekolahName);
                }

                if ($request->hasFile("anak.$index.fc_raport")) {
                    $file = $request->file("anak.$index.fc_raport");
                    $fcRaportName = 'fc_raport_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('upload'), $fcRaportName);
                }

                if ($request->hasFile("anak.$index.fc_rek_sekolah")) {
                    $file = $request->file("anak.$index.fc_rek_sekolah");
                    $fcRekSekolahName = 'fc_rek_sekolah_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('upload'), $fcRekSekolahName);
                }

                // Simpan data anak dengan employee_id dari anak pertama
                $anakData = DataAnak::create([
                    'nama'           => $anak['nama'],
                    'id_karyawan'    => $employeeId,  // Gunakan ID dari anak pertama
                    'id_program'     => $anak['id_program'],
                    'nama_sekolah'   => $anak['nama_sekolah'],
                    'tempat_lahir'   => $anak['tempat_lahir'],
                    'tgl_lahir'      => $tglLahir,
                    'fc_ktp'         => $fcKtpName, 
                    'surat_sekolah'  => $suratSekolahName,
                    'fc_raport'      => $fcRaportName,
                    'fc_rek_sekolah' => $fcRekSekolahName,
                ]);

                Log::info('Data anak berhasil disimpan', ['data' => $anakData]);

                // Ambil saldo sekolah
                $saldoSekolah = Program::where('id', $anak['id_program'])->first();
                if (!$saldoSekolah || !isset($saldoSekolah->total)) {
                    throw new \Exception("Saldo sekolah tidak ditemukan atau tidak memiliki nilai total");
                }

                // Simpan transaksi
                $req = ApprovalFirst::create([
                        'id_anak' => $anakData->id,
                        'status' => 0,
                    ]);

                $employee = Employee::find($employeeId);
                $idEmployee = (Auth::user()->role === 'krw') 
                                ? Crypt::encryptString($employee->id) 
                                : $employee->id;

                if (Auth::user()->role === 'krw') {
                    $toEmail = $employee->user->email;

                    $emailData = [
                        'title' => "Konfirmasi Pengajuan Peserta Tabungan Pendidikan",
                        'body' => "Halo $employee->name, terima kasih atas kepercayaan Anda, Anda baru saja mengajukan pendaftaran peserta tabungan pendidikan untuk anak anda yang bernama $anakData->nama, yang bersekolah di $anakData->nama_sekolah. Silahkan menunggu konfirmasi selanjutnya, atau bila ingin info lebih lanjut, anda bisa menghubungi Divisi Pendidikan Yayasan Persada Hati.",
                        'subject' => "Konfirmasi Pengajuan Peserta Tabungan Pendidikan",
                        'alert' => true
                    ];

                    Mail::to($toEmail)->queue(new CustomEmail($emailData));
                }


                $admins = User::where('role', 'adm')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new FirstApproval($req));
                }
            }

            DB::commit(); // Simpan transaksi

            // return redirect()->back()->with('success', 'Data anak berhasil disimpan!');
            return redirect()->route('employee.show', $idEmployee)->with('success', 'Data anak berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack(); // Kembalikan transaksi jika ada error
            Log::error('Gagal menyimpan data anak', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menyimpan data anak. '.$e->getMessage());
        }
    }

    public function reqapproval(string $id) {
        $term = TermAndCondition::first();
        $anakData = DataAnak::find($id);
    
        if (!$anakData) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }
    
        return response()->json([
            'anakData' => $anakData,
            'termContent' => $term ? $term->text : ''
        ]);
    }
    
    public function postreqapprovael(Request $request)
    {
        
        $request->validate([
            'id_anak' => 'required|exists:data_anaks,id',
            'nominal' => 'required|numeric|min:1',
            'tujuan_pencairan' => 'required|string',
            'norek' => 'required|string',
            'bankname' => 'required|string',
            'accountbankname' => 'required|string',
            'isreimburst' => 'nullable|boolean',
            'rincian' => 'required|array|min:1',
            'rincian.*' => 'required|string',
            'nominal_rincian' => 'required|array|min:1',
            'nominal_rincian.*' => 'required|numeric|min:1',
            'filepencairan' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xlsx,xls',
            'filefcraport' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ]);

        // Validasi jumlah array harus sama
        if (count($request->rincian) !== count($request->nominal_rincian)) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah rincian dan nominal rincian tidak sama!'
            ], 422);
        }

        // Validasi total rincian harus sama dengan nominal utama
        $totalDetail = array_sum($request->nominal_rincian);
        if ($totalDetail != $request->nominal) {
            return response()->json([
                'success' => false,
                'message' => 'Total seluruh rincian harus sama dengan nominal pengajuan!'
            ], 422);
        }

        $anakData = DataAnak::find($request->id_anak);
        $final_balance = $anakData->latestTransaction->final_balance ?? 0;

        if ($request->nominal > $final_balance) {
            return response()->json([
                'success' => false,
                'message' => "Nominal pengajuan tidak boleh melebihi sisa tabungan!"
            ], 422);
        }

        $filepencairanName = "";
        $filefcraportName = "";

        if ($request->hasFile('filepencairan')) {
            $filepencairan = $request->file('filepencairan');
            $filepencairanName = 'File_Pencairan_' . uniqid() . '.' . $filepencairan->getClientOriginalExtension();
            $filepencairan->move(public_path('upload'), $filepencairanName);
        }

        if ($request->hasFile('filefcraport')) {
            $filefcraport = $request->file('filefcraport');
            $filefcraportName = 'fc_raport_' . uniqid() . '.' . $filefcraport->getClientOriginalExtension();
            $filefcraport->move(public_path('upload'), $filefcraportName);
        }

        $req = ReqApproval::create([
            'id_anak' => $anakData->id,
            'reason' => $request->tujuan_pencairan,
            'nominal' => $request->nominal,
            'status' => 0,
            'file' => $filepencairanName,
            'norek' => $request->norek,
            'bankname' => $request->bankname,
            'accountbankname' => $request->accountbankname,
            'isreimburst' => $request->isreimburst
        ]);
        
        $rincianList = $request->rincian;
        $nominalList = $request->nominal_rincian;

        foreach ($rincianList as $i => $deskripsi) {
            ReqApprovalDetail::create([
                'id_req_approval' => $req->id,
                'rincian' => $deskripsi,
                'nominal' => $nominalList[$i],
            ]);
        }
        
        if ($filefcraportName !== "") {
            $anakData->update([
                'fc_raport' => $filefcraportName
            ]);
        }

        
        $admins = User::where('role', 'adm')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NotifReqApprovalCreated($req));
        }
        
        if (Auth::user()->role === 'krw') {
            $emailData = [
                'title' => 'Permohonan Pencairan Saldo Tabungan',
                'body' => "Kamu telah mengajukan pencairan saldo tabungan untuk {$anakData->nama} sebesar Rp. " . number_format($request->nominal, 2),
                'subject' => 'Permohonan Pencairan Saldo Tabungan',
                'alert' => true
            ];

            $toEmail = $anakData->karyawan->user->email;

            // Mail::to($toEmail)->queue(new CustomEmail($emailData));
        }
        
        return response()->json([
            'success' => true,
            'message' => "Form lengkap sudah diterima. Mohon menunggu persetujuan."
        ]);
    }

    
    public function get($id) {
        $anakData = DataAnak::with(['transaction', 'reqpproval', 'program', 'latestTransaction'])->find($id);
        
        if (!$anakData) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'anakData' => $anakData,
            'user_role' => $user->role ?? null
        ]);
    }
    
    public function updatebalance(Request $request, string $id){
        $query = DataAnak::find($id);
        $now = Carbon::now();
        $tahun = $now->year;
        $bulan = $now->month;
        $semester = ($bulan >= 1 && $bulan <= 6) ? "Genap" : "Ganjil";
        $keteranganSemester = "Penambahan Saldo Semester $semester $tahun";

        $user = $query->karyawan->user;
    
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }
    
        $nominalprogram = $query->program->total;
    
        if ($request->nominaltotal > $nominalprogram) {
            return response()->json([
                'success' => false,
                'message' => 'Tambahan nominal tidak boleh melebihi nominal yang sudah ditetapkan!'
            ], 400);
        }
    
        Transaction::createTransaction($query->id, $request->nominaltotal, 0, $keteranganSemester);

        if ($user) {
            $user->notify(new NotifAddCreditScore($query));
        }

        $toEmail = $user->email;
        $nominal = number_format($request->nominaltotal, 2);
        $totalscore = $query->latestTransaction->final_balance;
        $totalscorefix = number_format($totalscore, 2);

        $emailData = [
            'title' => 'Penambahan Saldo Tabungan',
            'body' => "Penambahan saldo tabungan sebesar Rp. $nominal dalam rangka $keteranganSemester. Kini tabungan $query->nama bertambah sebesar Rp. $totalscorefix.",
            'subject' => 'Penambahan Saldo Tabungan',
            'alert' => false
        ];

        // if ($toEmail) {
        //     Mail::to($toEmail)->queue(new CustomEmail($emailData));
        // }
    
        return response()->json([
            'success' => true,
            'message' => 'Saldo berhasil ditambahkan!'
        ], 200);
    }
    
    
    public function updatechild(Request $request) {
        $id = $request->idanak;
        $query = DataAnak::find($id);
    
        if (!$query) {
            return response()->json(['success' => false, 'message' => 'Data anak tidak ditemukan!'], 404);
        }
    
        // Update data anak
        $query->nama = $request->namaanak;
        $query->nama_sekolah = $request->namasekolah;
        $query->id_program = $request->id_program;
        $query->tempat_lahir = $request->tempatlahir;
        $query->tgl_lahir = date('Y-m-d', strtotime($request->tgllahir));
    
        // Simpan file yang diupload
        if ($request->hasFile('surat_sekolah')) {
            $file = $request->file('surat_sekolah');
            $filename = 'surat_sekolah_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filename);
            $query->surat_sekolah = $filename;
        }
    
        if ($request->hasFile('fc_ktp')) {
            $file = $request->file('fc_ktp');
            $filename = 'fc_ktp_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filename);
            $query->fc_ktp = $filename;
        }
    
        if ($request->hasFile('fc_raport')) {
            $file = $request->file('fc_raport');
            $filename = 'fc_raport_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filename);
            $query->fc_raport = $filename;
        }
    
        if ($request->hasFile('fc_rek_skolah')) {
            $file = $request->file('fc_rek_skolah');
            $filename = 'fc_rek_skolah_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filename);
            $query->fc_rek_sekolah = $filename;
        }
    
        $query->save(); // Simpan ke database
    
        return response()->json(['success' => true, 'message' => 'Data anak berhasil diperbarui!']);
    }

    public function exportAnakFormat()
    {
        return Excel::download(new AnakFormatExport, 'formatdataank.xlsx');
    }
    
    public function exportSaldoAnakFormat()
    {
        return Excel::download(new SaldoAnakFormatExport, 'formatdatasaldoank.xlsx');
    }

    public function importAnak(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new DataAnakImport, $request->file('file'));

        return back()->with('success', 'Data anak berhasil diimpor!');
    }
   
    public function importSaldoAnak(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new DataSaldoAnakImport, $request->file('file'));

        return back()->with('success', 'Data saldo anak berhasil diimpor!');
    }

    public function generate(){
        $user = "";
        $now = Carbon::now();
        $tahun = $now->year;
        $bulan = $now->month;
        $totalamount = 0;

        $semester = ($bulan >= 1 && $bulan <= 6) ? "Genap" : "Ganjil";
        $keteranganSemester = "Penambahan Saldo Semester $semester $tahun";

        $dataAnaks = DataAnak::whereHas('karyawan', function ($q) {
            $q->where('isactive', true);
        })
        ->whereHas('approval', function ($q) {
            $q->where('status', 1);
        })
        ->with('program')
        ->get();

        foreach ($dataAnaks as $anak) {
            Transaction::createTransaction($anak->id, $anak->program->total ?? 0, 0, $keteranganSemester);
            $user = $anak->karyawan->user;
            $user->notify(new NotifAddCreditScore($anak));

            $nominal = number_format($anak->program->total, 2);
            $totalscorefix = number_format($anak->latestTransaction->final_balance, 2);

            $emailData = [
                'title' => 'Penambahan Saldo Tabungan',
                'body' => "Penambahan saldo tabungan sebesar Rp. $nominal dalam rangka Penambahan Saldo Semester $semester. Kini tabungan $anak->nama bertambah sebesar Rp. $totalscorefix.",
                'subject' => 'Penambahan Saldo Tabungan',
                'alert' => false
            ];

            $totalamount +=$anak->program->total;

            // $toEmail = $user->email;

            // Mail::to($toEmail)->queue(new CustomEmail($emailData));
        }

        LogHistorySemesterCredit::create([
            'description' => $keteranganSemester,
            'totalamount' => $totalamount,
            'id_user' => Auth::user()->id
        ]);

        return back()->with('success', 'Transaksi per semester berhasil dibuat.');
    }
    
}
