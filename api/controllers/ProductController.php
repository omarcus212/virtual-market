<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../helpers/ValidatorProduct.php';
require_once __DIR__ . '/../helpers/Response.php';

class ProductController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new ProductModel($pdo);
    }

    public function index()
    {
        try {
            $filters = [
                'name' => $_GET['name'] ?? '',
                'code' => $_GET['code'] ?? '',
                'status' => isset($_GET['status']) ? $_GET['status'] : ''
            ];

            $products = $this->model->getAll($filters);

            if (count($products) === 0) {

                return Response::success([], 'Nenhum produto encontrado');
            }

            return Response::success($products);

        } catch (Exception $e) {
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $products = $this->model->getById($id);

            if (count($products) === 0) {

                return Response::success([], 'Nenhum produto encontrado');
            }

            return Response::success($products);

        } catch (Exception $e) {
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }

    public function store($data)
    {
        $errors = ValidatorProduct::validate($data);

        if (!empty($errors)) {
            return Response::error($errors);
        }

        try {

            $products = $this->model->create($data);

            if ($products) {
                $res = Response::success($products, 'Produto criado com sucesso');

            } else {
                $res = Response::error('Não foi possível criar o produto');
            }

            return $res;

        } catch (Exception $e) {
            return Response::error(['error' => $e->getMessage()], 500);
        }
    }

    public function update($id, $data)
    {
        $errors = ValidatorProduct::validate($data);

        if (!empty($errors)) {
            return Response::error($errors);
        }

        try {

            $products = $this->model->update($id, $data);

            if ($products) {
                $res = Response::success($products, 'Produto atualizado com sucesso');

            } else {
                $res = Response::error('Não foi possível atualizar o produto');
            }

            return $res;

        } catch (Exception $e) {
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
            $result = $this->model->delete($ids);

            if ($result['deleted'] > 0) {
                return Response::success($result, 'Produtos excluídos com sucesso', );

            } else {
                return Response::error('Não foi possível excluir os produtos');
            }

        } catch (Exception $e) {
            return Response::error(['error' => $e->getMessage()], 500);
        }

    }
}