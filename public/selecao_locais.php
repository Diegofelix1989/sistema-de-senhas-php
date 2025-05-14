<?php
session_start();
include_once __DIR__ . '/../includes/conexao.php';

// Buscar todos os locais disponíveis
$sql = "SELECT * FROM locais ORDER BY nome";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$locais = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar a sessão de filtros se não existir
if (!isset($_SESSION['filtro_locais'])) {
    $_SESSION['filtro_locais'] = [];
    foreach ($locais as $local) {
        $_SESSION['filtro_locais'][$local['id']] = true; // Por padrão, todos os locais estão selecionados
    }
}

// Processar ações de filtro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['aplicar_filtro'])) {
        // Resetar todos os filtros primeiro
        foreach ($_SESSION['filtro_locais'] as $key => $value) {
            $_SESSION['filtro_locais'][$key] = false;
        }
        
        // Ativar apenas os locais selecionados
        if (isset($_POST['locais_selecionados']) && is_array($_POST['locais_selecionados'])) {
            foreach ($_POST['locais_selecionados'] as $localId) {
                $_SESSION['filtro_locais'][$localId] = true;
            }
        }
        
        // Redirecionar para a página de emissão se pelo menos um local foi selecionado
        if (isset($_POST['locais_selecionados']) && count($_POST['locais_selecionados']) > 0) {
            $_SESSION['local_selecionado'] = (int)$_POST['locais_selecionados'][0]; // Define o primeiro local como selecionado
            header('Location: emissao.php');
            exit;
        }
    } else if (isset($_POST['resetar_filtro'])) {
        // Resetar todos os filtros (marcar todos)
        foreach ($_SESSION['filtro_locais'] as $key => $value) {
            $_SESSION['filtro_locais'][$key] = true;
        }
    } else if (isset($_POST['local_id'])) {
        // Quando um local específico é selecionado para emissão de senha
        $_SESSION['local_selecionado'] = (int)$_POST['local_id'];
        header('Location: emissao.php');
        exit;
    }
}

// Filtrar locais visíveis baseado nas configurações de filtro
$locaisVisiveis = array_filter($locais, function($local) {
    return isset($_SESSION['filtro_locais'][$local['id']]) && $_SESSION['filtro_locais'][$local['id']] === true;
});
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecionar Local - Sistema de Gerenciamento de Filas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-container {
            flex: 1;
            padding: 20px 0;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 1rem;
        }
        .form-check {
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.2s;
            margin-bottom: 5px;
        }
        .form-check:hover {
            background-color: #f0f0f0;
        }
        .form-check-input {
            cursor: pointer;
        }
        .form-check-label {
            cursor: pointer;
            width: 100%;
            font-weight: 500;
            margin-left: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .filter-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .filter-footer {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            margin-top: 15px;
        }
        .local-count {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .local-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-left: 25px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h1><i class="bi bi-building"></i> Seleção de Local</h1>
        <p class="lead">Selecione os locais para emissão de senhas</p>
    </div>

    <div class="container main-container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="local-count text-center">
                    Exibindo <?= count($locais) ?> locais disponíveis
                </div>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-check-square"></i> Selecione os Locais</h5>
                    </div>
                    <div class="card-body">
                        <div class="filter-header d-flex justify-content-between align-items-center">
                            <span>Escolha os locais para emissão de senhas:</span>
                            <button type="button" id="toggleAll" class="btn btn-sm btn-outline-primary">Marcar/Desmarcar Todos</button>
                        </div>
                        
                        <form method="POST" id="formLocais">
                            <?php if (empty($locais)): ?>
                                <div class="alert alert-warning text-center" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Nenhum local disponível no sistema.
                                </div>
                            <?php else: ?>
                                <?php foreach ($locais as $local): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="locais_selecionados[]" 
                                            value="<?= $local['id'] ?>" id="local<?= $local['id'] ?>"
                                            <?= (!isset($_SESSION['filtro_locais'][$local['id']]) || $_SESSION['filtro_locais'][$local['id']]) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="local<?= $local['id'] ?>">
                                            <span><?= htmlspecialchars($local['nome']) ?></span>
                                            <?php if (!empty($local['descricao'])): ?>
                                                <span class="badge bg-light text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= htmlspecialchars($local['descricao']) ?>">
                                                    <i class="bi bi-info-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                        </label>
                                        <?php if (!empty($local['descricao'])): ?>
                                            <span class="local-description"><?= htmlspecialchars($local['descricao']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="filter-footer d-flex justify-content-between align-items-center">
                                    <button type="submit" name="resetar_filtro" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-counterclockwise"></i> Resetar
                                    </button>
                                    <button type="submit" name="aplicar_filtro" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Aplicar e Avançar
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="bi bi-house"></i> Voltar ao Menu Principal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Funcionalidade de marcar/desmarcar todos
            const toggleAllBtn = document.getElementById('toggleAll');
            if (toggleAllBtn) {
                toggleAllBtn.addEventListener('click', function() {
                    const checkboxes = document.querySelectorAll('input[name="locais_selecionados[]"]');
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !allChecked;
                    });
                });
            }
            
            // Verificar se pelo menos um local está selecionado antes de enviar o formulário
            const formLocais = document.getElementById('formLocais');
            if (formLocais) {
                formLocais.addEventListener('submit', function(event) {
                    if (document.querySelector('button[name="aplicar_filtro"]').clicked) {
                        const checkboxes = document.querySelectorAll('input[name="locais_selecionados[]"]:checked');
                        if (checkboxes.length === 0) {
                            event.preventDefault();
                            alert('Por favor, selecione pelo menos um local antes de continuar.');
                        }
                    }
                });
                
                // Adicionar propriedade 'clicked' nos botões
                document.querySelectorAll('button[type="submit"]').forEach(function(button) {
                    button.addEventListener('click', function() {
                        this.clicked = true;
                    });
                });
            }
        });
    </script>
</body>
</html>