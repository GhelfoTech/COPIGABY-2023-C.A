<?php

declare(strict_types=1);

namespace App\Models\Core;

use PDO;

abstract class ConexionBD
{
    protected string $host;
    protected string $dbname;
    protected string $username;
    protected string $password;
    protected ?PDO $conn = null;

    public function __construct(
        string $host = 'localhost',
        string $dbname = 'copigaby',
        string $username = 'root',
        string $password = ''
    ) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
    }

    protected function conectar(): PDO
    {
        if ($this->conn === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $this->host,
                $this->dbname
            );

            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return $this->conn;
    }

    protected function desconectar(): void
    {
        $this->conn = null;
    }

    protected function getHost(): string
    {
        return $this->host;
    }

    protected function setHost(string $host): void
    {
        $this->host = $host;
    }

    protected function getDbname(): string
    {
        return $this->dbname;
    }

    protected function setDbname(string $dbname): void
    {
        $this->dbname = $dbname;
    }

    protected function getUsername(): string
    {
        return $this->username;
    }

    protected function setUsername(string $username): void
    {
        $this->username = $username;
    }

    protected function getPassword(): string
    {
        return $this->password;
    }

    protected function setPassword(string $password): void
    {
        $this->password = $password;
    }

    protected function getConn(): ?PDO
    {
        return $this->conn;
    }
}
