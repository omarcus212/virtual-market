<?php
// models/LinkModel.php

class LinkModel
{
    private $pdo;
    private $table = 'product_supplier';
    private $view = 'view_product_suppliers';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(array $filters = [])
    {

        $sql = "SELECT * FROM {$this->view} WHERE 1=1";
        $params = [];

        // Filtro por Nome (Usa LIKE e trim para evitar espaços vazios)
        if (!empty($filters['supplier_name'])) {
            $name = trim($filters['supplier_name']);
            if ($name !== '') {
                $sql .= " AND supplier_name LIKE :supplier_name";
                $params[':supplier_name'] = '%' . $name . '%';
            }
        }

        // Filtro por Email
        if (!empty($filters['supplier_email'])) {
            $email = trim($filters['supplier_email']);
            if ($email !== '') {
                $sql .= " AND supplier_email = :supplier_email";
                $params[':supplier_email'] = $email;
            }
        }

        // Filtro por CNPJ
        if (!empty($filters['supplier_cnpj'])) {
            $cnpj = trim($filters['supplier_cnpj']);
            if ($cnpj !== '') {
                $sql .= " AND supplier_cnpj = :supplier_cnpj";
                $params[':supplier_cnpj'] = $cnpj;
            }
        }

        // REMOVIDO O var_dump E O RETURN QUE BLOQUEAVAM O CÓDIGO
        // var_dump($sql); 
        // return; 

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // Recomendo especificar o fetch mode

            // Se não houver resultados, retorna array vazio em vez de null
            if (!$rows) {
                return [];
            }

            return $this->groupedBySupplier($rows);

        } catch (PDOException $e) {
            // Log do erro para debug interno (não mostre o erro bruto para o usuário final em produção)
            error_log('Erro ao listar vínculos: ' . $e->getMessage());
            throw new Exception('Erro ao listar vínculos.');
        }
    }

    private function groupedBySupplier(array $rows)
    {
        $grouped = [];

        foreach ($rows as $row) {
            $supplierId = $row['supplier_id'];

            if (!isset($grouped[$supplierId])) {
                $grouped[$supplierId] = [
                    'relation_id' => $row['relation_id'],
                    'supplier_id' => $row['supplier_id'],
                    'supplier_name' => $row['supplier_name'],
                    'supplier_cnpj' => $row['supplier_cnpj'],
                    'supplier_email' => $row['supplier_email'] ?? '',
                    'supplier_phone' => $row['supplier_phone'] ?? '',
                    'supplier_status' => $row['supplier_status'],
                    'created_at' => $row['linked_at'],
                    'products' => []
                ];
            }

            $grouped[$supplierId]['products'][] = [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'product_code' => $row['product_code'] ?? '',
                'product_status' => $row['product_status'],
            ];
        }

        // Retorna array numerado (remove chaves do supplier_id)
        return array_values($grouped);
    }

    public function create(array $data)
    {
        $created = 0;
        $failed = [];

        $links = $data['links'] ?? [];

        foreach ($links as $link) {
            $productId = $link['product_id'] ?? null;
            $supplierId = $link['supplier_id'] ?? null;

            $supplierActive = $this->isSupplierActive($supplierId);

            if (!$supplierActive) {
                $failed[] = [
                    'product_id' => $productId,
                    'supplier_id' => $supplierId,
                    'error' => 'Fornecedor está inativo'
                ];
                continue;
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->table} (product_id, supplier_id) 
                VALUES (:product_id, :supplier_id)
            ");
            $stmt->execute([
                ':product_id' => (int) $productId,
                ':supplier_id' => (int) $supplierId
            ]);

            $created++;
        }

        return [
            'created' => $created,
            'failed' => $failed
        ];
    }


    public function delete(array $ids)
    {
        $deleted = 0;
        $failed = [];

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        foreach ($ids as $id) {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE supplier_id = :id");
            $stmt->execute([':id' => (int) $id]);

            if ($stmt->rowCount() > 0) {
                $deleted++;
            } else {
                $failed[] = ['id' => $id, 'error' => 'Vínculo não encontrado'];
            }
        }

        return [
            'deleted' => $deleted,
            'failed' => $failed
        ];
    }

    private function isSupplierActive(int $supplierId)
    {
        $stmt = $this->pdo->prepare("SELECT status FROM suppliers WHERE id = :id");
        $stmt->execute([':id' => $supplierId]);
        $row = $stmt->fetch();
        return $row && $row['status'] == 1;
    }
}
?>