<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // 1 para debug, 0 para produção

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../includes/connection.php';

//  Parse da URL manual
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/api/', '', $uri);
$uri = str_replace('/index.php', '', $uri);
$route = trim($uri, '/');

// Divide a rota em partes (ex: products/1 → ['products', '1'])
$parts = explode('/', $route);
$resource = $parts[0] ?? '';
$id = $parts[1] ?? null;

$method = $_SERVER['REQUEST_METHOD'];

// Roteamento
switch ($resource) {
    case 'products':
        require_once __DIR__ . '/controllers/ProductController.php';
        $controller = new ProductController($pdo);

        switch ($method) {
            case 'GET':
                echo $id ? $controller->show($id) : $controller->index();
                break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                echo $controller->store($data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                echo $controller->update($id, $data);
                break;
            case 'DELETE':
                $data = json_decode(file_get_contents('php://input'), true);
                echo $controller->destroy($data);
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Método não permitido']);
        }
        break;

    case 'suppliers':
        require_once __DIR__ . '/controllers/SupplierController.php';
        $controller = new SupplierController($pdo);

        switch ($method) {
            case 'GET':
                echo $id ? $controller->show($id) : $controller->index();
                break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                echo $controller->store($data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                echo $controller->update($id, $data);
                break;
            case 'DELETE':
                $data = json_decode(file_get_contents('php://input'), true);
                echo $controller->destroy($data);
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Método não permitido']);
        }
        break;

    case 'links':
        require_once __DIR__ . '/controllers/LinkController.php';
        $controller = new LinkController($pdo);

        switch ($method) {
            case 'GET':
                echo $controller->index();
                break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                echo $controller->store($data);
                break;
            case 'DELETE':
                $data = json_decode(file_get_contents('php://input'), true);
                echo $controller->destroy($data);
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Método não permitido']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Recurso não encontrado']);
}
?>