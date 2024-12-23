<?php

namespace App\Middleware;

use App\Services\ValidationService;

class ValidationMiddleware implements MiddlewareInterface 
{
  private $rules;
  private $validationService;

  public function __construct(array $rules)
  {
    $this->rules = $rules;
    $this->validationService = new ValidationService();
  }

  public function handle($request, callable $next) 
  {
    $input = json_decode(file_get_contents("php://input"), true);
    if($input === null) {
      header('Content-Type: application/json', true, 400);
      echo json_encode(['error', 'Invalid or missing JSON payload']);
      return;
    }

    $validation = $this->validationService->validate($input, $this->rules);

    if($validation !== true) {
      header('Content-Type: application/json', true, 422);
      echo json_encode(['errors' => $validation]);
      return;
    }

    $request['body'] = $input;

    return $next($request);
  }
}