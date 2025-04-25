<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountCreateRequest;
use App\Http\Requests\DiscountUpdateRequest;
use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class DiscountController extends Controller
{
    public function __construct(public DiscountService $discountService) {}

    /**
     * Display a listing of the discounts.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $discounts = $this->discountService->getAll();
            return \responseJson("Discounts retrieved successfully", $discounts);
        } catch (\Throwable $th) {
            Log::error('Error fetching discounts: ' . $th->getMessage());
            return \responseJson("Failed to fetch discounts", null, false, HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created discount in storage.
     *
     * @param DiscountCreateRequest $request
     * @return JsonResponse
     */
    public function store(DiscountCreateRequest $request): JsonResponse
    {
        try {
            $discount = $this->discountService->create($request);
            return \responseJson("Discount created successfully", $discount, true, HttpFoundationResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error('Error creating discount: ' . $th->getMessage());
            return \responseJson("Failed to create discount", null, false, HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified discount.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $discount = $this->discountService->getById((int) $id);

            if (!$discount) {
                return \responseJson("Discount not found", null, false, HttpFoundationResponse::HTTP_NOT_FOUND);
            }

            return \responseJson("Discount retrieved successfully", $discount);
        } catch (\Throwable $th) {
            Log::error('Error fetching discount: ' . $th->getMessage());
            return \responseJson("Failed to fetch discount", null, false, HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified discount in storage.
     *
     * @param DiscountUpdateRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(DiscountUpdateRequest $request, string $id): JsonResponse
    {
        try {
            // Check if discount exists
            if (!$this->discountService->findById((int) $id)) {
                return \responseJson("Discount not found", null, false, HttpFoundationResponse::HTTP_NOT_FOUND);
            }

            $updated = $this->discountService->updateById((int) $id, $request);

            if ($updated) {
                $discount = $this->discountService->getById((int) $id);
                return \responseJson("Discount updated successfully", $discount);
            }

            return \responseJson("Failed to update discount", null, false, HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            Log::error('Error updating discount: ' . $th->getMessage());
            return \responseJson("Failed to update discount", null, false, HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified discount from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Check if discount exists
            if (!$this->discountService->findById((int) $id)) {
                return \responseJson("Discount not found", null, false, HttpFoundationResponse::HTTP_NOT_FOUND);
            }

            $deleted = $this->discountService->deleteById((int) $id);

            if ($deleted) {
                return \responseJson("Discount deleted successfully", null);
            }

            return \responseJson("Failed to delete discount", null, false, HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            Log::error('Error deleting discount: ' . $th->getMessage());
            return \responseJson("Failed to delete discount", null, false, HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search for discounts by name.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|min:1'
            ]);

            $discounts = $this->discountService->getByName($request->name);

            return \responseJson("Discounts found", $discounts);
        } catch (\Throwable $th) {
            Log::error('Error searching discounts: ' . $th->getMessage());
            return \responseJson("Failed to search discounts", null, false, HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
