<?php

namespace App\Services;

use App\Exceptions\ApiException;
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
        return $this->storage->delete("invoices/" . $filename);
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
}
