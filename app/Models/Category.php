<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{

    use HasFactory;

    protected $fillable  = ["name", "uuid", "store_id"];

    protected $table = 'categories';

    /**
     * The product that belong to the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function product()
    {
        return $this->belongsToMany(Product::class, "product_category");
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
