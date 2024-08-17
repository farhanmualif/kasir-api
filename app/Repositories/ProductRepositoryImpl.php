<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductRepositoryImpl implements ProductRepository
{

    public function __construct(public Product $product, public Category $category, public AuthManager $auth) {}

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        $data['uuid'] =  Str::uuid();
        return $this->product->create($data);
    }

    /**
     * @inheritDoc
     */
    public function deleteByBarcode(string $barcode)
    {
        return $this->product->where("barcode", $barcode)->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id)
    {
        return $this->product->find($id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid(string $uuid)
    {
        return $this->product->where('uuid', $uuid)->delete();
    }

    /**
     * @inheritDoc
     */
    public function findByBarcode(string $barcode)
    {

        return $this->product
            ->join('product_store', 'products.id', '=', 'product_store.product_id')
            ->join('stores', 'stores.id', '=', 'product_store.store_id')
            ->where('product_store.store_id', $this->auth->user()->stores()->first()->id)
            ->where('products.barcode', $barcode)
            ->exists();;
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id)
    {
        return $this->product->find($id);
    }


    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid)
    {
        return $this->product->where("uuid", $uuid);
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        $storeId =  $this->auth->user()->stores()->first()->id;

        return $this->product->whereHas('stores', function ($query) use ($storeId) {
            $query->where('stores.id', $storeId);
        })->with(['category', 'stores']);
    }

    /**
     * @inheritDoc
     */
    public function getByBarcode(string $barcode)
    {
        return $this->product
            ->select(['products.id as id', 'products.uuid as uuid', 'products.name as name', 'products.barcode', 'products.stock', 'products.selling_price', 'products.purchase_price', 'products.image',  'products.created_at', 'products.updated_at'])
            ->join('product_store', 'products.id', '=', 'product_store.product_id')
            ->join('stores', 'stores.id', '=', 'product_store.store_id')
            ->where('product_store.store_id', $this->auth->user()->stores()->first()->id)
            ->where('products.barcode', $barcode)
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
        return $this->product->find($id)->first();
    }

    /**
     * @inheritDoc
     */
    public function getByName($name)
    {
        return $this->product->where('name', $name)->get();
    }

    /**
     * @inheritDoc
     */
    public function getByUuid(string $uuid)
    {

        return $this->product->where('uuid', $uuid)->first();
    }

    /**
     * @inheritDoc
     */
    public function updateByBarcode(string $barcode, array $data)
    {
        // Ambil store_id dari user yang sedang login
        $storeId = $this->auth->user()->stores()->first()->id;

        // Cari produk berdasarkan barcode dan store_id
        $product = $this->product
            ->whereHas('stores', function ($store) use ($storeId) {
                $store->where('stores.id', $storeId);
            })
            ->where('barcode', $barcode)
            ->first();

        // Update data produk
        return $product->update($data);
    }

    /**
     * @inheritDoc
     */
    public function updateById(int $id, array $data)
    {
        return $this->product->find($id)->update($data);
    }

    /**
     * @inheritDoc
     */
    public function updateByUuid(string $uuid, array $data)
    {
        return $this->product->where('uuid', $uuid)->update($data);
    }
    /**
     * @inheritDoc
     */
    public function getByCategory(string $category)
    {

        $products = $this->product->whereHas('category', function ($query) use ($category) {
            $query->where('name', $category);
        })
            ->join('product_store', 'product_store.product_id', '=', 'products.id')
            ->join('stores', 'product_store.store_id', '=', 'stores.id')
            ->where('stores.id', '=', Auth::user()->stores->first()->id)
            ->select('products.*')->orderBy('products.created_at', 'desc');

        return $products;
    }
    /**
     * @inheritDoc
     */
    public function addCategoriesToProduct(string $productUuid, array $categoriesId)
    {
        $product = $this->product->where('uuid', $productUuid)->first();
        return $product->category()->syncWithoutDetaching($categoriesId);
    }
    /**
     * @inheritDoc
     */
    public function deleteCategoriesInProduct(string $productUuid, array $categoriesId)
    {
        $product = $this->product->where('uuid', $productUuid)->first();
        $product->category()->detach($categoriesId);
        return $this->getByUuid($productUuid);
    }
    /**
     * @inheritDoc
     */
    public function findByStoreId(int $storeId)
    {

        return $this->product->whereHas('stores', function ($query) use ($storeId) {
            $query->where('stores.id', $storeId);
        })->with('stores')->get();
    }
}
