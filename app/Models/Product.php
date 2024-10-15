<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        "id", "uuid", "name", "barcode", "stock", "selling_price", "purchase_price", "image", "created_at", "updated_at",
    ];

    /**
     * The category that belong to the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function category()
    {
        return $this->belongsToMany(Category::class, "product_category", "product_id", "category_id");
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'product_store', "product_id", "store_id");
    }
}
