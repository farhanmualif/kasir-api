<?php

namespace App\Services;

use Illuminate\Http\Request;

interface FileService
{
    public function uploadProductImage(Request $request, string $filename);
    public function getProductImage(string $filename);
    public function deleteProductImage(string $filename);
    public function uploadStruckTransaction(Request $request, string $filename);
    public function deleteStruckTransaction(string $filename);
}
