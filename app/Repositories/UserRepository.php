<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getAllUsers()
    {
        return $this->user->all();
    }

    public function createUser(array $data)
    {
        return $this->user->create($data);
    }

    public function getUserById(int $idUser)
    {
        return $this->user->findOrFail($idUser);
    }

    public function updateUser(array $data, int $idUser)
    {
        $user = $this->getUserById($idUser);
        $user->update($data);

        return $user;
    }

    public function deleteUser(int $idUser)
    {
        $user = $this->getUserById($idUser);
        $user->delete();
    }
}
