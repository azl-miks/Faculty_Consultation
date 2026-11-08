<?php

require_once __DIR__ . '/../config/config.php';

class DBRepository
{
    protected $connection;

    public function __construct()
    {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed.");
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}