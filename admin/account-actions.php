<?php
session_start();
require_once '../classes/account.class.php';

if (!isset($_SESSION['account']) || !$_SESSION['account']['is_admin']) {
    die(json_encode(['error' => 'Unauthorized access']));
}

$account = new Account();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'delete':
            $id = $_POST['id'] ?? 0;
            $result = $account->delete($id);
            echo json_encode(['success' => $result]);
            break;
            
        case 'add':
            $account->first_name = $_POST['first_name'];
            $account->last_name = $_POST['last_name'];
            $account->username = $_POST['username'];
            $account->password = $_POST['password'];
            $account->role = $_POST['role'];
            $account->is_staff = 1;
            $account->is_admin = ($_POST['role'] === 'admin') ? 1 : 0;
            
            $result = $account->add();
            echo json_encode(['success' => $result]);
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $accounts = $account->getAll();
    echo json_encode(['data' => $accounts]);
}
