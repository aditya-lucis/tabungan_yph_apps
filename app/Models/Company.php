<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "companies";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['name', 'aliase'];

    public function employee() : HasMany {
        return $this->hasMany(Employee::class, 'company_id', 'id');
    }
}
