<?php

namespace App\Http\Controllers;

use App\Http\Requests\BalanceStoreUpdate;
use App\Services\BalanceService;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    protected $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function index()
    {
        $balances = $this->balanceService->getAllBalances();
        return response()->json($balances, 200);
    }

    public function store(BalanceStoreUpdate $request)
    {
        $balance = $this->balanceService->createBalance($request->all());
        return response()->json($balance, 201);
    }

    public function show(int $id)
    {
        $balance = $this->balanceService->getBalanceById((int) $id);
        return response()->json($balance, 200);
    }

    public function update(BalanceStoreUpdate $request, int $id)
    {
        $balance = $this->balanceService->updateBalance($request->all(), (int) $id);

        if (isset($balance['message'])) {
            return response()->json($balance, 400);
        }
        return response()->json(["updated" => 'ok'], 200);
    }

    public function destroy(int $id)
    {
        $balance = $this->balanceService->deleteBalance((int) $id);
        return response()->json($balance, 204);
    }
}
