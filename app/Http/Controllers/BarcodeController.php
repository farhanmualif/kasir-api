<?php

namespace App\Http\Controllers;

use App\Http\Resources\BarcodeCollection;
use App\Models\Barcode;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \responseJson("Barcode mendapatkan data", new BarcodeCollection(Barcode::all()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(string $code)
    {

        $barcode = Barcode::where('code', $code)->first();
        // \dd($barcode);
        \responseJson("Prooduk Item ditemukan", $barcode);
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
