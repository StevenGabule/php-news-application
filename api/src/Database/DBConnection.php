<?php

namespace App\Database;

class DBConnection
{
  private static $instance = null;
  private $connection;

  private function __construct()
  {
    $dbHost = getenv('DB_HOST') ?: '172.19.80.1';
    $dbPort = getenv('DB_PORT') ?: '3307';
    $dbName = getenv('DB_DATABASE') ?: 'blogging_db';
    $dbUser = getenv('DB_USERNAME') ?: 'jpgabs';
    $dbPass = getenv('DB_PASSWORD') ?: 'password';
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8";
    
    $this->connection = new \PDO($dsn, $dbUser, $dbPass);
    $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  }

  public static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new DBConnection();
    }

    return self::$instance->connection;
  }
}
