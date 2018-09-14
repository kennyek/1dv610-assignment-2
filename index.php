<?php

session_start();

require_once 'controllers/LoginController.php';

// Display error messages.
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$loginController = new LoginController();
$loginController->handleResponse();
