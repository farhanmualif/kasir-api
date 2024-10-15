<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Services\FileService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(public  TransactionService $transactionService, public FileService $fileService) {}
    public function index()
    {
        return \responseJson("data transaksi ditemukan", $this->transactionService->getAll());
    }

    public function store(TransactionStoreRequest $request)
    {
        return \responseJson("berhasil menyimpan data transaksi", $this->transactionService->create($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $noTransaction)
    {
        return responseJson("berhasil mendapatkan data", $this->transactionService->getByNoTransaction($noTransaction));
    }
    public function showInvoice(string $noTransaction)
    {

        $fileInvoice = $this->fileService->getTrancsactionIvoice($noTransaction);

        // get MIME type from file
        $mimeType = mime_content_type($fileInvoice);

        return response()->file($fileInvoice, [
            "Content-Type" => $mimeType,
        ]);
    }

    public function showSalesInvoice(string $noTransaction)
    {
        return responseJson("Berhasil Mendaapatkan invoice", $this->transactionService->getInvoice($noTransaction));
    }
}
