<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
$nomeUsuario = $_SESSION['usuario']['nome'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Administração - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { overflow-x: hidden; }
        #sidebar { min-height: 100vh; background-color: #343a40; }
        #sidebar .nav-link { color: #ccc; }
        #sidebar .nav-link.active, #sidebar .nav-link:hover { color: #fff; background-color: #495057; }
        #header { background-color: #f8f9fa; }
        .sidebar-collapsed { width: 80px; }
        .sidebar-collapsed .nav-link span { display: none; }
        .sidebar-collapsed .nav-link i { margin-right: 0; }
        .admin-topbar {
            width: 100%;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1040;
        }
        .media-thumbnail {
            width: 80px;
            height: 60px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 2px;
            vertical-align: middle;
            margin-right: 5px;
        }
        .media-text-preview {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="admin-topbar">
    <span>Painel Administrativo</span>
    <div>
        <span class="me-3">Logado como: <strong><?= htmlspecialchars($nomeUsuario) ?></strong></span>
        <a href="/sistema-de-senhas-php002/logout.php" class="btn btn-danger btn-sm">Sair</a>
    </div>
</div>
<div class="d-flex"> 