<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function detailTransaction(): BelongsToMany
    {
        return $this->belongsToMany(DetailTransaction::class, 'id_transaction', 'id');
    }

    /**
     * Get the invoice associated with the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invoice()
    {
        return $this->hasOne(Invoices::class);
    }
}
