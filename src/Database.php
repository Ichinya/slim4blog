<?php

namespace Blog;

use PDO;

class Database
{
    private ?PDO $connection = null;

    /**
     * Database constructor.
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}