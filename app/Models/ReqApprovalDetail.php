<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReqApprovalDetail extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "req_approval_details";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['id_req_approval', 'rincian', 'nominal'];

    public function reqapproval() : BelongsTo {
        return $this->belongsTo(ReqApproval::class, 'id_req_approval', 'id');
    }

}
