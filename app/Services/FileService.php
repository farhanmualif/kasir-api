<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

interface FileService
{
    public function uploadProductImage(UploadedFile $request, string $filename);
    public function getProductImage(string $filename);
    public function getTrancsactionIvoice(string $noTransaction);
    public function deleteProductImage(string $filename);
    public function uploadStruckTransaction(Request $request, string $filename);
    public function deleteStruckTransaction(string $filename);
}
