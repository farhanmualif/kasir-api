<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discounts extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'title',
        'uuid',
        'uuid',
        'type',
        'value',
        'store_id'
    ];



    protected $casts = [
        'value' => 'float',
    ];

    /**
     * Relationship with Store
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
