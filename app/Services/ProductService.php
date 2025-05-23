<?php

namespace App\Services;

use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\UpdateImageProductRequest;
use App\Http\Requests\ProductStoreRequest;


interface ProductService
{
    public function create(ProductStoreRequest $request);
    public function addCategoriesToProduct(CategoryUpdateRequest $request, string $productUuid);
    public function addExistsProducts(ProductStoreRequest $request);
    public function deleteCategoriesInProduct(CategoryUpdateRequest $request, string $productUuid);
    public function getAll();
    public function getProductById($id);
    public function getProductByUuid($uuid);
    public function getProductByBarcode($uuid);
    public function getProductByCategory($uuid);
    public function findProductById($id);
    public function findProductByBarcode($name);
    public function findProductByUuid($uuid);
    public function updateProductById($id, ProductUpdateRequest $data);
    public function updateProductByUuid($uuid, ProductUpdateRequest $data);
    public function updateProductImageByUuid($uuid, UpdateImageProductRequest $data);
    public function deleteProductById($id);
    public function deleteProductByUuid($uuid);
}
