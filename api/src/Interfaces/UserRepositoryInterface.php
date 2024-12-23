<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function create(array $data);
    public function findByEmail(String $email);
    public function findById(int $id);
}
