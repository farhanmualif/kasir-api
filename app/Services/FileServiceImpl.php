<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Invoices;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileServiceImpl implements FileService
{

    public function __construct(public Storage $storage) {}

    /**
     * @inheritDoc
     */
    public function deleteProductImage(string $filename)
    {
        try {
            $path = "public/images/" . $filename;
            if (!Storage::exists($path)) {
                Log::warning("File not found for deletion: " . $path);
                return true; // Consider it deleted if it doesn't exist
            }
            return Storage::delete($path);
        } catch (\Throwable $th) {
            Log::error("Error deleting file: " . $th->getMessage());
            throw new ApiException("Gagal menghapus file: " . $th->getMessage());
        }
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
            if (!$request->hasFile('image')) {
                throw new ApiException('File image tidak ditemukan');
            }

            $file = $request->file('image');
            if (!$file->isValid()) {
                throw new ApiException('File upload tidak valid');
            }

            $path = $file->storeAs('public/images', $filename);
            if (!$path) {
                throw new ApiException('Gagal menyimpan file');
            }

            return $path;
        } catch (\Throwable $th) {
            Log::error("Error uploading file: " . $th->getMessage());
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
