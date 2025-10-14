<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogHistorySemesterCredit extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "log_history_semester_credits";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['description', 'totalamount', 'id_user'];

    public function user() : HasMany {
        return $this->hasMany(User::class, 'id_user', 'id');
    }
}
