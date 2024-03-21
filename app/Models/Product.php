<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        "uuid", "name", "barcode", "stock", "selling_price", "purchase_price", "image", "created_at", "updated_at"
    ];
}
