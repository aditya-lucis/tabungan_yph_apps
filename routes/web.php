<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\TermAndConditionController;
use App\Http\Controllers\ValidatedController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'store']);

Route::middleware(['auth'])->group(function (){

    Route::get('/', [HomeController::class, 'index'])->name('homeindex');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/tabungan/inbox', [InboxController::class, 'index'])->name('tabungan.inbox');
    Route::get('/tabungan/inbox/export', [InboxController::class, 'export'])->name('approval.export');
    Route::get('/tabungan/inbox/{id}', [InboxController::class, 'edit'])->name('tabungan.inbox.edit');
    Route::put('/tabungan/inbox/update/{id}', [InboxController::class, 'update'])->name('tabungan.inbox.update');

    Route::resource('/company', CompanyController::class);
    Route::resource('/program', ProgramController::class);
    Route::resource('/employee', EmployeeController::class);
    Route::resource('/users', UserController::class);
    Route::get('/get-format-employee', [EmployeeController::class, 'formatDataEmployee'])->name('formatDataEmployee');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::put('/users/{id}/update-profile', [UserController::class, 'updateprofile'])->name('users.update-profile');
    Route::put('/users/{id}/update-password', [UserController::class, 'updatepassword'])->name('users.update-password');
    Route::get('/export-user-employee', [UserController::class, 'exportUserEmployee']);
    Route::post('/import-user-employee', [UserController::class, 'importUserEmployee'])->name('importUserEmployee');
    Route::post('/employee/{id}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employee.toggleStatus');
    Route::get('/employee/{id}/get', [EmployeeController::class, 'getidkaryawn']);
    Route::post('/createauth/employee', [EmployeeController::class, 'createauth'])->name('authemployee');
    Route::post('/import-excel', [EmployeeController::class, 'importexcel'])->name('import.excel');
    Route::get('/pengajuan/{id}', [PengajuanController::class, 'add']);
    Route::get('/get/pengajuan/{id}', [PengajuanController::class, 'get']);
    Route::put('/get/pengajuan/{id}', [PengajuanController::class, 'updatebalance']);
    Route::get('/reqapproval/{id}', [PengajuanController::class, 'reqapproval']);
    Route::post('/postreqapprovael', [PengajuanController::class, 'postreqapprovael'])->name('postreqapprovael');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::post('/generate-transactions', [PengajuanController::class, 'generate'])->name('transactions.generate');
    Route::post('/pengajuan/update', [PengajuanController::class, 'updatechild'])->name('pengajuan.update');
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/get-anak-format', [PengajuanController::class, 'exportAnakFormat'])->name('exportAnakFormat');
    Route::get('/get-saldo-format', [PengajuanController::class, 'exportSaldoAnakFormat'])->name('exportSaldoAnakFormat');
    Route::post('/post-anak-format', [PengajuanController::class, 'importAnak'])->name('importAnak');
    Route::post('/post-saldo-anak-format', [PengajuanController::class, 'importSaldoAnak'])->name('importSaldoAnak');
    Route::get('/pengajuan/termandcondition/{id}', [TermAndConditionController::class, 'request'])->name('sk-route');
    Route::get('/termandcondition', [TermAndConditionController::class, 'add'])->name('sk-add');
    Route::put('/pengajuan/termandcondition', [TermAndConditionController::class, 'store'])->name('termandcondition.store');
    Route::resource('/validate', ValidatedController::class);

    Route::post('/notifications/read/{id}', function ($id) {
        $user = Auth::user();
        
        Log::info('Current user: ', ['user' => $user]);
    
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    
        // dump method existence
        if (!method_exists($user, 'notifications')) {
            return response()->json(['error' => 'notifications() method not available on user'], 500);
        }
    
        $notif = $user->notifications()->findOrFail($id);
        $notif->markAsRead();
    
        return response()->json(['success' => true]);
    });
    
});