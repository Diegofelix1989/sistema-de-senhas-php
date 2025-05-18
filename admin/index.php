<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

include_once __DIR__ . '/../includes/conexao.php';

$nomeUsuario = $_SESSION['usuario']['nome'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Administração - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }

        #sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }

        #sidebar .nav-link {
            color: #ccc;
        }

        #sidebar .nav-link.active,
        #sidebar .nav-link:hover {
            color: #fff;
            background-color: #495057;
        }

        #header {
            background-color: #f8f9fa;
        }

        .sidebar-collapsed {
            width: 80px;
        }

        .sidebar-collapsed .nav-link span {
            display: none;
        }

        .sidebar-collapsed .nav-link i {
            margin-right: 0;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="p-3 text-white collapse show sidebar-expanded">
        <button class="btn btn-sm btn-light mb-3" onclick="toggleSidebar()">☰</button>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="index.php"><i class="bi bi-house-door"></i> <span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="usuarios"><i class="bi bi-people"></i> <span>Usuários</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="filas"><i class="bi bi-list-ol"></i> <span>Filas</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="guiches"><i class="bi bi-window"></i> <span>Guichês</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="telas"><i class="bi bi-display"></i> <span>Telas</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="publicidades"><i class="bi bi-megaphone"></i> <span>Publicidades</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="locais"><i class="bi bi-pin-map-fill"></i> <span>Locais</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../public/emissao.php"><i class="bi bi-key"></i> <span>Gerar Senha</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../atendente/atendente.php"><i class="bi bi-headset"></i> <span>Painel Atendente</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../public/exibicao.php"><i class="bi bi-headset"></i> <span>Painel/senha/publicidade</span></a>
            </li>
            
        </ul>
    </div>

    <!-- Conteúdo Principal -->
    <div class="flex-grow-1">
        <div id="header" class="p-3 d-flex justify-content-between align-items-center border-bottom">
            <h4 class="mb-0">Painel Administrativo</h4>
            <div>
                <span class="me-3">Logado como: <strong><?= htmlspecialchars($nomeUsuario) ?></strong></span>
                <a href="../logout.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>

        <div class="p-4">
            <h5>Bem-vindo, <?= htmlspecialchars($nomeUsuario) ?>!</h5>
            <p>Use o menu lateral para navegar entre as funcionalidades administrativas do sistema.</p>
        </div>
    </div>
</div>

<!-- Ícones Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('sidebar-collapsed');
    }
</script>
</body>
</html>
