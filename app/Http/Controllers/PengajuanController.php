<?php

namespace App\Http\Controllers;

use App\Exports\AnakFormatExport;
use App\Exports\SaldoAnakFormatExport;
use App\Imports\DataAnakImport;
use App\Imports\DataSaldoAnakImport;
use App\Mail\CustomEmail;
use App\Models\ApprovalFirst;
use App\Models\DataAnak;
use App\Models\Employee;
use App\Models\Program;
use App\Models\ReqApproval;
use App\Models\TermAndCondition;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\FirstApproval;
use App\Notifications\NotifAddCreditScore;
use App\Notifications\NotifReqApprovalCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class PengajuanController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $query = DataAnak::with(['karyawan', 'program', 'karyawan.company', 'reqpproval'])
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
        $employee = Employee::where('id', $idEmployee)->first(); // Ambil satu record
        $anakData = DataAnak::where('id_karyawan', $idEmployee)->get();
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
                    'tgl_lahir'      => $anak['tgl_lahir'],
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

                $toEmail = $employee->user->email;

                $emailData = [
                    'title' => "Konfirmasi Pengajuan Peserta Tabungan Pendidikan",
                    'body' => "Halo $employee->name, terima kasih atas kepercayaan Anda, Anda baru saja mengajukan pendaftaran peserta tabungan pendidikan untuk anak anda yang bernama $anakData->nama, yang bersekolah di $anakData->nama_sekolah. Silahkan menunggu konfirmasi selanjutnya, atau bila ingin info lebih lanjut, anda bisa menghubungi Divisi Pendidikan Yayasan Persada Hati.",
                    'subject' => "Konfirmasi Pengajuan Peserta Tabungan Pendidikan",
                    'alert' => true
                ];

                Mail::to($toEmail)->queue(new CustomEmail($emailData));

                $admins = User::where('role', 'adm')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new FirstApproval($req));
                }
            }

            DB::commit(); // Simpan transaksi

            // return redirect()->back()->with('success', 'Data anak berhasil disimpan!');
            return redirect()->route('employee.show', $employeeId)->with('success', 'Data anak berhasil disimpan!');

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
    
    public function postreqapprovael(Request $request) {
        $idanak = $request->id_anak;
        $anakData = DataAnak::find($idanak);
        $filepencairanName = "";

        $final_balance = $anakData->latestTransaction->final_balance;

        if ($request->nominal > $final_balance) {
            return response()->json(['success' => false, 'message' => "Nominal pengajuan tidak boleh melebihi sisa tabungan!"], 404);
        }

        if ($request->hasFile('filepencairan')) {
            $filepencairan = $request->file('filepencairan');
            $filepencairanName = 'File_Pencairan_' . uniqid() . '.' . $filepencairan->getClientOriginalExtension();
            $filepencairan->move(public_path('upload'), $filepencairanName);
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

        // Kirim notifikasi ke semua admin
        $admins = User::where('role', 'adm')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NotifReqApprovalCreated($req));
        }

        $nominalreq = number_format($request->nominal, 2);
        
        $emailData = [
            'title' => 'Permohonan Pencairan Saldo Tabungan',
            'body' => "Kamu telah mengajukan pencairan saldo tabungan untuk $anakData->nama sebesar Rp. $nominalreq. Silahkan menunggu kabar balasan dari Yayasan Pesada Hati Divisi Pendidikan.",
            'subject' => 'Permohonan Pencairan Saldo Tabungan',
            'alert' => true
        ];

        $toEmail = $anakData->karyawan->user->email;

        if ($toEmail) {
            Mail::to($toEmail)->queue(new CustomEmail($emailData));
        }
    
        if ($anakData) {
            return response()->json(['success' => true, 'message' => "Form yang lengkap akan memudahkan pencairan dana. Jadi lengkapi form anda untuk kemudahan pencairan."]);
        } else {
            return response()->json(['success' => false, 'message' => "Data anak tidak ditemukan!"], 404);
        }
    }

    public function get($id) {
        $anakData = DataAnak::with(['transaction', 'reqpproval', 'program'])->find($id);
        
        if (!$anakData) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json($anakData);
    }
    
    public function updatebalance(Request $request, string $id){
        $query = DataAnak::find($id);

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
    
        Transaction::createTransaction($query->id, $request->nominaltotal, 0, $request->notesdescript);

        if ($user) {
            $user->notify(new NotifAddCreditScore($query));
        }

        $toEmail = $user->email;
        $nominal = number_format($request->nominaltotal, 2);
        $totalscore = $query->latestTransaction->final_balance;
        $totalscorefix = number_format($totalscore, 2);

        $emailData = [
            'title' => 'Penambahan Saldo Tabungan',
            'body' => "Penambahan saldo tabungan sebesar Rp. $nominal dalam rangka $request->notesdescript. Kini tabungan $query->nama bertambah sebesar Rp. $totalscorefix.",
            'subject' => 'Penambahan Saldo Tabungan',
            'alert' => false
        ];

        if ($toEmail) {
            Mail::to($toEmail)->queue(new CustomEmail($emailData));
        }
    
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
        $dataAnaks = DataAnak::whereHas('karyawan', function ($q) {
            $q->where('isactive', true);
        })->with('program')->get();

        foreach ($dataAnaks as $anak) {
            Transaction::createTransaction($anak->id, $anak->program->total ?? 0, 0, 'Penambahan Saldo Semester Genap');
            $user = $anak->karyawan->user;
            $user->notify(new NotifAddCreditScore($anak));

            $nominal = number_format($anak->program->total, 2);
            $totalscorefix = number_format($anak->latestTransaction->final_balance, 2);

            $emailData = [
                'title' => 'Penambahan Saldo Tabungan',
                'body' => "Penambahan saldo tabungan sebesar Rp. $nominal dalam rangka Penambahan Saldo Semester Genap. Kini tabungan $anak->nama bertambah sebesar Rp. $totalscorefix.",
                'subject' => 'Penambahan Saldo Tabungan',
                'alert' => false
            ];

            $toEmail = $user->email;

            Mail::to($toEmail)->queue(new CustomEmail($emailData));
        }

        return back()->with('success', 'Transaksi per semester berhasil dibuat.');
    }
    
}
