<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogApprovalHistory extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "log_approval_histories";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['id_req_approval', 'descript', 'status'];

    public function reqapproval() : BelongsTo {
        return $this->belongsTo(ReqApproval::class, 'id_req_approval', 'id');
    }
}
