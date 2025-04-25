<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discounts extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'title', // atau 'name' jika tidak diubah
        'type',
        'value',
        'description',
    ];

    // Konstanta untuk tipe diskon
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';


    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // Accessor untuk memudahkan penggunaan
    public function getFormattedValueAttribute()
    {
        return $this->type === self::TYPE_PERCENTAGE
            ? "{$this->value}%"
            : 'Rp ' . number_format($this->value, 2);
    }

    // Method untuk menghitung diskon
    public function calculateDiscount($price)
    {
        return $this->type === self::TYPE_PERCENTAGE
            ? $price * ($this->value / 100)
            : min($this->value, $price); // Memastikan diskon tidak melebihi harga
    }
}
