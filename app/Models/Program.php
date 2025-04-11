<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "programs";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['level', 'total'];

    public function anak() : HasMany {
        return $this->hasMany(DataAnak::class, 'id_program', 'id');
    }
}
