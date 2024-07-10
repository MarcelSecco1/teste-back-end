<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\User;
use GuzzleHttp\Client;

class TransactionRepository
{
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function getAllTransactions()
    {
        return $this->transaction->all();
    }

    public function createTransaction(array $data)
    {
        return $this->transaction->create($data);
    }

    public function getTransactionById(int $idTransaction)
    {
        return $this->transaction->findOrFail($idTransaction);
    }

    public function updateTransaction(array $data, int $transactionId)
    {
        return $this->getTransactionById($transactionId)->update($data);
    }

    public function deleteTransaction(int $idTransaction)
    {
        $transaction = $this->getTransactionById($idTransaction);
        $transaction->delete();
    }


}
