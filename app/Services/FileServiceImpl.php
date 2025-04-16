<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Invoices;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileServiceImpl implements FileService
{

    public function __construct(public Storage $storage) {}

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
    public function uploadProductImage(UploadedFile $request, string $filename)
    {
        try {
            return $request->storeAs('public/images', $filename);
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

    public  function getTrancsactionIvoice(string $noTransaction)
    {
        try {
            $transaction = Transaction::where('no_transaction', $noTransaction)->first();


            if ($transaction == null) {
                throw new ApiException('transaction tidak ditemukan', 404);
            }



            if (!$transaction->exists()) {
                throw new ApiException('transaction tidak ditemukan', 404);
            }

            $invoice = $transaction->invoice()->first();
            if ($invoice == null) {
                throw new ApiException('invoice tidak ditemukan', 404);
            }


            if ($invoice == null) {
                throw new ApiException("transaction tidak ditemukan", 404);
            }

            $path = storage_path("app/public/invoices/{$invoice->filename}");

            if (!file_exists($path)) {
                throw new ApiException("file invoice tidak ditemukan", 404);
            }

            return $path;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }
}
