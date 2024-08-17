<?php

namespace App\Http\Controllers;


use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\UpdateImageProductRequest;
use App\Http\Resources\ProductCollection;
use App\Services\FileService;
use App\Services\ProductService;

class ProductController extends Controller
{

    public function __construct(public ProductService $productServices, public FileService $fileService) {}

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

    public function showByBarcode(string $barcode)
    {
        $product = $this->productServices->getProductByBarcode($barcode);
        return responseJson("produk ditemukan", new ProductCollection($product));
    }

    public function showImage(string $uuid)
    {
        // get path image file from fileService
        $productImagePath = $this->fileService->getProductImage($uuid);


        // get MIME type from file
        $mimeType = mime_content_type($productImagePath);

        return response()->file($productImagePath, [
            "Content-Type" => $mimeType,
        ]);
    }

    public function showByCategory(string $categoryName)
    {
        return responseJson("produk ditemukan", ProductCollection::collection($this->productServices->getProductByCategory($categoryName)));
    }

    public function addCategoriesToProduct(CategoryUpdateRequest $request, string $productUuid)
    {
        return responseJson("produk ditemukan", new ProductCollection($this->productServices->addCategoriesToProduct($request, $productUuid)));
    }

    public function removeCategoriesFromProduct(CategoryUpdateRequest $request, string $productUuid)
    {
        return responseJson("produk ditemukan", new ProductCollection($this->productServices->deleteCategoriesInProduct($request, $productUuid)));
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
        $this->productServices->deleteProductByUuid($id);
        return responseJson("berhasil menghapus data");
    }

    public function purchaseProductsExist(ProductStoreRequest $request)
    {
        $updated = $this->productServices->addExistsProducts($request);
        return responseJson("berhasil menambah data produk", ProductCollection::collection($updated));
    }
}
