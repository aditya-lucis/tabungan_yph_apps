<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailConfiguration extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "email_configurations";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['driver', 'host', 'port', 'username', 'password', 'encryption'];
}
