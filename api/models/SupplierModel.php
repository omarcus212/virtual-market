<?php

class SupplierModel
{
    private $pdo;
    private $table = 'suppliers';
    private $view = 'view_suppliers';
    private $relation_table = 'product_supplier';

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

        if (!empty($filters['cnpj'])) {
            $sql .= " AND cnpj LIKE :cnpj";
            $params[':cnpj'] = '%' . $filters['cnpj'] . '%';
        }

        if (!empty($filters['email'])) {
            $sql .= " AND email LIKE :email";
            $params[':email'] = '%' . $filters['email'] . '%';
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = (int) $filters['status'];
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            throw new Exception('Erro ao listar fornecedores: ' . $e->getMessage());
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
            throw new Exception('Erro ao listar fornecedor: ' . $e->getMessage());
        }

    }

    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (name, cnpj, email, phone, status) 
                VALUES (:name, :cnpj, :email, :phone, :status)";

        $params = [
            ':name' => $data['name'],
            ':cnpj' => $data['cnpj'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':status' => $data['status'] ?? 1
        ];

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            throw new Exception('Erro ao criar fornecedor: ' . $e->getMessage());
        }
    }


    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET name = :name, 
                    cnpj = :cnpj, 
                    email = :email, 
                    phone = :phone, 
                    status = :status 
                WHERE id = :id";

        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':cnpj' => $data['cnpj'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
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

    public function checkDuplicates(string $cnpj, string $email, ?int $excludeId = null)
    {
        $result = [
            'cnpj_exists' => false,
            'email_exists' => false
        ];

        // Verifica CNPJ
        if ($cnpj !== null && trim($cnpj) !== '') {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE cnpj = :cnpj";
            $params = [':cnpj' => $cnpj];

            if ($excludeId) {
                $sql .= " AND id != :excludeId";
                $params[':excludeId'] = $excludeId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch();
            $result['cnpj_exists'] = $row['count'] > 0;
        }

        // Verifica E-mail
        if ($email !== null && trim($email) !== '') {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
            $params = [':email' => $email];

            if ($excludeId) {
                $sql .= " AND id != :excludeId";
                $params[':excludeId'] = $excludeId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch();
            $result['email_exists'] = $row['count'] > 0;
        }

        return $result;
    }

    public function delete($ids)
    {
        try {
            $this->pdo->beginTransaction();

            foreach ($ids as $id) {
                $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM {$this->relation_table}  WHERE supplier_id = :id
            ");
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch();

                if ($row['count'] > 0) {

                    $this->pdo->rollBack();
                    return ['deleted' => 0];

                }
            }

            $deleted = 0;
            foreach ($ids as $id) {

                $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
                $stmt->execute([':id' => $id]);

                if ($stmt->rowCount() > 0) {
                    $deleted++;
                }
            }

            $this->pdo->commit();
            return ['deleted' => $deleted];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception('Erro ao excluir fornecedor: ' . $e->getMessage());
        }
    }

}