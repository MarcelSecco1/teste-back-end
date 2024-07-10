<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Http\Requests\TransactionStoreUpdate;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = $this->transactionService->getAllTransactions();

        return new TransactionResource($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionStoreUpdate $request)
    {
        $transaction = $this->transactionService->createTransaction($request->all());

        return response()->json($transaction, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = $this->transactionService->getTransactionById((int) $id);

        return new TransactionResource($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = $this->transactionService->updateTransaction($request->all(), (int) $id);

        return response()->json($transaction, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->transactionService->deleteTransaction((int) $id);

        return response()->json([], 204);
    }
}
