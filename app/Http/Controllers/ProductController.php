<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \response()->json([
            "status" => true,
            "message" => "data found",
            "data" => ProductCollection::collection(Product::all()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {

        try {
            $file_name = "";

            if ($request->hasFile('image')) {
                $file_name = time() . '.' . $request->image->extension();
                $request->image->storeAs('images', $file_name);
            }

            $validated = $request->validated();
            $validated["image"] = $file_name;
            $insert_product = Product::create($validated);

            return \response()->json([
                "status" => \true,
                "message" => "insert data successfully",
                "data" => new ProductCollection($insert_product)
            ])->setStatusCode(200);
        } catch (\Throwable $th) {

            return \response()->json([
                "status" => \false,
                "message" => "insert data successfully",
                "data" => $th->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductStoreRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $payload = $request->all();
            $product = Product::where("uuid", $id)->firstOrFail();
            if (!$product) {
                return \response()->json([
                    "status" => \false,
                    "message" => "data not found"
                ]);
            }
            $file_name = "";
            if ($request->hasFile("image")) {
                $file_name = time() . '.' . $request->image->extension();
                $request->image->storeAs('images', $file_name);

                Storage::delete("images", $product->image);
            }
            $payload["image"] = $file_name;
            unset($payload["_method"]);
            Product::where("uuid", $id)->update($payload);
            return \response()->json([
                "status" => \true,
                "message" => "update data successfully",
                "data" => Product::where("uuid", $id)->firstOrFail()
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::commit();
            return response()->json(
                [
                    "status" => \false,
                    "message" => $th->getMessage(),
                ]
            )->setStatusCode(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
