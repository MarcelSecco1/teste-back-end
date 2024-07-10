<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class TransactionService
{
    protected $transactionRepository, $userRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        UserRepository $userRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
    }

    public function getAllTransactions()
    {
        return $this->transactionRepository->getAllTransactions();
    }

    public function createTransaction(array $data)
    {
        DB::beginTransaction();

        try {
            $transaction = $this->transactionRepository->createTransaction($data);
            $payer = $this->userRepository->getUserById($data['payer_id']);

            if ($payer->type == 'shopkeeper') {
                $this->updateStatusTransaction($transaction->id, 'canceled');
                DB::commit();
                return ['message' => 'Shopkeepers cannot make transactions'];
            }

            if ($data['value'] <= 0) {
                $this->updateStatusTransaction($transaction->id, 'canceled');
                DB::commit();
                return ['message' => 'Invalid value'];
            }


            $authorization = json_decode($this->autorizathionTransaction());

            if ($authorization->status != "success" || $authorization->data->authorization != true) {
                $this->updateStatusTransaction($transaction->id, 'canceled');
                DB::commit();
                return ['message' => 'Transaction not authorized'];
            }

            $transaction = $this->transactionRepository->createTransaction($data);
            DB::commit();

            $this->updateStatusTransaction($transaction->id, 'completed');

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            // return throw $e;
            return ['message' => 'Error creating transaction'];
        }
    }

    public function getTransactionById(int $idTransaction)
    {
        return $this->transactionRepository->getTransactionById($idTransaction);
    }

    public function updateTransaction(array $data, int $idTransaction): array
    {
        DB::beginTransaction();
        try {
            $transaction = $this->transactionRepository->getTransactionById($idTransaction);
            $payer = $this->userRepository->getUserById($data['payer_id']);

            if ($payer->type == 'shopkeeper') {
                $this->updateStatusTransaction($transaction->id, 'canceled');
                DB::commit();
                return ['message' => 'Shopkeepers cannot make transactions'];
            }
            if ($transaction->status == 'completed') {
                DB::commit();
                return ['message' => 'Transaction already completed'];
            }

            if ($data['value'] <= 0) {
                $this->updateStatusTransaction($transaction->id, 'canceled');
                DB::commit();
                return ['message' => 'Invalid value'];
            }

            $transaction = $this->updateTransaction($data, $idTransaction);
            DB::commit();

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            return ['message' => 'Error updating transaction'];
        }

        return $this->transactionRepository->updateTransaction($data, $idTransaction);
    }

    public function deleteTransaction(int $idTransaction)
    {
        return $this->transactionRepository->deleteTransaction($idTransaction);
    }

    private function autorizathionTransaction()
    {
        // $client = new Client();

        // $response = $client->request('GET', 'https://util.devi.tools/api/v2/authorize');

        return json_encode([
            'status' => 'success',
            'data' => [
                'authorization' => true
            ]
        ]);
    }

    private function updateStatusTransaction(int $idTransaction, string $status)
    {

        if ($status != 'completed' && $status != 'canceled') {
            return ['message' => 'Invalid status'];
        }

        if ($status == 'completed') {
            $this->transactionRepository->updateTransaction(['completed_at' => now()], $idTransaction);
        }

        if ($status == 'canceled') {
            $this->transactionRepository->updateTransaction(['canceled_at' => now()], $idTransaction);
        }
    }
}
