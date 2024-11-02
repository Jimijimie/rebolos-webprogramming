<?php
session_start();
require_once '../classes/permission.class.php';

if (!isset($_SESSION['account']) || !$_SESSION['account']['is_admin']) {
    die(json_encode(['error' => 'Unauthorized access']));
}

$permission = new Permission();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'delete':
            $id = $_POST['id'] ?? 0;
            $result = $permission->delete($id);
            echo json_encode(['success' => $result]);
            break;
            
        case 'add':
            $permission->name = $_POST['name'];
            $permission->description = $_POST['description'];
            
            $result = $permission->add();
            echo json_encode(['success' => $result]);
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $permissions = $permission->getAll();
    echo json_encode(['data' => $permissions]);
}
