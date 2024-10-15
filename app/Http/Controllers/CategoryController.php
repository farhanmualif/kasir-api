<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Services\CategoryService;


class CategoryController extends Controller
{

    public function __construct(public CategoryService $categoryService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return responseJson('category_ditemukan', $this->categoryService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        $insertCategory = $this->categoryService->create($request);
        return responseJson('berhasil menambahkan data category', $insertCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return responseJson('data category ditemukan', $this->categoryService->getByUuid($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryStoreRequest $request, string $id)
    {
        return responseJson('berhasil update category', $this->categoryService->updateByUuid($id, $request));
    }

    public function updateByProductUuid(CategoryUpdateRequest $request, string $uuid)
    {
        $product = $this->categoryService->updateByProductUuid($uuid, $request);
        return responseJson("berhasil mengubah data category", $product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->categoryService->deleteByUuid($id);
        return responseJson("berhasil delete category");
    }
}
