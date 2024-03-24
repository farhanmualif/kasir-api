<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable  = ["total_payment", "no_transaction", "cash", "change"];

    protected $table = "transactions";
}
