<?php


namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryServiceImpl implements CategoryService
{

    public function __construct(public CategoryRepository $categoryRepository, public ProductRepository $productRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(CategoryStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $stores = [];
            $storeId = Auth::user()->stores->first()->id;

            $findCategory = $this->categoryRepository->getByStoreId($storeId);

            foreach ($findCategory as $store) {
                array_push($stores, strtolower($store["name"]));
            }

            if (in_array(strtolower($data['name']), $stores)) throw new ApiException("category sudah ada");
            $data["store_id"] = $storeId;
            $insertCategory = $this->categoryRepository->create($data);
            return $this->categoryRepository->getById($insertCategory->id);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id)
    {
        try {

            $this->findById($id) ? throw new ApiException('category tidak ditemukan') :  $this->categoryRepository->deleteById($id);
            return $this->categoryRepository->findById($id);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid(string $uuid)
    {
        try {

            !$this->categoryRepository->findByUuid($uuid)->exists() ? throw new ApiException('category tidak ditemukan') :  $this->categoryRepository->deleteByUuid($uuid);
            return $this->categoryRepository->findByUuid($uuid);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id)
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid)
    {
        return $this->categoryRepository->findById($uuid);
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
        $this->categoryRepository->findById($id) ? throw new ApiException('category tidaka ditemukan') : $this->categoryRepository->getById($id);
    }

    /**
     * @inheritDoc
     */
    public function getByName(string $name)
    {
        return $this->categoryRepository->getByName($name);
    }

    /**
     * @inheritDoc
     */
    public function getByUuid(string $uuid)
    {
        if (!$this->categoryRepository->findByUuid($uuid)) {
            throw new ApiException('category tidak ditemukan');
        }
        return $this->categoryRepository->getByUuid($uuid);
    }

    /**
     * @inheritDoc
     */
    public function updateById(int $id, CategoryStoreRequest $data)
    {
    }

    /**
     * @inheritDoc
     */
    public function updateByUuid(string $uuid,  CategoryStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            if ($this->categoryRepository->findByUuid($uuid)->exists() == false) {
                throw new ApiException('category tidak ditemukan');
            }
            $this->categoryRepository->updateByUuid($uuid, $validated);
            return $this->categoryRepository->getByUuid($uuid);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }
    /**
     * @inheritDoc
     */
    /**
     * @inheritDoc
     */
    public function getAll()
    {
        try {
            return $this->categoryRepository->getAll()->map(function ($category) {
                $category['link'] = url()->current() . "/{$category->uuid}";
                return $category;
            });
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }
    /**
     * @inheritDoc
     */
    public function updateByProductUuid(string $productUuid, CategoryUpdateRequest $request)
    {

        DB::beginTransaction();
        try {
            $payload = $request->validated();

            if (!$this->productRepository->findByUuid($productUuid)->exists()) {
                throw new ApiException('product tidak ditemukan');
            }
            $product = $this->productRepository->findByUuid($productUuid)->first();
            $product->category()->sync([$payload['category_id']]);

            $response = $product->with('category')->where('uuid', $productUuid)->first();
            DB::commit();
            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();
            return responseJson($th->getMessage());
            // throw new ApiException($th->getMessage(), $th->getCode(), $th);
        }
    }
}
