<?php

namespace App\Validations;

class UserValidationRules
{
  public static function register(): array
  {
    return [
      'name' => 'required',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6',
    ];
  }
  public static function login(): array
  {
    return [
      'email' => 'required|email',
      'password' => 'required|min:6',
    ];
  }
}