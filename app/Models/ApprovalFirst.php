<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalFirst extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "approval_firsts";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['id_anak', 'status', 'approve_by_id', 'notes'];

    public function anak() : BelongsTo {
        return $this->belongsTo(DataAnak::class, 'id_anak', 'id');
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'approve_by_id', 'id');
    }
}
