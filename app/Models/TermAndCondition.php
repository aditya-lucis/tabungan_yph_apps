<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermAndCondition extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "term_and_conditions";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['text'];
}
