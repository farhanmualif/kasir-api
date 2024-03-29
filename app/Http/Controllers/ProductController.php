<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Models\Purchasing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datas = Product::all();
        $datas = $datas->map(function ($product) {
            $product->link = url()->current() . "/$product->uuid";
            return $product;
        });

        return responseJson("produk ditemukan", ProductCollection::collection($datas));
    }

    public function store(ProductStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $file_name = "";
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $file_name = time() . '.' . $request->image->extension();
                $request->image->storeAs('images', $file_name);
                $validated['image'] = $file_name;
            }

            $insert_product = Product::create($validated);
            Purchasing::create([
                'no_purchasing' => generateNoTransaction(),
                'product_id' => $insert_product->id,
                'quantity' => $insert_product->stock,
                'description' => $insert_product->description,
                'total_payment' => $insert_product->purchase_price * $insert_product->stock
            ]);

            // Simpan relasi dengan Category
            if ($validated['category_id'] != null) {
                $insert_product->category()->attach($validated['category_id']);
            }

            DB::commit();
            return responseJson("berhasil tambah produk", new ProductCollection($insert_product));
        } catch (\Throwable $th) {
            DB::rollBack();
            return responseJson("gagal menambahkan produk, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        try {
            $product = Product::where("uuid", $uuid)->first();
            $product->link = \url()->current();
            if (!$product) {
                return responseJson("produk tidak ditemukan", null, false, 404);
            }
            return responseJson("produk ditemukan", new ProductCollection($product));
        } catch (\Throwable $th) {
            return responseJson("get data failed {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()} {$th->getPrevious()}", null, false, 500);
        }
    }


    public function update(ProductStoreRequest $request, string $id)
    {
        DB::beginTransaction();
        try {

            $payload = $request->validated();
            $product = Product::where("uuid", $id)->firstOrFail();
            if (!$product) {
                return responseJson("produk tidak ditemukan", null, false, 400);
            }
            $file_name = "";
            if ($request->hasFile("image")) {
                $file_name = time() . '.' . $request->image->extension();
                $request->image->storeAs('images', $file_name);

                Storage::delete("images", $product->image);
            }
            $payload["image"] = $file_name;
            unset($payload["_method"]);
            Product::where("uuid", $id)->update([
                "name" => $payload['name'],
                "barcode" => $payload['barcode'],
                "stock" => $payload['stock'],
                "selling_price" => $payload['selling_price'],
                "purchase_price" => $payload['purchase_price'],
            ]);
            $no_purchasing = generateNoTransaction();
            Purchasing::create([
                'no_purchasing' => $no_purchasing,
                'product_id' => $product->id,
                'quantity' => $payload['stock'],
                'description' => $payload['description'],
                
            ]);
            return responseJson("produk berhasil di update", new ProductCollection(Product::where("uuid", $id)->firstOrFail()));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::commit();
            return responseJson("gagal update produk {$th->getMessage()} {$th->getFile()} {$th->getLine()}", null, false, 500);
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
            return responseJson("berhadil menghapus data produk", null, true, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::commit();
            return responseJson("gagal menghapus data produk {$th->getMessage()}", null, false, 500);
        }
    }
}
