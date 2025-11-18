<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReqApproval extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "req_approvals";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['id_anak', 'status', 'approve_by_id', 'notes', 'nominal', 'reason', 'file', 'norek', 'isreimburst', 'bankname', 'accountbankname', 'nominalapprove'];

    public function anak() : BelongsTo {
        return $this->belongsTo(DataAnak::class, 'id_anak', 'id');
    }
    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'approve_by_id', 'id');
    }

    public function reqpprovaldetail (): HasMany{
        return $this->hasMany(ReqApprovalDetail::class, 'id_req_approval', 'id');
    }
    
    public function reqpprovallog (): HasMany{
        return $this->hasMany(LogApprovalHistory::class, 'id_req_approval', 'id');
    }
    
    public function latestreqpprovallog (): HasOne{
        return $this->hasOne(LogApprovalHistory::class, 'id_req_approval', 'id')->latestOfMany();
    }
}
