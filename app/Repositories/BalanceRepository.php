<?php

namespace App\Repositories;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BalanceRepository
{
    protected $balance, $user;

    public function __construct(Balance $balance, User $user)
    {
        $this->user = $user;
        $this->balance = $balance;
    }

    public function getAllBalances(): Collection
    {
        return $this->balance->with('user')->get();
    }

    public function createBalance(array $data): mixed
    {
        return $this->balance->create($data);
    }

    public function getBalanceById(int $idBalance): Balance
    {
        return $this->balance->with('user')->findOrFail($idBalance);
    }

    public function updateBalance(array $data, int $balanceId)
    {
        return $this->getBalanceById($balanceId)->update($data);
    }

    public function deleteBalance(int $idBalance): bool | null
    {
        return $this->getBalanceById($idBalance)->delete();
    }

    public function getBalanceByUserId(int $userId)
    {
        return $this->balance->where('user_id', $userId)->first();
    }
}
