<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "transactions";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['id_anak', 'previous_balance', 'credit', 'running_balance', 'debit', 'final_balance', 'notes', 'created_at','updated_at',];

    public static function createTransaction($idanak, $credit, $debit, $notes)
    {
        // Ambil transaksi terakhir berdasarkan id_anak
        $lastTransaction = self::where('id_anak', $idanak)->latest()->first();
        $previousBalance = $lastTransaction ? $lastTransaction->final_balance : 0;

        $runningBalance = $previousBalance + $credit;
        $finalBalance = $runningBalance - $debit;

        return self::create([
            'id_anak' => $idanak,
            'previous_balance' => $previousBalance,
            'credit' => $credit,
            'running_balance' => $runningBalance,
            'debit' => $debit,
            'final_balance' => $finalBalance,
            'notes' => $notes,
        ]);
    }

    public function anak() : BelongsTo {
        return $this->belongsTo(DataAnak::class, 'id_anak', 'id');
    }
}

