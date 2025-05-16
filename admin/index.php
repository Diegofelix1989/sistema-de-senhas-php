<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

include_once __DIR__ . '/../includes/conexao.php';

$nomeUsuario = $_SESSION['usuario']['nome'];

// Verificar se é uma requisição AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Se for uma requisição AJAX, apenas retornar o conteúdo da página solicitada
if ($isAjax && isset($_GET['page'])) {
    $page = $_GET['page'];
    $validPages = ['usuarios', 'filas', 'guiches', 'telas', 'publicidades', 'locais'];
    
    if (in_array($page, $validPages)) {
        // Incluir apenas o arquivo da página solicitada
        include_once $page . '/index.php';
        exit;
    } elseif ($page === 'dashboard') {
        // Conteúdo do dashboard
        ?>
        <div class="p-4">
            <h5>Bem-vindo, <?= htmlspecialchars($nomeUsuario) ?>!</h5>
            <p>Use o menu lateral para navegar entre as funcionalidades administrativas do sistema.</p>
        </div>
        <?php
        exit;
    } else {
        // Página não encontrada
        http_response_code(404);
        echo "<div class='alert alert-danger'>Página não encontrada</div>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Administração - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        
        /* Adicionar estilo de carregamento */
        #content-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }
        
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<!-- Loader para indicar carregamento -->
<div id="content-loader">
    <div class="loader"></div>
</div>

<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="p-3 text-white collapse show sidebar-expanded">
        <button class="btn btn-sm btn-light mb-3" onclick="toggleSidebar()">☰</button>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-page="dashboard"><i class="bi bi-house-door"></i> <span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-page="usuarios"><i class="bi bi-people"></i> <span>Usuários</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-page="filas"><i class="bi bi-list-ol"></i> <span>Filas</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-page="guiches"><i class="bi bi-window"></i> <span>Guichês</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-page="telas"><i class="bi bi-display"></i> <span>Telas</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-page="publicidades"><i class="bi bi-megaphone"></i> <span>Publicidades</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-page="locais"><i class="bi bi-pin-map-fill"></i> <span>Locais</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../public/emissao.php"><i class="bi bi-key"></i> <span>Gerar Senha</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../atendente/atendente.php"><i class="bi bi-headset"></i> <span>Painel Atendente</span></a>
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

        <!-- Área de conteúdo dinâmico -->
        <div id="main-content">
            <div class="p-4">
                <h5>Bem-vindo, <?= htmlspecialchars($nomeUsuario) ?>!</h5>
                <p>Use o menu lateral para navegar entre as funcionalidades administrativas do sistema.</p>
            </div>
        </div>
    </div>
</div>

<!-- Ícones Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Função para alternar a barra lateral
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('sidebar-collapsed');
    }
    
    // Funcionalidade para carregamento de conteúdo AJAX
    $(document).ready(function() {
        // Cache para armazenar conteúdos já carregados
        const pageCache = {};
        
        // Carregar página
        const loadPage = (page) => {
            // Mostrar loader
            $('#content-loader').css('display', 'flex');
            
            // Atualizar menu ativo
            $('.nav-link').removeClass('active');
            $(`.nav-link[data-page="${page}"]`).addClass('active');
            
            // Verificar se a página está em cache
            if (pageCache[page]) {
                $('#main-content').html(pageCache[page]);
                $('#content-loader').css('display', 'none');
                return;
            }
            
            // Carregar via AJAX
            $.ajax({
                url: 'index.php',
                type: 'GET',
                data: { page: page },
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(data) {
                    // Armazenar em cache e exibir
                    pageCache[page] = data;
                    $('#main-content').html(data);
                },
                error: function() {
                    $('#main-content').html('<div class="p-4"><div class="alert alert-danger">Erro ao carregar a página</div></div>');
                },
                complete: function() {
                    $('#content-loader').css('display', 'none');
                }
            });
        };
        
        // Manipular cliques nos links do menu
        $('.nav-link').click(function(e) {
            // Ignorar links externos (que não têm data-page)
            if (!$(this).data('page')) return;
            
            e.preventDefault();
            const page = $(this).data('page');
            
            // Carregar página sem atualizar o histórico
            loadPage(page);
        });
        
        // Carregar página inicial com base na URL
        const urlParams = new URLSearchParams(window.location.search);
        const initialPage = urlParams.get('page') || 'dashboard';
        
        // Configurar página inicial apenas se não for dashboard
        setTimeout(function() {
            if (initialPage !== 'dashboard') {
                loadPage(initialPage);
            }
        }, 500);
    });
</script>
</body>
</html>