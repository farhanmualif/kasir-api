<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Discount;
use App\Models\Discounts;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DiscountRepositoryImpl implements DiscountRepository
{
    public function __construct(public Discounts $discount) {}

    /**
     * Create new discount
     */
    public function create(array $data): Discounts
    {
        return $this->discount->create([
            'store_id' => $data['store_id'],
            'title' => $data['title'],
            'type' => $data['type'],
            'value' => $data['value'],
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Delete discount by ID
     */
    public function deleteById(int $id): bool
    {
        try {
            $discount = $this->discount->findOrFail($id);
            return $discount->delete();
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Get all discounts for current user's store
     */
    public function getAll(): array
    {
        try {
            $storeId = Auth::user()->stores->first()->id;

            return $this->discount
                ->where('store_id', $storeId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Get discount by ID
     */
    public function getById(int $id): ?Discounts
    {
        try {
            return $this->discount->find($id);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Update discount by ID
     */
    public function updateById(int $id, array $data): Discounts
    {
        try {
            $discount = $this->discount->findOrFail($id);
            $discount->update($data);
            return $discount->fresh();
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Check if discount exists
     */
    public function findById(int $id): bool
    {
        try {
            return $this->discount->where('id', $id)->exists();
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Get discounts by name/title
     */
    public function getByName(string $name): array
    {
        try {
            return $this->discount
                ->where('title', 'like', "%{$name}%")
                ->get()
                ->toArray();
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Get discounts by store ID
     */
    public function getByStoreId(int $storeId): array
    {
        try {
            return $this->discount
                ->where('store_id', $storeId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Get active discounts for store
     */
    public function getActiveDiscounts(int $storeId): array
    {
        try {
            return $this->discount
                ->where('store_id', $storeId)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode());
        }
    }
}
