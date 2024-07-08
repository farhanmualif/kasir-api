<?php


namespace App\Repositories;

use App\Models\Category;
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
        return $this->category->create($data);
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
        return $this->category->select(
            'categories.id as id',
            'categories.uuid as uuid',
            'categories.name as name',
            'categories.created_at',
            'categories.updated_at',
            DB::raw('COALESCE(SUM(products.purchase_price * products.stock), 0) as capital'),
            DB::raw('CAST(COALESCE(SUM(products.stock), 0) AS SIGNED) as remaining_stock')
        )
            ->leftJoin('product_category', 'categories.id', '=', 'product_category.category_id')
            ->leftJoin('products', 'product_category.product_id', '=', 'products.id')
            ->groupBy('categories.id', 'categories.name')
            ->get();
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
}
