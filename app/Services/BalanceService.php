<?php

namespace App\Services;

use App\Models\Balance;
use App\Repositories\BalanceRepository;
use App\Repositories\UserRepository;

class BalanceService
{
    protected $balanceRepository, $userRepository;

    public function __construct(
        BalanceRepository $balanceRepository,
        UserRepository $userRepository
    ) {
        $this->balanceRepository = $balanceRepository;
        $this->userRepository = $userRepository;
    }

    public function getAllBalances()
    {
        return $this->balanceRepository->getAllBalances();
    }

    public function createBalance(array $data)
    {
       return $this->balanceRepository->createBalance($data);
    }

    public function getBalanceById(int $idBalance)
    {
        return $this->balanceRepository->getBalanceById($idBalance);
    }

    public function updateBalance(array $data, int $balanceId)
    {
        return $this->balanceRepository->updateBalance($data, $balanceId);
    }

    public function deleteBalance(int $idBalance)
    {
        return $this->balanceRepository->deleteBalance($idBalance);
    }
}
