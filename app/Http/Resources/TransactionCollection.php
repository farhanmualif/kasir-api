<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            "no_transaction" => $this->no_transaction,
            "total_payment" => $this->total_payment,
            "cash" => $this->cash,
            "change" => $this->change,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
