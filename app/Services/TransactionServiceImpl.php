<?php


namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\TransactionStoreRequest;
use App\Repositories\DetailTransactionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;

class TransactionServiceImpl implements TransactionService
{

    public function __construct(public TransactionRepository $transactionRepository, public DetailTransactionRepository $detailTransaction, public ProductRepository $productRepository)
    {
    }

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

            // validation, whether the Products are from different stores
            $count = array_count_values($storeId);
            if (count($count) !== 1) {
                throw new ApiException('Transaksi tidak dapat diproses. Produk berasal dari toko yang berbeda.');
            }


            foreach ($transaction['items'] as $item) {
                $currenProduct = $products[$item['id_product']];
                $totalPayment += $item['quantity'] * $currenProduct->selling_price;
            }

            if ($transaction['cash'] < $totalPayment) {
                throw new ApiException("gagal menambahkan data transaction, cash kurang dari total transaction");
            }

            $insertTransaction = $this->transactionRepository->create([
                "no_transaction" => generateNoTransaction(),
                "total_payment" => $totalPayment,
                "cash" => $transaction['cash'],
            ]);

            foreach ($transaction['items'] as $item) {
                $currenProduct = $products[$item['id_product']];
                $this->detailTransaction->create([
                    "id_transaction" => $insertTransaction->id,
                    "id_product" => $item['id_product'],
                    "item_price" => $currenProduct->selling_price,
                    "quantity" => $item['quantity'],
                    "total_price" => $currenProduct->selling_price * $item['quantity'],
                ]);

                $currenProduct->stock -= $item['quantity'];
                $currenProduct->save();
            }

            generateInvoice($insertTransaction->no_transaction);

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
    public function update(TransactionStoreRequest $data, array $transaction)
    {
    }
}
