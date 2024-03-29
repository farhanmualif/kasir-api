<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable  = ["total_payment", "no_transaction", "cash", "change", "income", "profit"];

    protected $table = "transactions";


    /**
     * Get all of the comments for the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detailTransaction()
    {
        return $this->belongsToMany(DetailTransaction::class, 'id_transaction', 'id');
    }
}
