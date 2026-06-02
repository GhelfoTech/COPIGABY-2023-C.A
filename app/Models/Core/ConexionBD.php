<?php

declare(strict_types=1);

namespace App\Models\Core;

use PDO;

class ConexionBD
{
    private string $host;
    private string $dbname;
    private string $username;
    private string $password;
    private ?PDO $conn = null;

    public function __construct()
    {
        $this->host = '';
        $this->dbname = '';
        $this->username = '';
        $this->password = '';
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function setDbname(string $dbname): void
    {
        $this->dbname = $dbname;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function conectar(): void
    {
    }

    public function desconectar(): void
    {
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getDbname(): string
    {
        return $this->dbname;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getConn(): ?PDO
    {
        return $this->conn;
    }

    public function setConn(?PDO $conn): void
    {
        $this->conn = $conn;
    }
}
