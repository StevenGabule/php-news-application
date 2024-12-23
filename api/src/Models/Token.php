<?php

namespace App\Models;

use App\Controllers\Controller;
use App\Database\DBConnection;

class Token extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = DBConnection::getInstance();
    }
}
