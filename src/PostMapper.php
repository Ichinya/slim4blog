<?php

namespace Blog;

use Exception;

class PostMapper
{

    private Database $database;

    /**
     * PostMapper constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param string $slug
     * @return array|null
     */
    public function getBySlug(string $slug): ?array
    {
        $statement = $this->database->getConnection()->prepare("SELECT * FROM post WHERE slug = :slug");
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
    public function getList(int $page = 1, int $limit = 2, string $direction = 'ASC'): ?array
    {
        if (!in_array($direction, ['DESC', 'ASC'])) {
            throw new Exception('Кривое направление');
        }
        $start = ($page - 1) * $limit;
        $statement = $this->database->getConnection()->prepare(
            "SELECT * FROM post 
                ORDER BY published_date {$direction} 
                LIMIT {$start},{$limit}; ");
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getTotalCount(): int
    {
        $statement = $this->database->getConnection()->prepare("SELECT COUNT(id) AS total FROM post");
        $statement->execute();
        return (int)($statement->fetchColumn() ?? 0);
    }
}