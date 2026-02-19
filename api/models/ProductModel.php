<?php

class ProductModel
{
    private $pdo;
    private $table = 'products';
    private $view = 'view_products';
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(array $filters = [])
    {
        $sql = "SELECT * FROM {$this->view} WHERE 1=1";
        $params = [];

        if (!empty($filters['name'])) {
            $sql .= " AND name LIKE :name";
            $params[':name'] = '%' . $filters['name'] . '%';
        }

        if (!empty($filters['code'])) {
            $sql .= " AND code = :code";
            $params[':code'] = $filters['code'];
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            throw new Exception('Erro ao listar produtos: ' . $e->getMessage());
        }
    }

    public function getById($id)
    {

        $sql = "SELECT * FROM {$this->view} WHERE id = :id";
        $params[':id'] = $id;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();


        } catch (PDOException $e) {
            throw new Exception('Erro ao listar produtos: ' . $e->getMessage());
        }

    }

    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (name, description, code, status) 
                VALUES (:name, :description, :code, :status)";

        $params = [
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':code' => $data['code'] ?? '',
            ':status' => $data['status'] ?? 1
        ];

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            throw new Exception('Erro ao criar produto: ' . $e->getMessage());
        }
    }


    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET name = :name, 
                    description = :description, 
                    code = :code, 
                    status = :status 
                WHERE id = :id";

        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':code' => $data['code'],
            ':status' => $data['status'] ?? 1
        ];

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();

        } catch (PDOException $e) {
            throw new Exception('Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    public function delete($ids)
    {
        $deleted = 0;
        foreach ($ids as $id) {

            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() > 0) {
                $deleted++;
            }
        }

        return ['deleted' => $deleted];

    }

}