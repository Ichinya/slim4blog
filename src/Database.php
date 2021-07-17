<?php

namespace Blog;

use http\Exception\InvalidArgumentException;
use PDO;
use PDOException;

class Database
{
    private ?PDO $connection = null;

    /**
     * Database constructor.
     * @param string $dsn
     * @param string|null $username
     * @param string|null $password
     */
    public function __construct(string $dsn, string $username = null, string $password = null)
    {
        try {
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}