<?php

namespace App\Models;

use App\Database\DBConnection;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = DBConnection::getInstance();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        return $stmt->execute($data);
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE id = :id");
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
