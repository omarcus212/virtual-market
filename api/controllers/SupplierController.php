<?php
require_once __DIR__ . '/../models/SupplierModel.php';
require_once __DIR__ . '/../helpers/ValidatorSupplier.php';
require_once __DIR__ . '/../helpers/Response.php';

class SupplierController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new SupplierModel($pdo);
    }

    public function index()
    {
        try {
            $filters = [
                'name' => $_GET['name'] ?? '',
                'cnpj' => $_GET['cnpj'] ?? '',
                'email' => $_GET['email'] ?? '',
                'status' => isset($_GET['status']) ? $_GET['status'] : ''
            ];

            $suppliers = $this->model->getAll($filters);

            if (count($suppliers) === 0) {

                return Response::success([], 'Nenhum fornecedor encontrado');
            }

            return Response::success($suppliers);

        } catch (Exception $e) {
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $suppliers = $this->model->getById($id);

            if (count($suppliers) === 0) {

                return Response::success([], 'Nenhum fornecedor encontrado');
            }

            return Response::success($suppliers);

        } catch (Exception $e) {
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }

    public function store($data)
    {
        $errors = ValidatorSupplier::validate($data);

        if (!empty($errors)) {
            return Response::error($errors);
        }

        // Verifica duplicidade de CNPJ e e-mail
        $duplicates = $this->model->checkDuplicates(
            $data['cnpj'],
            $data['email']
        );

        if ($duplicates['cnpj_exists']) {
            return Response::error('CNPJ já cadastrado');
        }

        if ($duplicates['email_exists']) {
            return Response::error('E-mail já cadastrado');
        }

        try {
            $suppliers = $this->model->create($data);
            return Response::success(['id' => $suppliers], 'Fornecedor cadastrado com sucesso');

        } catch (Exception $e) {
            http_response_code(500);
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }

    public function update($id, $data)
    {
        $errors = ValidatorSupplier::validate($data);

        if (!empty($errors)) {
            return Response::error($errors);
        }

        $duplicates = $this->model->checkDuplicates(
            $data['cnpj'],
            $data['email'],
            $id
        );

        if ($duplicates['cnpj_exists']) {
            return Response::error('CNPJ já cadastrado');
        }

        if ($duplicates['email_exists']) {
            return Response::error('E-mail já cadastrado');
        }

        try {
            $suppliers = $this->model->update($id, $data);
            return Response::success(['id' => $suppliers], 'Fornecedor atualizado com sucesso');

        } catch (Exception $e) {
            http_response_code(500);
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($data)
    {
        if (!isset($data)) {
            return Response::error('Nenhum ID informado');
        }

        $ids = $data['data'];

        try {
            $suppliers = $this->model->delete($ids);

            if ($suppliers['deleted'] > 0) {
                return Response::success($suppliers, 'Produtos excluídos com sucesso', );
            } else {
                return Response::error('Não foi possível excluir os produtos');
            }

        } catch (Exception $e) {
            http_response_code(500);
            return Response::error(['error' => $e->getMessage()], 500);
        }

    }
}