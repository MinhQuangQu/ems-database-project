<?php

class Model
{
    protected PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    protected function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }

        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    protected function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }

        return $stmt->execute();
    }

    protected function lastInsertId(): string
    {
        return $this->conn->lastInsertId();
    }
}
