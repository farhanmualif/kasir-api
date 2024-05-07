<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = DB::table('categories')
                ->select(
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

            return \responseJson("kategori berhasil di temukan", $categories);
        } catch (\Throwable $th) {
            return \responseJson("terjadi kesalahan {$th->getMessage()}", null, false, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $insert =  Category::create(
                $data
            );
            return \responseJson("berhasil menambahkan categori", $insert);
        } catch (\Throwable $th) {
            return \responseJson("gagal menambahkan data {$th->getMessage()}", null, false, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = Category::where('uuid', $id)->first();

            if ($category == null) {
                return \responseJson("data categori tidak ditemukan", null, false, 404);
            } else {
                return \responseJson("data categori berhasil ditemukan", $category);
            }
        } catch (\Throwable $th) {
            return \responseJson("gagal mendapatkan data categori {$th->getMessage()}", null, false, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryStoreRequest $request, string $id)
    {
        try {
            $find_category = Category::where('uuid', $id)->first();
            if ($find_category == null) {
                return responseJson("data categori tidak ditemukan", null, false, 404);
            }
            $validated = $request->validated();
            Category::where('uuid', $id)->update($validated);
            $updated = Category::where('uuid', $id)->first();
            return \responseJson("berhasil update data categori", $updated);
        } catch (\Throwable $th) {
            return \responseJson("gagal update data categori {$th->getMessage()}", null, false, 404);
        }
    }

    public function updateProductCategory(Request $request, string $uuid)
    {
        DB::beginTransaction();
        try {
            $product = Product::where('uuid', $uuid)->first();
            $payload = $request->all();
            $product->category()->sync([$payload['id']]);
            $response = Product::with('category')->where('uuid', $uuid)->first();
            DB::commit();
            return \responseJson("berhasil update categori produk", $response);
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::commit();
            return \responseJson("gagal update categori produck {$th->getMessage()}", null, false, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            Category::where("uuid", $id)->delete();
            DB::commit();
            return responseJson("berhasil menghapus data categori", null, true, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::commit();
            return responseJson("gagal menghapus data categori {$th->getMessage()}", null, false, 500);
        }
    }
}
