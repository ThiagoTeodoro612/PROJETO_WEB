<?php
session_start();
require_once 'config/database_pdo.php';
require_once 'config/log.php';

if (isset($_SESSION['usuario_id'])) {
    registrarLog($_SESSION['usuario_id'], 'Logout realizado', null, null);
}

session_destroy();
header('Location: login.php');
exit;
?>
