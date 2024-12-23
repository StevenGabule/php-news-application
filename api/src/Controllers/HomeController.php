<?php

namespace App\Controllers;

class HomeController extends Controller {
  public function index() {
    return $this->jsonResponse(['message' => 'Welcome to the api v1']);
  }
}