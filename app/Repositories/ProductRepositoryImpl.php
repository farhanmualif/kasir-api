<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;

class ProductRepositoryImpl implements ProductRepository
{

    public function __construct(public Product $product, public Category $category)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
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
        return $this->product->where('barcode', $barcode)->exists();
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
        return $this->product->with("category");
    }

    /**
     * @inheritDoc
     */
    public function getByBarcode(string $barcode)
    {

        return $this->product->where("barcode", $barcode)->first();
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
        return $this->product->where('barcode', $barcode)->update($data);
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
        return $this->category->where('name', $category)->product();
    }
}
