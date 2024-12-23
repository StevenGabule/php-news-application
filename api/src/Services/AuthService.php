<?php

namespace App\Services;

use App\Repositories\{UserRepository, TokenRepository};
use Firebase\JWT\{JWT, Key};

class AuthService
{
  private $userRepo;
  private $tokenRepo;
  private $secretKey;

  public function __construct()
  {
    $this->userRepo = new UserRepository();
    $this->tokenRepo = new TokenRepository();
    $this->secretKey = 'MY_SECRET_KEY_VALUE';
  }

  /**
   * Generate a new JWT for a given user ID.
   * Add a 'jti' claim to uniquely identity the token.
   */ 
  public function generateToken(int $userId) : string
  {
    $payload = [
      'iss' => 'http://localhost:8000',
      'sub' => $userId,
      'iat' => time(),
      'exp' => time() + 3600,
      'jti' => bin2hex(random_bytes(8))
    ];

    return JWT::encode($payload, $this->secretKey,'HS256');
  }

  /**
   * Decode a token and return its payload.
   * Return null or throw an exception if invalid.
   */
  public function decodeToken(string $token)
  {
    return JWT::decode($token, new Key($this->secretKey, 'HS256'));
  }

  /**
   * Check if token is revoked by jti in DB. 
   */
  public function isTokenRevoked(string $jti): bool
  {
    return $this->tokenRepo->isRevoked($jti);
  }

  /**
   * Revoke (logout) a token by storing it in revoked_tokens.
   * Provide the token's jti and expiry time.
   */
  public function revokeToken(string $jti, int $exp) {
    $expiresAt = date('Y-m-d H:i:s', $exp);
    return $this->tokenRepo->revoke($jti, $expiresAt);
  }

  public function register($data)
  {
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    return $this->userRepo->create($data);
  }

  public function login($email, $password)
  {
    $user = $this->userRepo->findByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
      return self::generateToken($user['id']);
    }

    return false;
  }

  public function authenticate()
  {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
      $matches = [];
      if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        $token = $matches[1];
        try {
          // $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));`1
          $decoded = self::decodeToken($token);
          if(self::isTokenRevoked($decoded->jti)) {
            return false;
          }
          return $this->userRepo->findById($decoded->sub);
        } catch (\Exception $e) {
          return false;
        }
      }
    }
    return false;
  }
}
