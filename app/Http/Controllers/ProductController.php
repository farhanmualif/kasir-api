<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Services\FileService;
use App\Services\ProductService;
use App\Services\StoreService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{

    public function __construct(public ProductService $productServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $products = $this->productServices->getAll();
        foreach ($products as $data) {
            $data['link'] = \url()->current() . '/' . $data->uuid;
        }

        return responseJson("produk ditemukan", ProductCollection::collection($products));
    }

    public function store(ProductStoreRequest $request)
    {
        try {
            $request->validated();
            $response = $this->productServices->create($request);

            if (!$response['status']) {
                return responseJson("gagal menambahkan produk, {$response['data']}", null, false, 500);
            }
            return responseJson("berhasil tambah produk", new ProductCollection($response['data']));
        } catch (\Throwable $th) {
            return responseJson("gagal menambahkan produk, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = $this->productServices->getProductByUuid($id);

            if (!$product['status']) return responseJson($product['data'], null, false, 404);

            return responseJson("produk ditemukan", new ProductCollection($product));
        } catch (\Throwable $th) {
            return responseJson("get data failed {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()} {$th->getPrevious()}", null, false, 500);
        }
    }


    public function update(ProductUpdateRequest $request, string $id)
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

            $current_stock = $product->stock;
            if ($payload['add_or_reduce_stock'] == "add") {

                $new_stock = $current_stock + $payload['quantity_stok'];
                Product::where("uuid", $id)->update([
                    "name" => $payload['name'],
                    "barcode" => $payload['barcode'],
                    "stock" => $new_stock,
                    "selling_price" => $payload['selling_price'],
                    "purchase_price" => $payload['purchase_price'],
                ]);
                DB::commit();

                return responseJson("produk berhasil di update", new ProductCollection(Product::where("uuid", $id)->firstOrFail()));
            } else if ($payload['add_or_reduce_stock'] == "reduce") {
                $new_stock = $current_stock - $payload['quantity_stok'];
                Product::where("uuid", $id)->update([
                    "name" => $payload['name'],
                    "barcode" => $payload['barcode'],
                    "stock" => $new_stock,
                    "selling_price" => $payload['selling_price'],
                    "purchase_price" => $payload['purchase_price'],
                ]);
                DB::commit();
            } else {
                return responseJson("gagal update produk add_or_reduce_stok barus berisi add atau reduce", null, false, 500);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
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
