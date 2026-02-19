<?php
// controllers/LinkController.php

require_once __DIR__ . '/../models/LinkModel.php';
require_once __DIR__ . '/../helpers/Response.php';

class LinkController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new LinkModel($pdo);
    }

    public function index()
    {
        $filters = [
            'supplier_name' => $_GET['supplier_name'] ?? '',
            'supplier_email' => $_GET['supplier_email'] ?? '',
            'supplier_cnpj' => $_GET['supplier_cnpj'] ?? ''
        ];

        try {
            $links = $this->model->getAll($filters);

            if (count($links) === 0) {

                return Response::success([], 'Nenhum vínculo encontrado');
            }

            return Response::success($links);

        } catch (Exception $e) {
            http_response_code(500);
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }

    public function store($data)
    {
        if (empty($data['supplier_id']) || empty($data['product_ids'])) {
            return Response::error('IDS do fornecedor e produtos são obrigatórios');
        }

        if (!is_numeric($data['supplier_id']) || $data['supplier_id'] <= 0) {
            return Response::error('ID do fornecedor inválido');
        }

        // Valida product_ids (deve ser array)
        if (!is_array($data['product_ids'])) {
            $data['product_ids'] = [$data['product_ids']];
        }

        // Prepara dados para o model
        $links = [];
        foreach ($data['product_ids'] as $productId) {
            $links[] = [
                'supplier_id' => $data['supplier_id'],
                'product_id' => $productId
            ];
        }

        try {
            $result = $this->model->create(['links' => $links]);

            if ($result['created'] > 0 && empty($result['failed'])) {
                return Response::success($result, 'Ação realizada com sucesso', );
            } else {
                http_response_code(400);
                return Response::error($result['failed']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($data)
    {
        if (empty($data['data']) || !is_array($data['data'])) {
            return Response::error('Campo "data" deve ser um array de IDs');
        }

        $ids = $data['data'];

        try {
            $result = $this->model->delete($ids);

            if ($result['deleted'] > 0) {
                return Response::success(['data' => $result], 'Ação realizada com sucesso');
            } else {
                http_response_code(404);
                return Response::error('Nenhum vínculo foi encontrado para exclusão');
            }
        } catch (Exception $e) {
            http_response_code(500);
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }
}
?>