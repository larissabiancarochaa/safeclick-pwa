<?php
// index.php - front controller
declare(strict_types=1);

session_start();

require_once __DIR__ . '/app/controllers/LinkController.php';

// roteamento simples via ?action=...
$action = $_GET['action'] ?? 'home';

$ctrl = new LinkController();

switch ($action) {
    case 'api':
        // endpoints AJAX/Fetch (POST/GET)
        $method = $_SERVER['REQUEST_METHOD'];
        header('Content-Type: application/json; charset=utf-8');

        if ($method === 'GET') {
            echo json_encode($ctrl->api_list());
            exit;
        }

        // POST bodies
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $op = $data['op'] ?? ($data['action'] ?? null);
        if ($op === 'create') {
            echo json_encode($ctrl->api_create($data));
            exit;
        }
        if ($op === 'update') {
            echo json_encode($ctrl->api_update($data));
            exit;
        }
        if ($op === 'delete') {
            echo json_encode($ctrl->api_delete($data));
            exit;
        }
        if ($op === 'analyze') {
            echo json_encode($ctrl->api_analyze($data));
            exit;
        }
        // default
        echo json_encode(['error' => 'Operação inválida']);
        exit;
        break;

    case 'home':
    default:
        // render view
        require_once __DIR__ . '/app/views/home.php';
        break;
}