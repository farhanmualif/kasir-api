<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\UpdateImageProductRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;



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
        $products = $this->productServices->getAll()->get();
        foreach ($products as $data) {
            $data['link'] = \url()->current() . '/' . $data->uuid;
        }

        return responseJson("produk ditemukan", ProductCollection::collection($products));
    }

    public function store(ProductStoreRequest $request)
    {

        $response = $this->productServices->create($request);
        return responseJson("berhasil tambah produk", new ProductCollection($response));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->productServices->getProductByUuid($id);
        return responseJson("produk ditemukan", new ProductCollection($product));
    }


    public function update(ProductUpdateRequest $request, string $id)
    {
        $response =  $this->productServices->updateProductByUuid($id, $request);
        return responseJson("produk berhasil diubah", new ProductCollection($response));
    }

    public function updateImage(UpdateImageProductRequest $request, string $id)
    {
        $response =  $this->productServices->updateProductImageByUuid($id, $request);
        return responseJson("produk berhasil diubah", new ProductCollection($response));
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
