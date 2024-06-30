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
            "id" => $this->id,
            "uuid" => $this->uuid,
            "name" => $this->name,
            "category" => $this->category,
            "barcode" => $this->barcode,
            "stock" => $this->stock,
            "selling_price" => $this->selling_price,
            "purchase_price" => $this->purchase_price,
            "image" => $this->image,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
