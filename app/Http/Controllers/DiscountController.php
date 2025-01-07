<?php

namespace App\Http\Controllers;

use App\Models\Discounts;
use App\Services\DiscountService;
use App\Http\Requests\DiscountStoreRequest;
use App\Http\Requests\DiscountUpdateRequest;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function __construct(public DiscountService $discountService) {}

    /**
     * Display a listing of the discounts.
     */
    public function index()
    {
        return responseJson("Diskon ditemukan", $this->discountService->getAll());
    }

    /**
     * Store a newly created discount.
     */
    public function store(DiscountStoreRequest $request)
    {
        $discount = $this->discountService->create($request);
        return responseJson("Diskon berhasil ditambahkan", $discount);
    }

    /**
     * Display the specified discount.
     */
    public function show(string $id)
    {
        $discount = $this->discountService->getByUuId($id);
        return responseJson("Diskon ditemukan", $discount);
    }

    /**
     * Update the specified discount.
     */
    public function update(DiscountUpdateRequest $request, string $id)
    {
        $discount = $this->discountService->updateByUuId($id, $request);
        return responseJson("Diskon berhasil diperbarui", $discount);
    }

    /**
     * Remove the specified discount.
     */
    public function destroy(string $id)
    {
        $this->discountService->deleteByUuId($id);
        return responseJson("Diskon berhasil dihapus", null);
    }
}
