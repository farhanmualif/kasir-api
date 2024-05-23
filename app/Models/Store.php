<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id',
        'uuid',
        'address',
    ];

    /**
     * The product that belong to the Store
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function product()
    {
        return $this->belongsToMany(Product::class, 'product_user', 'store_id', 'product_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
