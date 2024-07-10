<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAllUsers();
    }

    public function createUser(array $data)
    {
        return $this->userRepository->createUser($data);
    }

    public function getUserById(int $idUser)
    {
        return $this->userRepository->getUserById($idUser);
    }

    public function updateUser(array $data, int $idUser)
    {
        return $this->userRepository->updateUser($data, $idUser);
    }

    public function deleteUser(int $idUser)
    {
        return $this->userRepository->deleteUser($idUser);
    }

}
