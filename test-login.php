<?php
require_once '../tools/functions.php';
require_once '../classes/account.class.php';

$accountObj = new Account();

// Test credentials
$username = 'admin';
$password = 'admin';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test the login method
$result = $accountObj->login($username, $password);
var_dump($result);

// Test the fetch method
$data = $accountObj->fetch($username);
var_dump($data);