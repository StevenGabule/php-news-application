<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    private $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }

    public function create($data)
    {
        return $this->userModel->create($data);
    }
    public function findByEmail(string $email)
    {
        return $this->userModel->findByEmail($email);
    }
    public function findById(int $id)
    {
      return $this->userModel->findById($id);
    }
}
