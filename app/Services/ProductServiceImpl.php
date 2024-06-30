<?php


namespace App\Services;

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

    public function __construct(public ProductRepository $productRepository, public FileService $fileService, public PurchasingRepository $purchasingRepository, public StoreRepository $storeRepository, public CategoryRepository $categoryRepository, public Logger $logging)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {

            if ($this->productRepository->findByBarcode($request['barcode'])) {
                return ['status' => false, 'data' => 'barcode sudah digunakan'];
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
                throw new Exception('Gagal menyimpan produk');
            }

            $purchasing = $this->purchasingRepository->create([
                'no_purchasing' => generateNoTransaction(),
                'product_id' => $insertProduct->id,
                'quantity' => $insertProduct->stock,
                'description' => $request['description'] ?? "",
                'total_payment' => $insertProduct->purchase_price * $insertProduct->stock
            ]);

            if (!$purchasing) {
                throw new Exception('Gagal menyimpan data pembelian');
            }

            if ($request['category_id'] != null) {
                $this->categoryRepository->findById($request['category_id']) ? $insertProduct->category()->attach($request['category_id']) :  ['status' => false, 'data' => 'category tidak ditemukan'];
            }

            $storeId = intval($request['store_id']);
            $store = $this->storeRepository->findById($storeId);
            if (!$store) {
                return ['status' => false, 'data' => 'store tidak ditemukan'];
            }
            $insertProduct->stores()->attach($storeId);

            DB::commit();
            $this->logging->info('Successful create product ' . $insertProduct->name . ' by ' . $request->user()['email']);
            return ['status' => true, 'data' => $insertProduct];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }


    /**
     * @inheritDoc
     */
    public function deleteProductById($id)
    {
        if (!$this->findProductById($id)) return ['status' => false, 'data' => 'product tidak ditemukan'];
        return $this->productRepository->deleteById($id);
    }



    /**
     * @inheritDoc
     */
    public function deleteProductByUuid($uuid)
    {
        if (!$this->findProductByUuid($uuid)) ['status' => false, 'data' => 'product tidak ditemukan'];
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
        return $this->productRepository->findByUuid($uuid);
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->productRepository->getAll();
    }

    /**
     * @inheritDoc
     */
    public function getProductById($id)
    {
        if (!$this->findProductById($id)) ['status' => false, 'data' => 'product tidak ditemukan'];
        return $this->productRepository->findById($id);
    }



    /**
     * @inheritDoc
     */
    public function getProductByUuid($uuid)
    {
        if (!$this->findProductByUuid($uuid)) return ['status' => false, 'data' => 'data tidak ditemukan'];
        return $this->productRepository->getByUuid($uuid);
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
    public function updateProductByUuid($uuid, $data)
    {
        return $this->productRepository->updateByUuid($uuid, $data);
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
}
