<?php
session_start();
require_once '../classes/role.class.php';

if (!isset($_SESSION['account']) || !$_SESSION['account']['is_admin']) {
    die(json_encode(['error' => 'Unauthorized access']));
}

$role = new Role();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'delete':
            $id = $_POST['id'] ?? 0;
            $result = $role->delete($id);
            echo json_encode(['success' => $result]);
            break;
            
        case 'add':
            $role->name = $_POST['name'];
            $role->description = $_POST['description'];
            $result = $role->add();
            echo json_encode(['success' => $result]);
            break;

        case 'assign_permissions':
            $roleId = $_POST['role_id'] ?? 0;
            $permissionIds = $_POST['permission_ids'] ?? [];
            $result = $role->assignPermissions($roleId, $permissionIds);
            echo json_encode(['success' => $result]);
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $roles = $role->getAll();
    echo json_encode(['data' => $roles]);
}
