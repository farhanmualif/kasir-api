<?php


namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Invoice;
use App\Repositories\DetailTransactionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Storage;

class TransactionServiceImpl implements TransactionService
{

    public function __construct(public TransactionRepository $transactionRepository, public DetailTransactionRepository $detailTransaction, public ProductRepository $productRepository, public Invoice $invoices, public DiscountService $discountService) {}

    /**
     * @inheritDoc
     */
    public function create(TransactionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $payloadValidate = $request->validated();
            $transaction = $payloadValidate['transaction'];
            $totalPayment = 0;
            $products = [];
            $storeId = [];

            foreach ($transaction['items'] as $item) {
                $findProduct = $this->productRepository->findById($item['id_product']);
                $storeId[] = $findProduct->stores()->first()->id;

                if (!$findProduct) {
                    throw new ApiException("product dengan id {$item['id_product']} tidak ditemukan");
                }

                $products[$item['id_product']] = $findProduct;

                if ($findProduct->stock <= 0) {
                    throw new ApiException("stok product {$findProduct->name} tidak tersedia");
                }

                if (($findProduct->stock - $item['quantity']) < 0) {
                    throw new ApiException("stok product {$findProduct->name} tidak mencukupi");
                }
            }

            // Validasi store
            $count = array_count_values($storeId);
            if (count($count) !== 1) {
                throw new ApiException('Transaksi tidak dapat diproses. Produk berasal dari toko yang berbeda.');
            }

            // Hitung total pembayaran sebelum diskon
            $totalBeforeDiscount = 0;
            foreach ($transaction['items'] as $item) {
                $currentProduct = $products[$item['id_product']];
                $totalBeforeDiscount += $item['quantity'] * $currentProduct->selling_price;
            }

            // Cek dan terapkan diskon jika ada
            $discount = null;
            $discountAmount = 0;
            if (isset($transaction['discount_uuid'])) {
                $discount = $this->discountService->getByUuId($transaction['discount_uuid']);

                if ($discount) {
                    // Hitung diskon
                    if ($discount->type === 'percentage') {
                        $discountAmount = $totalBeforeDiscount * ($discount->value / 100);
                    } else { // fixed
                        $discountAmount = $discount->value;
                    }
                }
            }

            // Hitung total pembayaran setelah diskon
            $totalPayment = $totalBeforeDiscount - $discountAmount;

            // Validasi cash
            if ($transaction['cash'] < $totalPayment) {
                throw new ApiException("Gagal menambahkan data transaksi, cash kurang dari total transaksi");
            }

            // Buat transaksi
            $insertTransaction = $this->transactionRepository->create([
                "no_transaction" => generateNoTransaction(),
                "total_payment" => $totalPayment,
                "cash" => $transaction['cash'],
                "discount_id" => $discount ? $discount->id : null, // Simpan ID diskon jika ada
            ]);

            // Buat detail transaksi
            foreach ($transaction['items'] as $item) {
                $currentProduct = $products[$item['id_product']];
                $this->detailTransaction->create([
                    "id_transaction" => $insertTransaction->id,
                    "id_product" => $item['id_product'],
                    "item_price" => $currentProduct->selling_price,
                    "quantity" => $item['quantity'],
                    "total_price" => $currentProduct->selling_price * $item['quantity'],
                ]);

                // Kurangi stok
                $currentProduct->stock -= $item['quantity'];
                $currentProduct->save();
            }

            // Generate invoice
            $pdfFilename = $this->generateInvoice($insertTransaction->no_transaction);

            // Simpan invoice
            $this->invoices->create([
                'transaction_id' => $insertTransaction->id,
                'filename' => $pdfFilename
            ]);

            DB::commit();
            return $insertTransaction;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new ApiException($th->getMessage());
        }
    }


    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return responseJson('berhasil mendapatkan data', $this->transactionRepository->getAll());
    }

    /**
     * @inheritDoc
     */
    public function getByNoTransaction(string $noTransaction)
    {
        try {
            $findTransaction = $this->transactionRepository->findByNoTransaction($noTransaction);
            if (!$findTransaction->exists())  throw new ApiException('transaction tidak ditemukan');
            return $findTransaction->first();
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function update(TransactionStoreRequest $data, array $transaction) {}
    /**
     * @inheritDoc
     */
    public function generateInvoice(string $noTransaction)
    {
        try {

            $transaction = $this->transactionRepository->getSalesInvoice($noTransaction);

            $totalPrice = 0;
            foreach ($transaction as $item) {
                $totalPrice += $item->total_price;
            }

            $detailTransaction = [
                'no_transaction' => $transaction->first()->no_transaction,
                'time' => $transaction->first()->time,
                'date' => $transaction->first()->date,
                'cash' => intval($transaction->first()->cash),
                'change' => $transaction->first()->cash - $totalPrice,
                'total_price' => intval($totalPrice),
                'total_payment' => intval($transaction->first()->total_payment),
                'items' => []
            ];

            foreach ($transaction as $items) {
                $detailTransaction['items'][] = [
                    'name' => $items->name,
                    'quantity' => $items->quantity,
                    'item_price' => $items->item_price,
                    'total_price' => $items->total_price
                ];
            }

            // $pdf = App::make('dompdf.wrapper');
            // $pdf->loadView('invoice', compact('detail_transaction'));


            $pdf = FacadePdf::loadView('invoice', compact('detailTransaction'));
            $pdf->setPaper('A4', 'portrait');

            // Set lebar kertas menjadi 8cm
            // Simpan file PDF ke storage
            $pdf_filename = "invoice_$noTransaction.pdf";

            Storage::put("public/invoices/$pdf_filename", $pdf->output());

            // Return the PDF file URL
            return $pdf_filename;
        } catch (\Throwable $th) {
            throw new ApiException("tidak dapat membuat struk {$th->getMessage()}");
        }
    }
    /**
     * @inheritDoc
     */
    /**
     * @inheritDoc
     */
    public function getInvoice(string $noTransaction)
    {
        try {
            $invoice = $this->transactionRepository->getSalesInvoice($noTransaction);
            $invoice->map(function ($item) {
                $item->total_payment = (int) $item->total_payment;
                $item->cash = (int) $item->cash;
            });

            return [
                'no_transaction' => $invoice->first()->no_transaction,
                'total_payment' => $invoice->first()->total_payment,
                'cash' => $invoice->first()->cash,
                'time' => $invoice->first()->time,
                'date' => $invoice->first()->date,
                'items' =>  $invoice->map(function ($item) {
                    return  [
                        "name" => $item->name,
                        "quantity" => $item->quantity,
                        "item_price" => $item->item_price,
                        "total_price" => $item->total_price,
                    ];
                })
            ];
        } catch (\Throwable $th) {
            throw new ApiException("Terjadi kesalahan {$th->getMessage()}");
        }
    }
}
