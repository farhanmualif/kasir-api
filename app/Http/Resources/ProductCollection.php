<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "link" => $this->link,
            "id" => $this->id,
            "stores_id" => $this->stores->first()->id,
            "uuid" => $this->uuid,
            "name" => $this->name,
            "barcode" => $this->barcode,
            "stock" => $this->stock,
            "selling_price" => $this->selling_price,
            "purchase_price" => $this->purchase_price,
            "image" => $this->image,
            "category" => $this->category ?? null,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
