<?php

namespace App\Services;

use App\Exceptions\ApiException;
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
        return Storage::disk('s3')->delete("product/images/$filename");
    }

    /**
     * @inheritDoc
     */
    public function deleteStruckTransaction(string $filename)
    {
        return Storage::disk('s3')->delete("invoices/$filename");
    }

    /**
     * @inheritDoc
     */
    public function uploadProductImage(UploadedFile $file, string $filename)
    {
        try {
            return Storage::disk('s3')->putFileAs(
                'product/images', // direktori di disk public
                $file,            // file yang diupload
                $filename         // nama file tujuan
            );
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
            return Storage::disk('s3')->putFileAs(
                'invoices', // direktori di disk public
                $request->file('invoice'),            // file yang diupload
                $filename         // nama file tujuan
            );
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }
    /**
     * @inheritDoc
     */
    public function getProductImage(string $uuid)
    {
        $product = Product::where("uuid", $uuid)->first();

        if (!$product) {
            throw new ApiException("Produk tidak ditemukan");
        }

        $path = "product/{$product->image}";

        if (!Storage::disk('s3')->exists($path)) {
            throw new ApiException("Gambar tidak ditemukan di S3");
        }

        return [
            'stream' => Storage::disk('s3')->readStream($path),
            'mime_type' => Storage::disk('s3')->mimeType($path),
            'file_name' => $product->image
        ];
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
