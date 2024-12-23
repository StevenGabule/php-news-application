<?php

namespace App\Exceptions;

class ExceptionHandler {
  public static function handle(\Throwable $e)
  {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
  }
}