<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileServiceImpl implements FileService
{

    public function __construct(public Storage $storage)
    {
    }

    /**
     * @inheritDoc
     */
    public function deleteProductImage(string $filename)
    {
        return $this->storage->delete("product/images" . $filename);
    }

    /**
     * @inheritDoc
     */
    public function deleteStruckTransaction(string $filename)
    {
        return $this->storage->delete("invoices/$filename");
    }

    /**
     * @inheritDoc
     */
    public function uploadProductImage(Request $request, string $filename)
    {
        try {
            return $request->image->storeAs('public/images', $filename);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function uploadStruckTransaction(Request $request, string $filename)
    {
        try {
            return $request->image->storeAs('public/images', $filename);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }
    /**
     * @inheritDoc
     */
    public function getProductImage(string $uuid)
    {

        try {
            $productImage = Product::where("uuid", $uuid)->first();
            if ($productImage == null) {
                throw new ApiException("Produk tidak ditemukan");
            }
            $path = storage_path("app/public/images/{$productImage->image}");

            if (!file_exists($path)) {
                abort(404);
            }

            return $path;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }
}
