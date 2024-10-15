<?php


namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Category;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryRepositoryImpl implements CategoryRepository
{

    public function __construct(public Category $category)
    {
    }

    /**
     * @inheritDoc
     */
    public function create($data)
    {
        try {
            return $this->category->create($data);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id)
    {
        return $this->category->find($id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid(string $uuid)
    {
        return $this->category->where("uuid", $uuid)->delete();
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        try {
            $storeId = Auth::user()->stores->first()->id;

            return $this->category->select(
                'categories.id as id',
                'categories.uuid as uuid',
                'categories.name as name',
                DB::raw('COALESCE(SUM(products.purchase_price * products.stock), 0) as capital'),
                DB::raw('CAST(COALESCE(SUM(products.stock), 0) AS SIGNED) as remaining_stock'),
                'categories.created_at',
                'categories.updated_at'
            )
                ->leftJoin('product_category', 'product_category.category_id', 'categories.id')
                ->leftJoin('products', 'product_category.product_id', 'products.id')
                ->where('categories.store_id', '=', $storeId)
                ->groupBy('categories.id', 'categories.uuid', 'categories.name', 'categories.created_at', 'categories.updated_at')->orderBy('categories.created_at', 'desc')->get();
            // dd($categories);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
        return $this->category->find($id);
    }

    /**
     * @inheritDoc
     */
    public function getByUuid(string $uuid)
    {
        return $this->category->where("uuid", $uuid)->first();
    }

    /**
     * @inheritDoc
     */
    public function updateById(int $id, array $data)
    {
        return $this->category->find($id)->update($data);
    }

    /**
     * @inheritDoc
     */
    public function updateByUuid(string $uuid, array $data)
    {
        return $this->category->where('uuid', $uuid)->update($data);
    }
    /**
     * @inheritDoc
     */
    public function findById(int $id)
    {
        return $this->category->where('id', $id)->exists();
    }

    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid)
    {
        return $this->category->where('uuid', $uuid);
    }
    /**
     * @inheritDoc
     */
    public function getByName(string $name)
    {
        return $this->category->where('name', $name);
    }
    /**
     * @inheritDoc
     */
    public function getByStoreId(int $storeId)
    {
        try {
            return $this->category->whereHas('store', function ($query) use ($storeId) {
                $query->where('stores.id', $storeId);
            })->with('store')->orderBy('categories.created_at', 'desc')->get();
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }
}
