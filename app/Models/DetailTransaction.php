<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaction extends Model
{

    protected $fillable  = ["id_transaction", "id_product", "total_price", "quantity"];
    use HasFactory;
}
