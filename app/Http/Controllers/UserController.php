<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreUpdate;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();

        return UserResource::collection($users);
    }

    public function store(UserStoreUpdate $request)
    {
        $user = $this->userService->createUser($request->all());

        return response()->json(["created" => "ok"], 201);
    }

    public function show(int $idUser)
    {
        $user = $this->userService->getUserById($idUser);
        return new UserResource($user);
    }

    public function update(UserStoreUpdate $request, int $idUser)
    {
        $this->userService->updateUser($request->all(), $idUser);

        return response()->json(["updated" => "ok"], 200);
    }

    public function destroy(int $idUser)
    {
        $this->userService->deleteUser($idUser);

        return response()->json(["deleted" => "ok"], 200);
    }
}
