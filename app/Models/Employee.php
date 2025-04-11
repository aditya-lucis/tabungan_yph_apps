<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "employees";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['name', 'email', 'phone', 'company_id', 'isactive'];
    
    public function company() : BelongsTo {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function anak() : HasMany {
        return $this->hasMany(DataAnak::class, 'id_karyawan', 'id');
    }

    public function user() : HasOne {
        return $this->hasOne(User::class, 'id_employee', 'id');
    }

}
