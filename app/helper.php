<?php


use Illuminate\Http\JsonResponse;

if (!function_exists("generateNoTransaction")) {
    function generateNoTransaction()
    {
        return date("YmdHis") . rand(5, 6);
    }
}


if (!function_exists("responseJson")) {
    function responseJson(string $message, $data = null, bool $status = true, int $status_code = 200): JsonResponse
    {
        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $data
        ])->setStatusCode($status_code);
    }
}



/**
 * {
    "message": "data found",
    "status": true,
    "data": {

        "0": {
            "uuid": "84c5db8d-e8da-11ee-a4bb-0a0027000004",
            "name": "shampo clear",
            "barcode": "989814",
            "stock": 20,
            "selling_price": "1000.00",
            "purchase_price": "500.00",
            "image": "product-default.png",
            "created_at": "2024-03-23T05:59:32.000000Z",
            "updated_at": "2024-03-23T05:59:32.000000Z"
        },
        "1": {
            "uuid": "84c670b2-e8da-11ee-a4bb-0a0027000004",
            "name": "sabun lifeboy",
            "barcode": null,
            "stock": 20,
            "selling_price": "1500.00",
            "purchase_price": "1000.00",
            "image": "product-default.png",
            "created_at": "2024-03-23T05:59:32.000000Z",
            "updated_at": "2024-03-23T05:59:32.000000Z"
        },
        "2": {
            "uuid": "84c6d223-e8da-11ee-a4bb-0a0027000004",
            "name": "aqua galon",
            "barcode": "989810",
            "stock": 10,
            "selling_price": "1000.00",
            "purchase_price": "1500.00",
            "image": "product-default.png",
            "created_at": "2024-03-23T05:59:32.000000Z",
            "updated_at": "2024-03-23T05:59:32.000000Z"
        },

 **/
