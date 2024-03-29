<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchasing extends Model
{
    use HasFactory;
    protected $fillable = ['no_purchasing', 'product_id', 'quantity', 'description', 'total_payment'];
    protected $table = "purchasing";
}
