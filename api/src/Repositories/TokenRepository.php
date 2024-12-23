<?php

namespace App\Repositories;

use App\Database\DBConnection;

class TokenRepository {
  private $db;

  public function __construct()
  {
    $this->db = DBConnection::getInstance();
  }

  public function revoke(string $jti, string $expiresAt)
  {
    $stmt = $this->db->prepare('INSERT INTO revoked_tokens (jti, expires_at) VALUES (:jti, :expires_at)');
    return $stmt->execute([ 'jti' => $jti, 'expires_at' => $expiresAt ]);
  }

  public function isRevoked(string $jti): bool
  {
    $stmt = $this->db->prepare('SELECT id FROM revoked_tokens WHERE jti = :jti LIMIT 1');
    $stmt->execute(['jti' => $jti]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return !empty($row);
  }

}