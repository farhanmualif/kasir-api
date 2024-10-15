<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Services\StoreService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function __construct(public UserService $userService, public StoreService $storeServices)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return responseJson('user ditemukan', $this->userService->getAll());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StoreStoreRequest $request)
    {
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
        try {
            $response =  $this->userService->updateByUuid($request->uuid, $request->all());

            return responseJson('berhasil update data', $response);
        } catch (\Throwable $th) {
            return responseJson('gagal update data', "{$th->getMessage()} {$th->getFile()} {$th->getLine()}", false, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        try {
            $this->userService->deleteByUuid($uuid);
            return responseJson("berhasil menghapus data", null);
        } catch (\Throwable $th) {
            return responseJson('gagal menghapus data', "{$th->getMessage()} {$th->getFile()} {$th->getLine()}", false, 500);
        }
    }
}
