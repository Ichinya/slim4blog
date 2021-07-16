<?php

namespace Blog;

use PDO;

class PostMapper
{

    private PDO $connection;

    /**
     * PostMapper constructor.
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $slug
     * @return array|null
     */
    public function getBySlug(string $slug): ?array
    {
        $statement = $this->connection->prepare("SELECT * FROM post WHERE slug = :slug");
        $statement->execute(compact('slug'));
        $result = $statement->fetchAll();
        return array_shift($result);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string $direction
     * @return array|null
     * @throws Exception
     */
    public function getList(int $page = 1, int $limit = 2, $direction = 'ASC'): ?array
    {
        if (!in_array($direction, ['DESC', 'ASC'])) {
            throw new \Exception('Кривое направление');
        }
        $start = ($page - 1) * $limit;
        $statement = $this->connection->prepare(
            "SELECT * FROM post 
                ORDER BY published_date {$direction} 
                LIMIT {$start},{$limit}; ");
        $statement->execute();
        return $statement->fetchAll();
    }
}