<?php


namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\UpdateImageProductRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PurchasingRepository;

use App\Repositories\StoreRepository;
use App\Services\FileService;
use App\Services\ProductService;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProductServiceImpl implements ProductService
{

    public function __construct(public Logger $logging, public ProductRepository $productRepository, public FileService $fileService, public PurchasingRepository $purchasingRepository, public StoreRepository $storeRepository, public CategoryRepository $categoryRepository, public AuthManager $auth) {}

    /**
     * @inheritDoc
     */
    public function create(ProductStoreRequest $request)
    {
        Log::info('Request data:', $request->all());
        Log::info('Files:', $request->allFiles());

        DB::beginTransaction();
        try {
            $payload = $request->validated();
            $storeId = $this->auth->user()->stores()->first()->id;

            // Validasi barcode unik
            $findBarcodes = $this->productRepository->findByStoreId($storeId)->pluck('barcode')->toArray();
            if (in_array($payload['barcode'], $findBarcodes)) {
                throw new ApiException("Barcode sudah digunakan, gunakan barcode yang lain");
            }

            // Validasi harga jual
            if ($payload['selling_price'] < $payload['purchase_price']) {
                throw new ApiException('Harga jual tidak boleh lebih rendah dari harga beli. Pastikan harga jual lebih tinggi atau sama dengan harga beli.');
            }

            // Proses unggahan gambar
            $filename = "product-default.png";
            if ($request->hasFile('image')) {
                $extension = $request->file('image')->extension();
                $filename = time() . '.' . $extension;
                $this->fileService->uploadProductImage($request->file('image'), $filename);
                $filename = "images/{$filename}";
            }

            // Simpan produk
            $insertProduct = $this->productRepository->create([
                "name" => $payload['name'],
                "barcode" => $payload['barcode'],
                "stock" => intval($payload['stock']),
                "selling_price" => intval($payload['selling_price']),
                "purchase_price" => intval($payload['purchase_price']),
                "image" => $filename,
            ]);

            if (!$insertProduct) {
                throw new ApiException('Gagal menyimpan produk');
            }

            // Simpan data pembelian
            $purchasing = $this->purchasingRepository->create([
                'no_purchasing' => generateNoTransaction(),
                'product_id' => $insertProduct->id,
                'quantity' => $insertProduct->stock,
                'description' => $payload['description'] ?? "",
                'total_payment' => $insertProduct->purchase_price * $insertProduct->stock
            ]);

            if (!$purchasing) {
                throw new ApiException('Gagal menyimpan data pembelian');
            }

            // Validasi dan hubungkan kategori (jika ada)
            if (!empty($payload['category_id'])) {
                $categoriesId = $this->categoryRepository->getByStoreId($storeId)->pluck('id')->toArray();
                if (!in_array($payload['category_id'], $categoriesId)) {
                    throw new ApiException('Category tidak ditemukan');
                }
                $insertProduct->categories()->attach($payload['category_id']);
            }

            // Hubungkan produk dengan toko
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
    public function getProductByBarcode($barcode)
    {
        try {
            $product = $this->productRepository->getByBarcode($barcode);
            $product->purchase_price = floatval($product->purchase_price);
            $product->selling_price = floatval($product->selling_price);

            $product->link = url()->previous() . "/api/products/{$barcode}/barcode";


            // dd($products);
            return $product;
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
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
            $this->fileService->uploadProductImage($request->file('image'), $filename);
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
            $slug = $category;
            $category = str_replace('-', ' ', $category);
            $products = $this->productRepository->getByCategory($category)->get();

            $products = $products->map(function ($product) use ($slug) {
                $product->link = url()->previous() . "/api/categories/{$slug}/products";
                return $product;
            });

            return $products;
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


    /**
     * @inheritDoc
     */
    public function addExistsProducts(ProductStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $updatedProducts = [];
            $validated = $request->validated();


            foreach ($validated["products"] as $item) {

                if ($item['quantity_stok'] < 0) {
                    throw new ApiException("Jumlah stok tidak boleh kurang dari 0.");
                }

                if (!$this->productRepository->findByBarcode($item['barcode'])) {
                    throw new ApiException("Produk {$item['name']} Belum tersedia");
                }

                $getCurrentProduct = $this->productRepository->getByBarcode($item['barcode']);

                $currentStock = $getCurrentProduct['stock'];
                $newStock = $currentStock + $item['quantity_stok'];

                $item['stock'] = $newStock;



                $updated = $this->productRepository->updateByBarcode($item['barcode'], $item);

                if (!$updated) {
                    throw new ApiException("Gagal update data {$item['name']}");
                }

                $createPurchasing = $this->purchasingRepository->create([
                    'no_purchasing' => generateNoTransaction(),
                    'product_id' => $getCurrentProduct->id,
                    'quantity' => $item['quantity_stok'],
                    'description' => $item['description'] ?? "",
                    'total_payment' => $getCurrentProduct->purchase_price * $item['quantity_stok']
                ]);

                if (!$createPurchasing) {
                    throw new ApiException("Gagal update data {$item['name']}");
                }

                unset($item['quantity_stok']);

                $getCurrentProduct->stock = $item['stock'];
                $updatedProducts[$getCurrentProduct->barcode] = $this->productRepository->getByBarcode($item['barcode']);
                $updatedProducts[$getCurrentProduct->barcode]['link'] = url()->current();
            }


            DB::commit();
            return $updatedProducts;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            throw new ApiException($e->getMessage());
        }
    }
}
