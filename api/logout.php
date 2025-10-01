<?php
require_once __DIR__ . '/../includes/database.php';

$auth = new Auth();
$auth->logout();

header('Location: ../login.php?logout=success');
exit;
