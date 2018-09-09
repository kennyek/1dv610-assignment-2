<?php

require_once 'view/LoginView.php';
require_once 'view/DateTimeView.php';
require_once 'view/LayoutView.php';

// Display error messages.
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$loginView = new LoginView();
$dateTimeView = new DateTimeView();
$layoutView = new LayoutView();

$layoutView->render($loginView->checkIfLoggedIn(), $loginView, $dateTimeView);
