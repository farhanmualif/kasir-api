<?php


namespace App\Repositories;

use App\Models\Category;

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
        return $this->category->all();
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
        return $this->category->where('uuid', $uuid)->exists();
    }
}
