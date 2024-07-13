<?php

namespace App\Services;

use App\Repositories\BalanceRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class TransactionService
{
    protected $transactionRepository, $userRepository, $balanceRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        BalanceRepository $balanceRepository,
        UserRepository $userRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        $this->balanceRepository = $balanceRepository;
    }

    public function getAllTransactions()
    {
        return $this->transactionRepository->getAllTransactions();
    }

    public function createTransaction(array $data)
    {
        DB::beginTransaction();

        try {
            $payer = $this->userRepository->getUserById($data['payer_id']);

            if ($payer->type == 'shopkeeper') {
                DB::rollBack();
                return ['message' => 'Shopkeepers cannot make transactions'];
            }

            $balancePayer = $this->balanceRepository->getBalanceByUserId($data['payer_id']);
            $balancePayee = $this->balanceRepository->getBalanceByUserId($data['payee_id']);

            if ($balancePayer->amount < $data['value']) {
                DB::rollBack();
                return ['message' => 'Insufficient balance'];
            }

            if ($data['value'] <= 0) {
                DB::rollBack();
                return ['message' => 'Invalid value'];
            }


            $authorization = json_decode($this->autorizathionTransaction());

            if ($authorization->status != "success" || $authorization->data->authorization != true) {
                DB::rollBack();
                return ['message' => 'Transaction not authorized'];
            }

            $balancePayer->amount -= $data['value'];
            $this->balanceRepository->updateBalance(['amount' => $balancePayer->amount], $balancePayer->id);

            $balancePayee->amount += $data['value'];
            $this->balanceRepository->updateBalance(['amount' => $balancePayee->amount], $balancePayee->id);

            $transaction = $this->transactionRepository->createTransaction($data);
            DB::commit();

            $this->updateStatusTransaction($transaction->id, 'completed');

            return $transaction;
        } catch (\Exception $e) {

            DB::rollBack();
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
            $payer = $this->userRepository->getUserById($transaction['payer_id']);

            if ($payer->type == 'shopkeeper') {
                $this->updateStatusTransaction($transaction->id, 'canceled');
                DB::rollBack();
                return ['message' => 'Shopkeepers cannot make transactions'];
            }
            if ($transaction->status == 'completed') {
                DB::rollBack();
                return ['message' => 'Transaction already completed'];
            }

            if ($payer->balance < $data['value']) {
                $this->updateStatusTransaction($transaction->id, 'canceled');
                DB::rollBack();
                return ['message' => 'Insufficient balance'];
            }

            if (isset($data['value']) || $data['value'] <= 0) {
                $this->updateStatusTransaction($transaction->id, 'canceled');
                DB::rollBack();
                return ['message' => 'Invalid value'];
            }


            $transaction = $this->transactionRepository->updateTransaction($data, $idTransaction);
            $this->updateStatusTransaction($idTransaction, 'completed');
            DB::commit();

            return ['updated' => $transaction];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['message' => 'Error updating transaction'];
        }
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
            $this->transactionRepository->updateTransaction(['completed_at' => now(), 'status' => $status], $idTransaction);
        }

        if ($status == 'canceled') {
            $this->transactionRepository->updateTransaction(['canceled_at' => now(), 'status' => $status], $idTransaction);
        }
    }
}
