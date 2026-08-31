<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

echo '
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Mundo - Gerenciamento Geográfico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/crud-mundo/index.php">
                <i class="fas fa-globe-americas me-2"></i>CRUD Mundo
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/crud-mundo/pages/paises/index.php">
                            <i class="fas fa-flag"></i> Países
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/crud-mundo/pages/cidades/index.php">
                            <i class="fas fa-city"></i> Cidades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/crud-mundo/pages/continentes/index.php">
                            <i class="fas fa-globe"></i> Continentes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/crud-mundo/pages/governantes/index.php">
                            <i class="fas fa-user-tie"></i> Governantes
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> ' . htmlspecialchars($_SESSION['usuario_nome']) . '
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/crud-mundo/pages/logs/index.php">
                                <i class="fas fa-history me-2"></i>Logs do Sistema
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/crud-mundo/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
';
?>
