<?php


namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\UpdateImageProductRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PurchasingRepository;

use App\Repositories\StoreRepository;
use App\Services\FileService;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProductServiceImpl implements ProductService
{

    public function __construct(public Logger $logging, public ProductRepository $productRepository, public FileService $fileService, public PurchasingRepository $purchasingRepository, public StoreRepository $storeRepository, public CategoryRepository $categoryRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(Request $request)
    {
        $request->validated();

        DB::beginTransaction();
        try {

            if ($this->productRepository->findByBarcode($request['barcode'])) {
                throw new ApiException('Barcode sudah digunakan. Silakan gunakan barcode yang berbeda.');
            }

            if ($request['selling_price'] < $request['purchase_price']) {
                throw new ApiException('Harga jual tidak boleh lebih rendah dari harga beli. Pastikan harga jual lebih tinggi atau sama dengan harga beli.');
            }

            $filename = $request->hasFile('image')
                ? $this->fileService->uploadProductImage($request, time() . '.' . $request->image->extension())
                : "product-default.png";


            $insertProduct = $this->productRepository->create([
                "name" => $request['name'],
                "barcode" => $request['barcode'],
                "stock" => intval($request['stock']),
                "selling_price" => intval($request['selling_price']),
                "purchase_price" => intval($request['purchase_price']),
                "image" => $filename,
            ]);

            if (!$insertProduct) {
                throw new ApiException('Gagal menyimpan produk');
            }

            $purchasing = $this->purchasingRepository->create([
                'no_purchasing' => generateNoTransaction(),
                'product_id' => $insertProduct->id,
                'quantity' => $insertProduct->stock,
                'description' => $request['description'] ?? "",
                'total_payment' => $insertProduct->purchase_price * $insertProduct->stock
            ]);

            if (!$purchasing) {
                throw new ApiException('Gagal menyimpan data pembelian');
            }

            if ($request['category_id'] != null) {
                $this->categoryRepository->findById($request['category_id']) ? $insertProduct->category()->attach($request['category_id']) :   throw new ApiException('category tidak ditemukan');
            }

            $storeId = intval($request['store_id']);
            $store = $this->storeRepository->findById($storeId);

            if (!$store) {
                throw new ApiException('store tidak ditemukan');
            }
            $insertProduct->stores()->attach($storeId);

            DB::commit();
            $this->logging->info('Successful create product ' . $insertProduct->name . ' by ' . $request->user()['email']);

            return $insertProduct;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            throw new ApiException($e->getMessage());
        }
    }


    /**
     * @inheritDoc
     */
    public function deleteProductById($id)
    {
        if (!$this->findProductById($id)) throw new ApiException('product tidak ditemukan');;
        return $this->productRepository->deleteById($id);
    }



    /**
     * @inheritDoc
     */
    public function deleteProductByUuid($uuid)
    {
        if (!$this->productRepository->findByUuid($uuid)->exists()) throw new ApiException('product tidak ditemukan');
        return $this->productRepository->deleteByUuid($uuid);
    }

    /**
     * @inheritDoc
     */
    public function findProductById($id)
    {
        return $this->productRepository->findById($id);
    }


    /**
     * @inheritDoc
     */
    public function findProductByUuid($uuid)
    {
        return $this->productRepository->findByUuid($uuid)->exists();
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        try {
            //code...
            return $this->productRepository->getAll();
        } catch (\Throwable $th) {
            throw new ApiException('terjadi kesalahan' . $th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getProductById($id)
    {
        $this->findProductById($id) ? ['status' => true, 'data' => $this->productRepository->findById($id)] :  throw new ApiException("product tidak ditemukan");;
    }



    /**
     * @inheritDoc
     */
    public function getProductByUuid($uuid)
    {
        if (!$this->findProductByUuid($uuid)) {

            throw new ApiException("product tidak ditemukan");
        }
        $product = $this->productRepository->getByUuid($uuid);
        $product->link = url()->previous() . "/api/products/" . $product->uuid;
        // foreach ($products as $product) {
        //     $product->link = url()->previous() . "/api/products/" . $product->uuid;
        // }
        return $product;
    }

    /**
     * @inheritDoc
     */
    public function updateProductById($id, $data)
    {

        return $this->productRepository->updateById($id, $data);
    }


    /**
     * @inheritDoc
     */
    public function updateProductByUuid($uuid, ProductUpdateRequest $request)
    {

        DB::beginTransaction();
        try {
            $payload = $request->validated();

            $findProduct = $this->productRepository->findByUuid($uuid)->exists();

            if (!$findProduct) {
                throw new ApiException("produk tidak ditemukan", 404);
            }
            $currentProduct = $this->productRepository->getByUuid($uuid);

            unset($payload["_method"]);

            $newStock = 0;

            switch ($payload['add_or_reduce_stock']) {
                case "add":
                    $newStock = $currentProduct->stock + $payload['quantity_stok'];
                    break;
                case "reduce":
                    $newStock = $currentProduct->stock - $payload['quantity_stok'];
                    break;
                default:
                    throw new ApiException("gagal update produk add_or_reduce_stok barus berisi add atau reduce", 404);
            }

            $this->productRepository->updateByUuid($uuid, [
                "name" => $payload['name'],
                "barcode" => $payload['barcode'],
                "stock" => $newStock,
                "selling_price" => $payload['selling_price'],
                "purchase_price" => $payload['purchase_price'],
            ]);

            DB::commit();

            return $this->getProductByUuid($uuid);
        } catch (\Throwable $th) {

            DB::rollBack();
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getProductByBarcode($uuid)
    {
        return $this->productRepository->getByBarcode($uuid);
    }
    /**
     * @inheritDoc
     */
    public function findProductByBarcode($name)
    {
        return $this->productRepository->findByBarcode($name);
    }
    /**
     * @inheritDoc
     */
    public function updateProductImageByUuid($uuid, UpdateImageProductRequest $request)
    {
        try {
            $payload = $request->validated();
            if (!$request->hasFile('image')) {
                throw new ApiException('image tidak ditemukan', 400);
            }
            $currentProduct = $this->productRepository->getByUuid($uuid);
            $currentProduct['image'] == 'product-default.png' ?: $this->fileService->deleteProductImage($payload['image']);
            $filename =  time() . '.' . $request->image->extension();
            $this->fileService->uploadProductImage($request, $filename);
            $this->productRepository->updateByUuid($uuid, [
                'image' => $filename
            ]);
            return $this->productRepository->getByUuid($uuid);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }
    /**
     * @inheritDoc
     */
    public function getProductByCategory($category)
    {
        try {
            $link = $category;
            $category = str_replace('-', ' ', $category);
            $product = $this->productRepository->getByCategory($category)->first();
            $product->link = url()->previous() . "/api/categories/{$link}/products";
            return $product;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }
    /**
     * @inheritDoc
     */
    public function addCategoriesToProduct(CategoryUpdateRequest $request, string $productUuid)
    {
        $requestValid = $request->validated();

        DB::beginTransaction();
        try {
            if (!$this->productRepository->findByUuid($productUuid)->exists()) throw new ApiException('product tidak dittemukan', 404);

            $this->productRepository->addCategoriesToProduct($productUuid, $requestValid['category_id']);
            DB::commit();
            return $this->productRepository->getByUuid($productUuid);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new ApiException($th->getMessage());
        }
    }
    /**
     * @inheritDoc
     */
    public function deleteCategoriesInProduct(CategoryUpdateRequest $request, string $productUuid)
    {
        $validated =  $request->validated();

        DB::beginTransaction();
        try {
            if (!$this->productRepository->findByUuid($productUuid)->exists()) throw new ApiException('product tidak ditemukan');
            foreach ($validated['category_id'] as $valid) {
                if (!$this->categoryRepository->findById($valid)) throw new ApiException('product tidak ditemukan');
            }
            DB::commit();
            return $this->productRepository->deleteCategoriesInProduct($productUuid, $validated['category_id']);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new ApiException($th->getMessage());
        }
    }
}
