<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::all();
        return responseJson("data found", ProductCollection::collection($data));
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
            return responseJson("insert data successfully", new ProductCollection($insert_product));
        } catch (\Throwable $th) {
            return responseJson("insert data failure, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        try {
            $product = Product::where("uuid", $uuid)->first();

            return responseJson("get data successfully", new ProductCollection($product));
        } catch (\Throwable $th) {
            return responseJson("get data failed {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()} {$th->getPrevious()}", null, false, 500);
        }
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
                return responseJson("data not found", null, false, 400);
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
            return responseJson("update data successfully", new ProductCollection(Product::where("uuid", $id)->firstOrFail()));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::commit();
            return responseJson("update data failed {$th->getMessage()}", null, false, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            Product::where("uuid", $id)->delete();
            DB::commit();
            return responseJson("delete data successfully", null, true, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::commit();
            return responseJson("any problem {$th->getMessage()}", null, false, 500);
        }
    }
}
