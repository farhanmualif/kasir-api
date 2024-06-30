<?php

namespace App\Services;

use Illuminate\Http\Request;

interface ProductService
{
    public function create(Request $request);
    public function getAll();
    public function getProductById($id);
    public function getProductByUuid($uuid);
    public function getProductByBarcode($uuid);
    public function findProductById($id);
    public function findProductByBarcode($name);
    public function findProductByUuid($uuid);
    public function updateProductById($id, $data);
    public function updateProductByUuid($uuid, $data);
    public function deleteProductById($id);
    public function deleteProductByUuid($uuid);
}
