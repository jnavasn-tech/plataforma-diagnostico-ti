<?php
require_once __DIR__ . '/../src/config/config.php';
$_SESSION = [];
session_destroy();
header('Location: index.php');
exit;
