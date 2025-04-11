<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DataAnak extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "data_anaks";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama',
        'id_karyawan',
        'id_program',
        'nama_sekolah',
        'tempat_lahir',
        'tgl_lahir',
        'fc_ktp',
        'surat_sekolah',
        'fc_raport',
        'fc_rek_sekolah'
    ];

    public function karyawan() : BelongsTo {
        return $this->belongsTo(Employee::class, 'id_karyawan', 'id');
    }
    public function program() : BelongsTo {
        return $this->belongsTo(Program::class, 'id_program', 'id');
    }

    public function transaction () : HasMany{
        return $this->hasMany(Transaction::class, 'id_anak', 'id');
    }

    public function latestTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'id_anak', 'id')->latestOfMany();
    }

    public function approval (): HasMany{
        return $this->hasMany(ApprovalFirst::class, 'id_anak', 'id');
    }
   
    public function reqpproval (): HasMany{
        return $this->hasMany(ReqApproval::class, 'id_anak', 'id');
    }

}
