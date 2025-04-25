<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\DiscountCreateRequest;
use App\Http\Requests\DiscountUpdateRequest;
use App\Repositories\DiscountRepository;
use App\Models\Discount;
use App\Models\Discounts;
use Symfony\Component\HttpFoundation\Response;

class DiscountServiceImpl implements DiscountService
{
    public function __construct(public DiscountRepository $discountRepository) {}

    /**
     * @inheritDoc
     */
    public function create(DiscountCreateRequest $request): Discounts
    {
        $data = $request->validated();

        // Validasi tambahan untuk value berdasarkan type
        $this->validateDiscountValue($data['type'], $data['value']);

        // Add store_id from the authenticated user
        $data['store_id'] = auth()->user()->stores->first()->id;

        return $this->discountRepository->create($data);
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?Discounts
    {
        try {
            return $this->discountRepository->getById($id);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        try {
            $discounts = $this->discountRepository->getAll();
            return $discounts;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): array
    {
        try {
            $discount = $this->discountRepository->getById($id);
            return $this->formatDiscount($discount);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * @inheritDoc
     */
    public function getByName(string $name): array
    {
        try {
            $discounts = $this->discountRepository->getByName($name);
            return $discounts;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * @inheritDoc
     */
    public function updateById(int $id, DiscountUpdateRequest $request): Discounts
    {
        try {
            $data = $request->validated();

            // Validasi tambahan jika type/value diupdate
            if (isset($data['type']) || isset($data['value'])) {
                $current = $this->findById($id);
                $type = $data['type'] ?? $current->type;
                $value = $data['value'] ?? $current->value;
                $this->validateDiscountValue($type, $value);
            }

            return $this->discountRepository->updateById($id, $data);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): bool
    {
        try {
            return $this->discountRepository->deleteById($id);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * Validasi value berdasarkan type diskon
     */
    private function validateDiscountValue(string $type, float $value): void
    {
        if ($type === Discounts::TYPE_PERCENTAGE && ($value < 0 || $value > 100)) {
            throw new \InvalidArgumentException('Persentase diskon harus antara 0-100%');
        }

        if ($type === Discounts::TYPE_FIXED && $value < 0) {
            throw new \InvalidArgumentException('Potongan harga tidak boleh negatif');
        }
    }

    /**
     * Format data diskon untuk response
     */
    private function formatDiscount(Discounts $discount): array
    {
        return [
            'id' => $discount->id,
            'store_id' => $discount->store_id,
            'title' => $discount->title,
            'type' => $discount->type,
            'type_label' => $discount->type === Discounts::TYPE_PERCENTAGE ? 'Percentage' : 'Fixed Amount',
            'value' => $discount->value,
            'formatted_value' => $discount->formatted_value,
            'description' => $discount->description,
            'created_at' => $discount->created_at,
            'updated_at' => $discount->updated_at,
        ];
    }
}
