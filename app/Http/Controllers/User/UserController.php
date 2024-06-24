<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Resources\StoreCollection;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return \response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StoreStoreRequest $request)
    {

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user_created = User::create($validated);
            // membuat store secara otomatis
            $store_created =  Store::create([
                'name' => $user_created->name . '_store',
                'address' => $user_created->address,

            ]);

            // Simpan relasi dengan Category
            $user_created->store()->attach($store_created->id);
            DB::commit();
            return responseJson("berhasil tambah user", new StoreCollection($user_created));
        } catch (\Throwable $th) {
            DB::rollBack();
            return responseJson("gagal menambahkan user, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
