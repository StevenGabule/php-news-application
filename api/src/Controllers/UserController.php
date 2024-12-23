<?php

namespace App\Controllers;

use App\Services\{AuthService, ValidationService};

class UserController extends Controller
{
  private $authService;
  private $validationService;
  private $secretKey;

  public function __construct()
  {
    $this->authService = new AuthService();
    $this->validationService = new ValidationService();
    $this->secretKey = 'MY_SECRET_KEY_VALUE';
  }

  public function register()
  {
    $input = json_decode(file_get_contents('php://input'), true);
    $this->authService->register($input);
    return $this->jsonResponse(['message' => 'User registered successfully'], 201);
  }

  public function login()
  {
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $this->authService->login($input['email'], $input['password']);
    if (!$token) {
      return $this->jsonResponse(['error' => 'Invalid credentials'], 401);
    }

    return $this->jsonResponse(['token' => $token]);
  }

  public function getUser()
  {
    $user = $this->authService->authenticate();

    if (!$user) {
      return $this->jsonResponse(['error' => 'Unauthorized'], 401);
    }

    return $this->jsonResponse($user);
  }

  public function logout()
  {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
      return $this->jsonResponse(['error' => 'No token provided.'], 401);
    }

    if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
      return $this->jsonResponse(['error' => 'Invalid token format.'], 401);
    }

    $token = $matches[1];

    try {
      $decoded = $this->authService->decodeToken($token);

      // if either is missing, token might be malformed
      if (empty($decoded->jti) || empty($decoded->exp)) {
        return $this->jsonResponse(['error' => 'Invalid tokens'], 401);
      }

      // revoke the token
      $this->authService->revokeToken($decoded->jti, $decoded->exp);

      return $this->jsonResponse(['message' => 'Logged out']);
    } catch (\Exception $e) {
      // e.g. token invalid, expired, bad signature, etc.
      return $this->jsonResponse(['error' => $e->getMessage()], 401);
    }
  }
}