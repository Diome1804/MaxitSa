<?php

namespace App\Service;

use Src\Repository\UserRepository;
use Src\Entity\User;

class UserService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }


    public function getAllUsers(): array
    {
        return $this->userRepo->findAll();
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepo->findById($id);
    }
}
